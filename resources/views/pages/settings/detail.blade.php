<x-layout>
    <div class="app-content mt-3">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- /.col -->
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title">Settings</h3>
                        </div>
                        <div class="card-body">
                            <form action="" id="addEditForm" method="POST">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="dist_name">Company Name</label>
                                            <input type="text" class="form-control" required name="dist_name" id="dist_name" value="{{ $setting->dist_name ?? ''  }}" onkeyup="revalidateForm('dist_name')" />
                                            <span id="dist_name-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" required name="address" id="address" value="{{ $setting->address ?? ''  }}" onkeyup="revalidateForm('address')" />
                                            <span id="address-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>                                
                                    <div class="col-lg-6 mt-3">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control" required name="city" id="city" value="{{ $setting->city ?? ''  }}" onkeyup="revalidateForm('city')" />
                                            <span id="city-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>  
                                    <div class="col-lg-6 mt-3">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <input type="text" class="form-control" required name="state" id="state" value="{{ $setting->city ?? ''  }}" onkeyup="revalidateForm('state')" />
                                            <span id="state-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>                                                                
                                    <div class="col-lg-6 mt-3">
                                        <div class="form-group">
                                            <label for="state_code">State Code</label>
                                            <input type="text" class="form-control" required name="state_code" id="state_code" value="{{ $setting->state_code ?? ''  }}" onkeyup="revalidateForm('state_code')" />
                                            <span id="state_code-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>                                                                                                
                                    <div class="col-lg-6 mt-3">
                                        <div class="form-group">
                                            <label for="phone">Mobile Number</label>
                                            <input type="text" class="form-control" required name="phone" id="phone" value="{{ $setting->phone ?? ''  }}" onkeyup="revalidateForm('phone')" />
                                            <span id="phone-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>                                                                                                
                                    <div class="col-lg-6 mt-3">
                                        <div class="form-group">
                                            <label for="gstin_number">GSTIN Number</label>
                                            <input type="text" class="form-control" required name="gstin_number" id="gstin_number" value="{{ $setting->gstin_number ?? ''  }}" onkeyup="revalidateForm('gstin_number')" />
                                            <span id="gstin_number-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>                                                                                                
                                    <div class="col-lg-6 mt-3">
                                        <div class="form-group">
                                            <label for="pan_number">PAN Number</label>
                                            <input type="text" class="form-control" required name="pan_number" id="pan_number" value="{{ $setting->pan_number ?? ''  }}" onkeyup="revalidateForm('pan_number')" />
                                            <span id="pan_number-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>                                                                                                                                
                                    <div class="col-lg-6 mt-3">
                                        <div class="form-group">
                                            <label for="pan_number">Profit Calculation</label>
                                            <input type="text" class="form-control" required name="profit_calc" id="profit_calc" value="{{ $setting->profit_calc ?? ''  }}" onkeyup="revalidateForm('profit_calc')" />
                                            <span id="profit_calc-error" class="error invalid-feedback d-none" ></span>
                                        </div>
                                    </div>                                                                                                                                                                    
                                </div>
    
                            </form>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary" type="button" onclick="submitForm()">Submit Data</button>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script>
        function revalidateForm(id){
            let elem = $("#"+id);
            let errorDiv = $(`#${id}-error`);
            if(elem.val().length > 0){
                elem.removeClass('is-invalid');
                errorDiv.addClass('d-none');
            }
            else{
                elem.addClass('is-invalid');
                errorDiv.removeClass('d-none');
                errorDiv.html(`This field is required`)
            }
        }

        function submitForm(){
            $("#addEditForm").submit();
        }
        $("#addEditForm").on("submit", function(ev){
                ev.preventDefault();
                
                let values = $("#addEditForm").serializeArray();  
                let isFormValid = true;
                let errors = [];
                values.some((v)=>{
                    
                    if(v.value.length == 0){
                        errors.push({
                            key: v.name,
                            error: `${v.name} is required`
                        });
                        isFormValid = false
                    }                    
                });

                if(errors.length > 0){
                    
                    errors.some((er)=>{
                        let field = $(`#${er.key}`);
                        let errorField = $(`#${er.key}-error`);                        

                        field.addClass('is-invalid')
                        errorField.removeClass('d-none')
                        errorField.html(`${er.key} is required`);
                        
                    });
                }
                else{

                    let url = '{{ route('settingsUpdate') }}';

                    $.ajax({
                        url:url,
                        type: 'POST',
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ), // CSRF Token
                        },                        
                        data:values,
                        success: function(response){
                            if(response.success){
                                alert(response.message);
                                location.reload()
                            }
                        },
                        error: function(ev){
                            console.log(ev);
                            
                        }
                    });
                
                }                
                        
                        
            });

    </script>
</x-layout>