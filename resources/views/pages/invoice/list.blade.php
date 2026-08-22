<x-layout>
    <div class="app-content mt-3">
        <section class="content">
            <div class="container-fluid">
              <div class="row">
                <!-- /.col -->
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title">Invoice List</h3>
      
                      <div class="card-tools d-flex">
                        <input type="text" id="searchInvoice" class="form-control" placeholder="Search Invoice" onkeyup="searchInvoice()">
                      </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                      <table class="table" id="invTable">
                        <thead>
                          <tr>
                            <th>Invoice No.</th>
                            <th>Customer Name</th>
                            <th>Invoice Total</th>
                            <th>Created Date</th>
                          </tr>
                        </thead>
                        <tbody id="invBody">
                            @include('pages.invoice.single',['invoices'=>$invoices])
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
    <script>
        function paginate(){
            let perPage = $("#perPage").val();
        let currentPage = $("#currentPage").val();
        $("#invTable").hide();
        $(".loader-holder").show();


        let url = '{{ route('invoiceList') }}';
        $.ajax({
                    url:url,
                    type: 'GET',
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ), // CSRF Token
                    },                      
                    data:{
                        perPage:perPage,
                        currentPage:currentPage,
                        searchString: $("#searchInvoice").val()
                    },  
                    success: function(response){
                        $("#invBody").html(response)
                        $("#invTable").show();
                        $(".loader-holder").hide();

                    },
                    error: function(ev){
                        
                    }
                });            

        }
        function searchInvoice(){
            paginate();
        }

    </script>
</x-layout>