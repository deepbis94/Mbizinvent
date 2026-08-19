<x-layout>
    <div class="app-content mt-3">
        <section class="content">
            <div class="container-fluid">
              <div class="row">
                <!-- /.col -->
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Inventory History</h3>
      
                      <div class="card-tools d-flex">
                        <input type="text" id="searchHistory" class="form-control" placeholder="Search Product" onkeyup="paginate()">
                      </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                      <table class="table" id="hiTable">
                        <thead>
                          <tr>
                            <th>HSN Code</th>
                            <th>Product Description</th>
                            <th>Stock Out In</th>
                            <th>Buying Price</th>
                            <th>Action</th>
                            <th>Updated Date</th>
                          </tr>
                        </thead>
                        <tbody id="hiBody">
                            @include('pages.inventory-history.single',['history'=>$history])
                        </tbody>
                      </table>
                      <div class="loader-holder" style="display:none">
                        <div class="d-flex" style="justify-content: center;padding: 60px;">
                            <div class="loader"></div> 
                        </div>
                      </div>

                    </div>
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

                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
      
                  <!-- /.card -->
                </div>

              <!-- /.row -->
            </div><!-- /.container-fluid -->
          </section>        
    </div>
    @include('pages.inventory-history.scripts')
</x-layout>