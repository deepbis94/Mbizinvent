<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesAjaxLists;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use PaginatesAjaxLists;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Inventory::with('product')->orderBy('id', 'desc');
        $search = trim((string) $request->input('searchString', ''));
        if ($search !== '') {
            $query->whereHas('product', function ($product) use ($search) {
                $product->where('product_description', 'like', "%{$search}%");
            });
        }

        $inventory = $query->get();
        return view('pages.inventory.list', compact('inventory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::get();
        return view('pages.inventory.add',compact('products'));        

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'product_id' => 'required',
            'stock_count' => 'required',
            // 'buying_price' => 'required',
        ]);

        $inventory = Inventory::where('product_id',$validated['product_id'])->first();
        if($inventory){
            InventoryHistory::create([
                'product_id'=>$validated['product_id'],
                'stock_out_in'=>$validated['stock_count'],
                // 'buying_price'=>$validated['buying_price'],
                'action'=>'added'
            ]);

            $total_stock = $inventory->available_stock + $validated['stock_count'];
            $inventory->available_stock = $total_stock;
            $inventory->save();
            return response()->json(['success' => true, 'message' => 'Inventory updated successfully!']);            

        }
        Inventory::create([
            'product_id'=>$validated['product_id'],
            'available_stock'=>$validated['stock_count'],
            // 'buying_price'=>$validated['buying_price'],
        ]);

        return response()->json(['success' => true, 'message' => 'Inventory created successfully!']);            
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function inventoryHistory(Request $request){

        $page = $this->listPage($request);
        $query = InventoryHistory::with('product')->orderBy('id', 'desc');

        if ($page['search'] !== '') {
            $query->whereHas('product', function ($product) use ($page) {
                $product->where('product_description', 'like', '%'.$page['search'].'%');
            });
        }

        $totalRecords = (clone $query)->count();
        $totalpagenums = $this->pageCount($totalRecords, $page['perPage']);
        $history = $query->skip($page['skip'])->take($page['perPage'])->get();
        $view = $page['isFragment'] ? 'pages.inventory-history.single' : 'pages.inventory-history.list';

        return view($view, compact('history', 'totalpagenums', 'totalRecords'));
    }

    public function exportInventory(){
        $inventory = Inventory::with('product')->orderBy('id','desc')->get();
        
        // Generate the filename
        $filename = 'inventory-' . now()->timestamp . '.csv';

        // Define the path where the file will be stored
        $filePath = storage_path('app/' . $filename);

        // Open the file for writing
        $handle = fopen($filePath, 'w');

        // Add the CSV column headings (optional)
        // fputcsv($handle, ['Product ID','Product Description', 'Stock', 'Buying Price']);
        fputcsv($handle, ['Product ID','Product Description', 'Stock']);


        // Loop through the data and write each row to the CSV file
        foreach ($inventory as $i) {
            // Write the product row to the CSV
            fputcsv($handle, [
                $i->product->id,
                $i->product->product_description,
                $i->available_stock,
                // $i->buying_price,
            ]);
        }

        // Close the file handle
        fclose($handle);

        // Return the file path so it can be used for the download
        return response()->json(['url_path'=> route('exportDownload',['file'=>$filename])]);
        
    }

    public function importInventory(Request $request){
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt|max:2048', // You can adjust file type and size
        ]);

        // Handle file upload
        if ($request->hasFile('file_csv')) {
            $file = $request->file('file_csv');

            // Process the CSV (example)
            $csvData = $this->parseCsv($file);
            foreach ($csvData as $csv) {
                // Use Laravel's Collection to transform the keys
                $newArray = collect($csv)
                    ->mapWithKeys(function ($value, $key) {
                        // Convert the key to lowercase and replace spaces with underscores
                        $newKey = strtolower(str_replace(' ', '_', $key));
                        return [$newKey => $value];
                    })
                    ->toArray();

                $productId = $newArray['product_id'] ?? null;
                $stock = $newArray['stock'] ?? null;
                if (! $productId || $stock === null) {
                    continue;
                }

                $inv = Inventory::where('product_id', $productId)->first();
                if (! $inv) {
                    continue;
                }
                if ($inv->available_stock < $stock) {
                    InventoryHistory::create([
                        'product_id' => $productId,
                        'stock_out_in' => $stock - $inv->available_stock,
                        'action' => 'added',
                    ]);
                }

                $inv->available_stock = $stock;
                $inv->save();
            }

            return response()->json(['success' => 'File uploaded successfully!']);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);          
    }

    private function parseCsv($file)
    {
        $csvData  = [];
        $filePath = $file->getRealPath();
        $file     = fopen($filePath, 'r');

        // Assuming the first row contains headers
        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            if (! is_array($header) || count($header) !== count($row)) {
                continue;
            }
            $csvData[] = array_combine($header, $row);
        }

        fclose($file);

        return $csvData;
    }        
}
