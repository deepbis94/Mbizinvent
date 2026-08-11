<x-layout>
    <div class="app-content mt-3">
        <section class="content">
            <div class="container-fluid">
              <div class="row">
                <!-- /.col -->
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Customer List</h3>
                      <div class="card-tools d-flex">
                        <input type="text" id="searchCustInput" class="form-control" placeholder="Search Customer" onkeyup="searchCustomer()">
                        <button class="btn btn-warning w-100" style="margin-left:11px" onclick="exportCustomers()"><i class="bi bi-download"></i> Export List</button>                        
                        <button class="btn btn-success w-100" style="margin-left:11px" onclick="importCustomers()"><i class="bi bi-upload"></i> Import List</button>                                                
                        <input type="file" id="csv-file" accept=".csv" style="display: none" onchange="uploadBulk()" />                        
                        <button class="btn btn-primary w-100" style="margin-left:11px" onclick="showAddCustomerModal()">Add Customer</button>
                      </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                      <table class="table" id="custTable">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>GSTIN Number</th>
                            <th>Phone</th>
                            <th>Created Date</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody id="ctBody">
                            @include('pages.customers.single',['customers'=>$customers])
                        </tbody>
                      </table>
                      <div class="loader-holder" style="display:none">
                        <div class="d-flex" style="justify-content: center;padding: 60px;">
                            <div class="loader"></div> 
                        </div>
                      </div>

                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-2 d-flex">
                                <span
                                    style="
                                        width: 139px;
                                        margin-top: 7px;
                                    "
                                    >Per Page</span
                                >
                                <select
                                    id="perPage"
                                    class="form-control"
                                    onchange="paginate()"
                                >
                                @foreach(range(10, 100, 10) as $number)
                                    <option value="{{ $number }}">{{ $number }}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 d-flex">
                                <span style="
                                        width: 173px;
                                        margin-top: 7px;
                                    ">Current Page</span>
                                <select id="currentPage" class="form-control" onchange="paginate()">
                                    @foreach (range(1, $totalpagenums, 1) as $n)
                                        <option value="{{ $n }}">{{ $n }}</option>                                        
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3" style="margin-top: 7px">
                                Total Records <span>{{ $totalRecords }}</span>
                            </div>
                        </div>
                    </div>
                  </div>
                  <!-- /.card -->
      
                  <!-- /.card -->
                </div>

              <!-- /.row -->
            </div><!-- /.container-fluid -->
          </section>        
    </div>
    @include('pages.customers.scripts')
</x-layout>