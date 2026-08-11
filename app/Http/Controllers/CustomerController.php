<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesAjaxLists;
use App\Models\Customer;
use App\Support\SafeDownload;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use PaginatesAjaxLists;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page = $this->listPage($request);
        $query = Customer::query()->orderBy('id', 'desc');

        if ($page['search'] !== '') {
            $query->where('customer_name', 'LIKE', '%'.$page['search'].'%');
        }

        $totalRecords = (clone $query)->count();
        $totalpagenums = $this->pageCount($totalRecords, $page['perPage']);
        $customers = $query->skip($page['skip'])->take($page['perPage'])->get();
        $view = $page['isFragment'] ? 'pages.customers.single' : 'pages.customers.list';

        return view($view, compact('customers', 'totalpagenums', 'totalRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.customers.add-or-edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'address' => 'nullable|string',
            'state' => 'nullable|string',
            'state_code' => 'nullable|string',
            'city' => 'nullable|string',
            'phone' => 'nullable|string',
            'gstin_number' => 'nullable|string',
            'pan_number' => 'nullable|string',

        ]);

        Customer::create([
            'customer_name'=>$validated['customer_name'],
            'address'=>$validated['address'],
            'state'=>$validated['state'],
            'state_code'=>$validated['state_code'],
            'city'=>$validated['city'],
            'phone'=>$validated['phone'],
            'gstin_number'=>$validated['gstin_number']??'',
            'pan_number'=>$validated['pan_number']??''
        ]);

        return response()->json(['success' => true, 'message' => 'Customer created successfully!']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        return view('pages.customers.add-or-edit',compact('customer'));
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

        // Validate the incoming request
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'address' => 'required|string',
            'state' => 'required|string',
            'state_code' => 'required|string',
            'city' => 'string',
            'phone' => 'string',
            'gstin_number' => '',
            'pan_number' => '',

        ]);

        $customer = Customer::findOrFail($id);
        if($customer){
            $customer->customer_name = $validated['customer_name'];
            $customer->address = $validated['address'];
            $customer->state = $validated['state'];
            $customer->state_code = $validated['state_code'];
            $customer->city = $validated['city'];
            $customer->phone = $validated['phone'];
            $customer->gstin_number = $validated['gstin_number'];
            $customer->pan_number = $validated['pan_number'];
            $customer->save();
        }

        return response()->json(['success' => true, 'message' => 'Customer updated successfully!']);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        if($customer){
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully!']);
            
        }
        return response()->json(['success' => false, 'message' => 'Customer not Found!']);        
    }

    public function exportCustomers(){
        // Fetch products with their related measurements
        $customers = Customer::get();

        // Generate the filename
        $filename = 'customers-' . now()->timestamp . '.csv';

        // Define the path where the file will be stored
        $filePath = storage_path('app/' . $filename);

        // Open the file for writing
        $handle = fopen($filePath, 'w');

        // Add the CSV column headings (optional)
        fputcsv($handle, ['ID','Customer Name', 'Address', 'State','State Code','City','Phone','GSTIN Number','PAN Number']);

        // Loop through the data and write each row to the CSV file
        foreach ($customers as $customer) {
            // Write the product row to the CSV
            fputcsv($handle, [
                $customer->id,
                $customer->customer_name,
                $customer->address,
                $customer->state,
                $customer->state_code,
                $customer->city,
                $customer->phone,
                $customer->gstin_number,
                $customer->pan_number,
            ]);
        }

        // Close the file handle
        fclose($handle);

        // Return the file path so it can be used for the download
        return response()->json(['url_path'=> route('exportDownload',['file'=>$filename])]);

    }

    public function exportDownload(Request $request,$file){
        $path = SafeDownload::path(storage_path('app'), $file, ['customers-', 'products-', 'inventory-']);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function importCustomers(Request $request){
        // Validate the uploaded file
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt|max:2048', // You can adjust file type and size
        ]);

        // Handle file upload
        if ($request->hasFile('file_csv')) {
            $file = $request->file('file_csv');

            // Process the CSV (example)
            $csvData = $this->parseCsv($file);
            $allowed = ['customer_name','address','state','state_code','city','phone','gstin_number','pan_number'];
            foreach ($csvData as $csv) {
                $newArray = collect($csv)
                    ->mapWithKeys(function ($value, $key) {
                        $newKey = strtolower(str_replace(' ', '_', $key));
                        return [$newKey => $value];
                    })
                    ->only($allowed)
                    ->toArray();

                if ($newArray === []) {
                    continue;
                }

                Customer::create($newArray);
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
