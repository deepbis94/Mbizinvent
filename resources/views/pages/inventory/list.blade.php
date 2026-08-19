<x-layout>
    <div class="app-content mt-3">
        <section class="content">
            <div class="container-fluid">
              <div class="row">
                <!-- /.col -->
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Inventory List</h3>
      
                      <div class="card-tools d-flex">
                        <input type="text" id="searchInventory" class="form-control" placeholder="Search Product" onkeyup="filterInventory()">
                        <button class="btn btn-warning w-100" style="margin-left:11px" onclick="exportInventory()"><i class="bi bi-download"></i> Export List</button>                        
                        <button class="btn btn-success w-100" style="margin-left:11px" onclick="importInventory()"><i class="bi bi-upload"></i> Import List</button>                                                
                        <input type="file" id="csv-file" accept=".csv" style="display: none" onchange="uploadBulk()" />                        

                        <button class="btn btn-primary w-100" style="margin-left:11px" onclick="showAddInventoryModal()">Add Inventory</button>
                      </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Product ID</th>
                            <th>HSN Code</th>
                            <th>Product Description</th>
                            <th>Available Stock</th>
                            <!-- <th>Buying Price</th> -->
                            <th>Updated Date</th>
                          </tr>
                        </thead>
                        <tbody>
                            @include('pages.inventory.single',['inventory'=>$inventory])
                        </tbody>
                      </table>
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
    @include('pages.inventory.scripts')
</x-layout>