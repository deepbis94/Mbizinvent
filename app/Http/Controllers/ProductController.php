<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesAjaxLists;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryHistory;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    use PaginatesAjaxLists;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $this->listPage($request);
        $query = Product::query()->orderBy('id', 'desc');

        if ($page['search'] !== '') {
            $query->where('product_description', 'LIKE', '%'.$page['search'].'%');
        }

        $totalRecords = (clone $query)->count();
        $totalpagenums = $this->pageCount($totalRecords, $page['perPage']);
        $products = $query->skip($page['skip'])->take($page['perPage'])->get();
        $view = $page['isFragment'] ? 'pages.products.single' : 'pages.products.list';

        return view($view, compact('products', 'totalpagenums', 'totalRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.products.add-or-edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'product_description' => 'required|string',
            'hsn_code' => 'required',
            'rate' => 'required',
            'gst_percentage' => 'required',

        ]);

        $product = Product::create([
            'product_description'=>$validated['product_description'],
            'hsn_code'=>$validated['hsn_code'],
            'rate'=>$validated['rate'],
            'gst_percentage'=>$validated['gst_percentage'],

        ]);

        Inventory::firstOrCreate(
            ['product_id' => $product->id],
            ['available_stock' => 0]
        );

        return response()->json(['success' => true, 'message' => 'Product created successfully!']);        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('pages.products.add-or-edit',compact('product'));        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validated = $request->validate([
            'product_description' => 'required|string',
            'hsn_code' => 'required',
            'rate' => 'required',
            'gst_percentage' => 'required',

        ]);

        $product = Product::findOrFail($id);
        if($product){
            $product->product_description = $validated['product_description'];
            $product->hsn_code = $validated['hsn_code'];
            $product->rate = $validated['rate'];
            $product->gst_percentage = $validated['gst_percentage'];
            

            $product->save();
        }

        return response()->json(['success' => true, 'message' => 'Product updated successfully!']);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        if($product){
            Inventory::where('product_id', $id)->delete();
            InventoryHistory::where('product_id', $id)->delete();            
            $product->delete();
            return response()->json(['success' => true, 'message' => 'Product deleted successfully!']);
            
        }
        return response()->json(['success' => false, 'message' => 'Product not Found!']);           
    }

    public function exportProducts(){
        // Fetch products with their related measurements
        $products = Product::get();

        // Generate the filename
        $filename = 'products-' . now()->timestamp . '.csv';

        // Define the path where the file will be stored
        $filePath = storage_path('app/' . $filename);

        // Open the file for writing
        $handle = fopen($filePath, 'w');

        // Add the CSV column headings (optional)
        fputcsv($handle, ['ID','Hsn Code', 'Product Description', 'rate','GST percentage']);

        // Loop through the data and write each row to the CSV file
        foreach ($products as $p) {
            // Write the product row to the CSV
            fputcsv($handle, [
                $p->id,
                $p->hsn_code,
                $p->product_description,
                $p->rate,
                $p->gst_percentage,

            ]);
        }

        // Close the file handle
        fclose($handle);

        // Return the file path so it can be used for the download
        return response()->json(['url_path'=> route('exportDownload',['file'=>$filename])]);

    }

    public function importProducts(Request $request){
        // Validate the uploaded file
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt|max:2048', // You can adjust file type and size
        ]);

        // Handle file upload
        if ($request->hasFile('file_csv')) {
            $file = $request->file('file_csv');

            // Process the CSV (example)
            $csvData = $this->parseCsv($file);
            $allowed = ['product_description','hsn_code','rate','gst_percentage'];
            foreach ($csvData as $csv) {
                $newArray = collect($csv)
                    ->mapWithKeys(function ($value, $key) {
                        $newKey = strtolower(str_replace(' ', '_', $key));
                        return [$newKey => $value];
                    })
                    ->only($allowed)
                    ->toArray();

                if ($newArray === [] || empty($newArray['product_description'])) {
                    continue;
                }

                $product = Product::create($newArray);
                Inventory::firstOrCreate(
                    ['product_id' => $product->id],
                    ['available_stock' => 0]
                );
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
