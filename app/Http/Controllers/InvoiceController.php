<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesAjaxLists;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use App\Models\Settings;
use App\Support\SafeDownload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;


class InvoiceController extends Controller
{
    use PaginatesAjaxLists;

    public function invoiceList(Request $request){

        $page = $this->listPage($request);
        $query = Invoice::query()->with('customer')->orderBy('id', 'desc');

        if ($page['search'] !== '') {
            $search = $page['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customer) use ($search) {
                        $customer->where('customer_name', 'like', "%{$search}%");
                    });
            });
        }

        $totalRecords = (clone $query)->count();
        $totalpagenums = $this->pageCount($totalRecords, $page['perPage']);

        $invoices = $query->skip($page['skip'])->take($page['perPage'])->get();
        $view = $page['isFragment'] ? 'pages.invoice.single' : 'pages.invoice.list';

        return view($view, compact('invoices', 'totalpagenums', 'totalRecords'));
    }

    public function showGenerateForm(){
        $customers = Customer::get();
        $invoiceCount = $this->nextInvoiceSequence();
        $settings = Settings::get()->first();
        return view('pages.generate-invoice.form',compact('customers','invoiceCount','settings'));
    }

    public function addSingleProduct(Request $request){
        $data = Product::with('stock')->get();

        // Filter out products with no stock or out of stock
        $products = $data->filter(function ($product) {
            return isset($product['stock']) && $product['stock']['available_stock'] > 0;
        });        
        
        $index = $request->input('productCount');
        return view('pages.generate-invoice.add-product',compact('products','index'));
    }

    public function generateInvoice(Request $request){

        $data = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'invoice_number' => 'required|string',
            'invoice_date' => 'required|string',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer|exists:products,id',
            'product_qty' => 'required|array',
            'product_qty.*' => 'required|numeric|min:0.01',
            'product_mrp' => 'required|array',
            'product_rate' => 'required|array',
            'product_gross_total' => 'required|array',
            'product_discount' => 'required|array',
            'product_discount_amt' => 'required|array',
            'product_taxable_value' => 'required|array',
            'product_tax_rate' => 'required|array',
            'product_cgst_perc' => 'required|array',
            'product_cgst' => 'required|array',
            'product_sgst_perc' => 'required|array',
            'product_sgst' => 'required|array',
            'product_total_amt' => 'required|array',
            'total_round_off' => 'nullable',
            'total_quantity' => 'nullable',
            'total_rate' => 'nullable',
            'total_discount' => 'nullable',
            'total_discount_amt' => 'nullable',
            'total_taxable_value' => 'nullable',
            'total_gross_sum' => 'nullable',
            'total_cgst' => 'nullable',
            'total_cgst_perc' => 'nullable',
            'total_sgst' => 'nullable',
            'total_sgst_perc' => 'nullable',
            'total_grand' => 'required|numeric',
        ]);

        $productModels = Product::query()
            ->whereIn('id', collect($data['product_id'])->unique()->all())
            ->get()
            ->keyBy('id');

        $products = collect($data['product_id'])->map(function ($id, $index) use ($data, $productModels) {
            $product = $productModels->get($id);
            if (! $product) {
                throw ValidationException::withMessages(['product_id' => 'Unknown product.']);
            }

            return [
                'product_id' => $id,
                'product_description' => $product->product_description,
                'hsn_code' => $product->hsn_code,
                'quantity' => $data['product_qty'][$index],
                'mrp' => $data['product_mrp'][$index],
                'rate' => $data['product_rate'][$index],
                'gross_total' => $data['product_gross_total'][$index],
                'discount' => $data['product_discount'][$index],
                'discount_amt' => $data['product_discount_amt'][$index],
                'taxable_value' => $data['product_taxable_value'][$index],
                'tax_rate' => $data['product_tax_rate'][$index],
                'cgst_perc' => $data['product_cgst_perc'][$index],
                'cgst' => $data['product_cgst'][$index],
                'sgst_perc' => $data['product_sgst_perc'][$index],
                'sgst' => $data['product_sgst'][$index],
                'total_amount' => $data['product_total_amt'][$index],
            ];
        })->toArray();

        $invoice = [
            'customer_id' => $data['customer_id'],
            'invoice_number' => $data['invoice_number'],
            'invoice_date' => $this->parseInvoiceDate($data['invoice_date']),
            'products' => $products,
            'total_round_off' => $data['total_round_off'] ?? 0,
            'total_quantity' => $data['total_quantity'] ?? 0,
            'total_mrp' => $data['total_rate'] ?? 0,
            'total_discount' => $data['total_discount'] ?? 0,
            'total_discount_amt' => $data['total_discount_amt'] ?? 0,
            'total_taxable_value' => $data['total_taxable_value'] ?? 0,
            'total_gross_sum' => $data['total_gross_sum'] ?? 0,
            'total_cgst' => $data['total_cgst'] ?? 0,
            'total_cgst_perc' => $data['total_cgst_perc'] ?? 0,
            'total_sgst' => $data['total_sgst'] ?? 0,
            'total_sgst_perc' => $data['total_sgst_perc'] ?? 0,
            'total_grand' => $data['total_grand'],
        ];

        try {
            $insinvoice = $this->persistInvoiceAndStock($invoice);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $customer = Customer::where('id', $data['customer_id'])->first();
        $settings = Settings::get()->first();
        $custhtml = $this->customerHtml($customer);
        $filename = $this->writeInvoicePdf($invoice, $customer, $settings, $custhtml, $insinvoice->invoice_number);

        return response()->json([
            'zipUrl' => route('downloadZip', ['file' => $filename])
        ]);
    }

    public function downloadZip($file)
    {
        $path = SafeDownload::path(storage_path('app/public'), $file, ['invoices-']);

        return response()->download($path)->deleteFileAfterSend(true);
    }    

    public function getProductInfo(Request $request){
        $id = $request->input('id');
        $product = Product::with('stock')->where('id',$id)->get()->first();
        $settings = Settings::get()->first();

        return response()->json([
            'success'=>true,
            'data'=>$product,
            'profit'=>$settings->profit_calc
        ]); 
    }

    private function parseCsv($file)
    {
        $csvData  = [];
        $filePath = $file->getRealPath();
        $file     = fopen($filePath, 'r');

        // Assuming the first row contains headers
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $csvData[] = array_combine($header, $row); // Combine headers with data
        }

        fclose($file);

        return $csvData;
    }       
    
    public function prepareCsv($collection){
        $newc = [];
        foreach ($collection as $mainKey => $items) {
            $customer_id = $items[0]['Customer ID'];
            $invoice_date = $items[0]['Invoice Date'];

            $newc[$mainKey]['customer_id']=$customer_id;
            $newc[$mainKey]['invoice_date']=$invoice_date;
            $newc[$mainKey]['total_round_off']=$items[0]['Total Round Off'];
            $newc[$mainKey]['products']=$items;


            foreach ($items as $index => $item) {
                $grouped = [];

                $grouped[$index] = [
                    'profit' => [],
                    'rate_discount_percentage'=>[],
                    'discount_percentage' => [],
                    'discount_amount' => [],
                    'total_discount_percentage' => [],
                    'total_discount_amount' => [],
                    'total_round_off' => [],

                ];
        
                foreach ($item as $field => $value) {
                    if (stripos($field, 'Profit') === 0) {
                        $grouped[$index]['profit'][$field] = $value;
                    } elseif (preg_match('/^Discount \d+ %$/', $field)) {
                        $grouped[$index]['discount_percentage'][$field] = $value;

                    } 
                    elseif (preg_match('/^Rate Discount \d+ %$/', $field)) {
                        // $grouped[$index]['total_discount_percentage'][$field] = $value;
                        $grouped[$index]['rate_discount_percentage'][$field] = $value;

                    }
                    elseif (preg_match('/^Total Discount \d+ %$/', $field)) {
                        // $grouped[$index]['total_discount_percentage'][$field] = $value;
                        $grouped[$index]['total_discount_percentage'][$field] = $value;

                    }
                    elseif (preg_match('/^Total Discount \d+ Amount$/', $field)) {
                        // $grouped[$index]['total_discount_amount'][$field] = $value;
                        $grouped[$index]['total_discount_amount'][$field] = $value;

                    }                    
                    elseif (preg_match('/^Total Round Off$/', $field)) {
                        // $grouped[$index]['total_discount_amount'][$field] = $value;
                        $grouped[$index]['total_round_off'][$field] = $value;

                    }                                        
                     elseif (preg_match('/^Discount \d+ Amount$/', $field)) {
                        $grouped[$index]['discount_amount'][$field] = $value;
                    }
                }

                $newc[$mainKey]['products'][$index]['results'] = $grouped;
                // $collection[$mainKey][$index]['products'] = $grouped;
            }


        }
        return $newc;
    }

    public function calculateInvoice($newc){
        $finalArray=[];
        $totaldcalc = 0;
        $totaldacalc = 0;
        $totaltaxableamount = 0;
        $totalgrosssum = 0;
        $totalquantity = 0;
        $totalmrp=0;
        $total_single_gst_rate = 0;
        $total_single_gst_perc = 0;
        $total_grand = 0;
        $total_disc_perc = 0;
        $total_disc_perc_value = 0;
        $total_disc_amount = 0;
        $total_gst_deduction = 0;
        $total_gst = 0;
        $total_round_off = 0;


        foreach($newc as $index => $c){
            $finalArray['customer_id']=(int)$c['customer_id'];
            $finalArray['total_round_off']=!empty($c['total_round_off'])?floatval($c['total_round_off']):0;
            $finalArray['invoice_number']=$index;
            $finalArray['invoice_date']=$c['invoice_date'];

            foreach($c['products'] as $p){
                $product = Product::where('id',$p['Product ID'])->get()->first();

                $mrp = $rate = floatval($p['MRP']);
                $totalmrp+=$mrp;
                $gross_value=0;
                $taxable_value=0;
                $total_with_gst=0;

                $first_key = array_key_first($p['results']);


                // Rate Calculation Start

                $totalquantity += floatval($p['Quantity']);

                // Profit Calculation
                $totalpcalc = 0;
                $pcalc = $p['results'][$first_key]['profit'];
                foreach($pcalc as $d => $v){
                    if(!empty($v)){
                        $prft = floatval($v);
                        $rate /= $prft;

                        $totalpcalc += floatval($v);    
                    }
                }

                // Rate Discount Calculation
                $totalratediscountcalc = 0;
                $ratediscounts = $p['results'][$first_key]['rate_discount_percentage'];
                foreach($ratediscounts as $d => $v){
                    if(!empty($v)){
                        $disc =  floatval($v)/100;

                        $rate -= $rate * $disc;

                        $totalratediscountcalc += floatval($v);    
                    }
                }                

                $gross_value = $rate * floatval($p['Quantity']);
                $totalgrosssum+=$gross_value;

                $dcalc = $p['results'][$first_key]['discount_percentage'];
                $tdclc = 0;
                foreach($dcalc as $d => $v){
                    if(!empty($v)){
                        $disc =  floatval($v)/100;
                        $rate -= $rate * $disc;
                        $totaldcalc += floatval($v);
                        $tdclc +=floatval($v);
                    }
                }

                $dacalc = $p['results'][$first_key]['discount_amount'];
                $tdcla = 0;
                foreach($dacalc as $d => $v){
                    $disa = !empty($v) ? floatval($v):0;
                    $rate -= $disa;
                    $totaldacalc += !empty($v) ? floatval($v):0;
                    $tdcla += floatval($v);
                }

                $taxable_value = $rate * floatval($p['Quantity']);
                $totaltaxableamount+=$taxable_value;


                $tdcalc = $p['results'][$first_key]['total_discount_percentage'];
                foreach($tdcalc as $d => $v){
                    if(!empty($v)){
                        $disc =  floatval($v)/100;
                        $total_disc_perc_value += $disc;
                        $total_disc_perc += floatval($v);
                    }
                }

                $tdacalc = $p['results'][$first_key]['total_discount_amount'];
                foreach($tdacalc as $d => $v){
                    if(!empty($v)){
                        $total_disc_amount += floatval($v);
                    }
                }


                if(!empty($p['GST %'])){
                    $total_single_gst_perc += floatval($p['GST %']);

                    $gst_perc =  floatval($p['GST %'])/100;
                    $gst_total = $taxable_value * $gst_perc;
                    $total_gst+=$gst_total;
                    $total_single_gst_rate += $gst_total / 2;
                    $total_with_gst = $taxable_value+$gst_total;
                    $total_grand += $total_with_gst;
                }

                
                

                $finalArray['products'][]=[
                    'product_id'=>(int)$p['Product ID'],
                    'product_description' => $product->product_description,
                    'hsn_code' => $product->hsn_code,
                    'quantity' => floatval($p['Quantity']),
                    'mrp' => floatval($this->roundOff($p['MRP'])),
                    'rate' => floatval($this->roundOff($rate)),
                    'gross_total' => floatval($this->roundOff($gross_value)),
                    'discount' => floatval($this->roundOff($tdclc)),
                    'discount_amt' => floatval($this->roundOff($tdcla)),
                    'taxable_value' => floatval($this->roundOff($taxable_value)),
                    'tax_rate' => floatval($this->roundOff($p['GST %'])),
                    'cgst_perc' => floatval($this->roundOff(($p['GST %'])/2)),
                    'cgst' => floatval($this->roundOff($gst_total/2)),
                    'sgst_perc' => floatval($this->roundOff(($p['GST %'])/2)),
                    'sgst' => floatval($this->roundOff($gst_total/2)),
                    'total_amount' => floatval($this->roundOff($total_with_gst,2)),
                    
                ];
            }
            
            $finalArray['product_total_taxv'] =  $this->roundOff(floatval($totaltaxableamount));
            
            $finalArray['total_grand2'] =  $this->roundOff(floatval($totaltaxableamount + $total_gst),2);            

            $totaltaxableamount = $totalgrosssum;
            $total_disc_amount_inv = $totaltaxableamount;
            if($total_disc_perc !=0){
                $total_disc_amount_inv = ($totaltaxableamount * ($total_disc_perc/100));
                $totaltaxableamount = $totaltaxableamount - ($totaltaxableamount * ($total_disc_perc/100));
            }
            if($total_disc_amount !=0){
                $totaltaxableamount -= $total_disc_amount;
            }            
            $total_grand = $totaltaxableamount + $total_gst;
            
            if($finalArray['total_round_off'] != 0){
                $total_grand += floatval($finalArray['total_round_off']);
            }


            $finalArray['total_quantity'] = $this->roundOff($totalquantity,2);
            $finalArray['total_mrp'] = $this->roundOff($totalmrp,2);
            $finalArray['total_discount'] = $this->roundOff(floatval($totaldcalc),2);
            $finalArray['total_discount_amt'] = $this->roundOff($totaldacalc,2);
            $finalArray['invoice_total_discount'] = $this->roundOff($total_disc_perc,2);
            $finalArray['invoice_total_discount_amt'] = $this->roundOff($total_disc_amount_inv,2);
            $finalArray['total_taxable_value'] = $this->roundOff(floatval($totaltaxableamount));
            $finalArray['total_gross_sum'] = $this->roundOff(floatval($totalgrosssum));
            $finalArray['total_cgst'] = $this->roundOff(floatval($total_single_gst_rate));
            $finalArray['total_cgst_perc'] = $this->roundOff(floatval($total_single_gst_perc));
            $finalArray['total_sgst'] = $this->roundOff(floatval($total_single_gst_rate));
            $finalArray['total_sgst_perc'] = $this->roundOff(floatval($total_single_gst_perc));
            $finalArray['total_grand'] = $this->roundOff(floatval($total_grand));
        }

        return $finalArray;

    }

    public function prepareInvoiceLayout($finalArray,$insinvoice){
        $customer = Customer::where('id',$finalArray['customer_id'])->first();
        $settings = Settings::get()->first();
        $custhtml = $this->customerHtml($customer);

        return $this->writeInvoicePdf($finalArray, $customer, $settings, $custhtml, $insinvoice->invoice_number);
    }

    public function genInv(Request $request){
        try{
            $request->validate([
                'file_csv' => 'required|file|mimes:csv,txt,xlsx,xls|max:4096',
            ]);

            $file = $request->file('file_csv');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            $headers = array_shift($rows);
            $invoices = [];
            $currentInvoiceNo = null;

            foreach ($rows as $row) {
                if (! is_array($headers) || count($headers) !== count($row)) {
                    continue;
                }
                $rowData = collect($headers)->combine($row)->map(fn($val) => trim((string) $val))->toArray();

                if (!empty($rowData['Invoice No'])) {
                    $currentInvoiceNo = $rowData['Invoice No'];
                }

                if ($currentInvoiceNo) {
                    unset($rowData['Invoice No']);
                    $invoices[$currentInvoiceNo][] = $rowData;
                }
            }

            $newc = $this->prepareCsv($invoices);
            $filename = null;

            foreach ($newc as $invoiceNumber => $payload) {
                $finalArray = $this->calculateInvoice([$invoiceNumber => $payload]);
                $finalArray['invoice_date'] = $this->parseInvoiceDate($finalArray['invoice_date'] ?? null);
                $finalArray['csv'] = json_encode($payload);

                $insinvoice = $this->persistInvoiceAndStock($finalArray);
                $filename = $this->prepareInvoiceLayout($finalArray, $insinvoice);
            }

            if (! $filename) {
                return response()->json(['success' => false, 'message' => 'No invoices found in the file.'], 422);
            }

            return response()->json([
                'zipUrl' => route('downloadZip', ['file' => $filename])
            ]);
        }
        catch (ValidationException $e) {
            throw $e;
        }
        catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Could not generate invoices from the file.',
            ], 422);
        }
    }


    public function roundOff($value,$precision=2){

        $rounded = ceil($value * pow(10, $precision)) / pow(10, $precision);
        return $rounded;
    }

    public function downloadSample(){
        $path = SafeDownload::path(storage_path('app/public'), 'sample_invoice.csv', ['sample_invoice.csv']);

        return response()->download($path)->deleteFileAfterSend(false);
    }

    private function persistInvoiceAndStock(array $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            Invoice::query()->orderByDesc('id')->lockForUpdate()->first();

            $number = trim((string) $invoice['invoice_number']);
            if (Invoice::query()->where('invoice_number', $number)->lockForUpdate()->exists()) {
                throw ValidationException::withMessages([
                    'invoice_number' => 'Invoice number already exists.',
                ]);
            }

            $qtyByProduct = [];
            foreach ($invoice['products'] as $p) {
                $pid = (int) $p['product_id'];
                $qtyByProduct[$pid] = ($qtyByProduct[$pid] ?? 0) + (float) $p['quantity'];
            }

            $stocks = Inventory::query()
                ->whereIn('product_id', array_keys($qtyByProduct))
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            foreach ($qtyByProduct as $pid => $qty) {
                $stock = $stocks->get($pid);
                if (! $stock) {
                    throw ValidationException::withMessages([
                        'product_id' => "No inventory found for product {$pid}.",
                    ]);
                }
                if ($stock->available_stock < $qty) {
                    throw ValidationException::withMessages([
                        'product_qty' => 'Insufficient stock for one or more products.',
                    ]);
                }
            }

            $record = Invoice::create([
                'invoice_number' => $number,
                'invoice_date' => $invoice['invoice_date'] ?? null,
                'customer_id' => $invoice['customer_id'],
                'round_off' => $invoice['total_round_off'] ?? 0,
                'total' => $invoice['total_grand'],
                'csv' => $invoice['csv'] ?? null,
            ]);

            foreach ($invoice['products'] as $p) {
                $stock = $stocks->get((int) $p['product_id']);
                $stock->available_stock -= $p['quantity'];
                $stock->save();
                $stocks->put((int) $p['product_id'], $stock);

                InventoryHistory::create([
                    'product_id' => $p['product_id'],
                    'stock_out_in' => $p['quantity'],
                    'action' => 'removed',
                ]);

                InvoiceProduct::create([
                    'invoice_id' => $record->id,
                    'product_id' => $p['product_id'],
                    'quantity' => $p['quantity'],
                    'mrp' => $p['mrp'] ?? null,
                    'rate' => $p['rate'],
                    'discount' => $p['discount'],
                    'discount_percentage' => $p['discount'] ?? null,
                    'discount_amt' => $p['discount_amt'] ?? null,
                    'tax_rate' => $p['tax_rate'],
                    'total' => $p['total_amount'],
                    'profit' => $p['profit'] ?? 0,
                ]);
            }

            return $record;
        });
    }

    private function writeInvoicePdf(array $invoice, $customer, $settings, string $custhtml, string $invoiceNumber): string
    {
        $safeNumber = preg_replace('/[^A-Za-z0-9._-]+/', '-', $invoiceNumber) ?: 'invoice';
        $filename = 'invoices-'.$safeNumber.'-'.time().'.pdf';
        $filePath = storage_path('app/public/'.$filename);

        $pdf = PDF::loadView('pages.generate-invoice.invoice-format', compact('invoice', 'customer', 'settings', 'custhtml'));
        $pdf->setPaper('a4', 'portrait');
        $pdf->set_option('defaultFont', 'Arial');
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isRemoteEnabled', false);
        $pdf->set_option('isPhpEnabled', false);
        $pdf->set_option('margin_top', 0);
        $pdf->set_option('margin_bottom', 0);
        $pdf->set_option('margin_left', 0);
        $pdf->set_option('margin_right', 0);
        file_put_contents($filePath, $pdf->output());

        return $filename;
    }

    private function nextInvoiceSequence(): int
    {
        return (int) Invoice::query()->max('id') + 1;
    }

    private function parseInvoiceDate(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        foreach (['d-m-Y', 'Y-m-d', 'd/m/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function customerHtml(?Customer $customer): string
    {
        if (! $customer) {
            return '';
        }

        $custhtml = '';
        if (!empty($customer->customer_name)) {
            $custhtml .= 'Name: '.$customer->customer_name;
        }
        if (!empty($customer->address)) {
            $custhtml .= '<br>Address: '.$customer->address;
        }
        if (!empty($customer->state)) {
            $custhtml .= '<br>State: '.$customer->state;
        }
        if (!empty($customer->state_code)) {
            $custhtml .= '<br>State Code: '.$customer->state_code;
        }
        if (!empty($customer->gstin_number)) {
            $custhtml .= '<br>GSTIN No: '.$customer->gstin_number;
        }
        if (!empty($customer->phone)) {
            $custhtml .= '<br>Mobile: '.$customer->phone;
        }
        if (!empty($customer->pan_number)) {
            $custhtml .= '<br>PAN: '.$customer->pan_number;
        }

        return $custhtml;
    }
}