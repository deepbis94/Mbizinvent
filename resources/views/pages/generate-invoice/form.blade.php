<x-layout>
    <style>
.select2-container {
    width: 300px !important;
}
        
    </style>
    <div class="app-content mt-3">
        <section class="content">
            <div class="container-fluid">
              <div class="row">
                <!-- /.col -->
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Generate Invoice</h3>
                      <button class="btn btn-primary ml-4" style="margin-left: 14px" onclick="uploadInvoice()" id="upinvbtn">
                         <span id="uploadinv">Upload Invoice</span> 
                         <span id="uploading" style="display:none">Uploading <i class="nav-icon bi bi-hourglass-bottom"></i></span>                          
                      </button>
                      <input type="file" id="csv-file" accept=".csv" style="display: none" onchange="uploadBulk()" />                                              
                      <button class="btn btn-primary ml-4" style="margin-left: 14px" onclick="downloadSample()">Download Sample</button>                      
                    </div>  
                    <form id="invForm" method="POST">
                    <div class="card-body">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Customer Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="customer_id">Select Customer</label>
                                            <select name="customer_id" id="customer_id" class="form-control sel2input">
                                                @forelse ($customers as $c)
                                                    <option value="{{ $c->id }}">{{ $c->customer_name }}</option>
                                                @empty
                                                    <option value=""></option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="invoice_number">Invoice Number</label>
                                            <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="MM-{{ $invoiceCount }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="invoice_date">Invoice Date</label>
                                            <input type="text" name="invoice_date" id="invoice_date" class="form-control datepicker">
                                        </div>
                                    </div>                            
                                </div>
        
                            </div>
                        </div>
                        <div class="card card-success mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Product Information</h3>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary" type="button" onclick="addProduct()">Add Product</button>
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>MRP</th>
                                            <th>Profit</th>
                                            <td>Disc. %</td>
                                            <td>Disc. Amt</td>
                                            <td>Rate</td>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoice_product_tbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card card-dark mt-3">
                            <div class="card-header">
                                <h3 class="card-title">Total Information</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th>Qty</th>
                                            <td>Disc. %</td>
                                            <td>Disc. Amt</td>
                                            <td>Taxable Value</td>
                                            <th>Round Off</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    @include('pages.generate-invoice.total-invoice')


                                </table>                                

                            </div>
                        </div>

                        <button class="btn btn-primary mt-3" type="button" onclick="generateInvoice()">Generate Invoice</button>
                    </div>

                </form>
                    
                  </div>
                </div>
              </div>
            </div>
        </section>
    </div>
    @include('pages.generate-invoice.scripts')
</x-layout>