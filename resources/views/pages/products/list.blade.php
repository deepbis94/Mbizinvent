<x-layout>
    <div class="app-content mt-3">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- /.col -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Product List</h3>

                                <div class="card-tools d-flex">
                                    <input
                                        type="text"
                                        id="searchString"
                                        class="form-control"
                                        placeholder="Search Products"
                                        onkeyup="searchProduct()"
                                    />
                                    <button
                                        class="btn btn-warning w-100"
                                        style="margin-left: 11px"
                                        onclick="exportProducts()"
                                    >
                                    <i class="bi bi-download"></i>  Export List
                                    </button>
                                    <button
                                        class="btn btn-success w-100"
                                        style="margin-left: 11px"
                                        onclick="startBulkUpload()"
                                    >
                                    <i class="bi bi-upload"></i>  Import List
                                    </button>
                                    <input type="file" id="csv-file" accept=".csv" style="display: none" onchange="uploadBulk()" />

                                    <button
                                        class="btn btn-primary w-100"
                                        style="margin-left: 11px"
                                        onclick="showAddProductModal()"
                                    >
                                        Add Product
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="loader-holder" style="display:none">
                                    <div class="d-flex" style="justify-content: center;padding: 60px;">
                                        <div class="loader"></div> 
                                    </div>
                                  </div>
            
                                <table class="table" id="prodTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>HSN Code</th>
                                            <th>Product Description</th>
                                            <th>Rate</th>
                                            <th>GST %</th>
                                            <th>Created Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptBody">
                                        @include('pages.products.single',['products'=>$products])
                                    </tbody>
                                </table>
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
                </div>
                <!-- /.container-fluid -->
            </div>
        </section>
    </div>
    @include('pages.products.scripts')
</x-layout>
