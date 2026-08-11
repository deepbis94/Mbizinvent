
        <form action="" id="addEditForm" method="post">
            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input type="text" class="form-control" required name="customer_name" id="customer_name" value="{{ $customer->customer_name ?? ''  }}" onkeyup="revalidateForm('customer_name')" />
                <span id="customer_name-error" class="error invalid-feedback d-none" ></span>
              </div>
              <div class="form-group mt-2">
                <label for="address">Address</label>
                <input type="text" class="form-control" required name="address" id="address" value="{{ $customer->address ?? ''  }}" onkeyup="revalidateForm('address')" />
                <span id="address-error" class="error invalid-feedback d-none" ></span>
              </div>
              <div class="form-group mt-2">
                  <label for="state">State</label>
                  <input type="text" class="form-control" required name="state" id="state" value="{{ $customer->state ?? ''  }}" onkeyup="revalidateForm('state')" />
                <span id="state-error" class="error invalid-feedback d-none" ></span>
              </div>
              <div class="form-group mt-2">
                  <label for="state_code">State Code</label>
                  <input type="text" class="form-control" required name="state_code" id="state_code" value="{{ $customer->state_code ?? '' }}" onkeyup="revalidateForm('state_code')" />
                <span id="state_code-error" class="error invalid-feedback d-none" ></span>
              </div>                            
              <div class="form-group mt-2">
                <label for="city">City</label>
                <input type="text" class="form-control" required name="city" id="city" value="{{ $customer->city ?? '' }}" onkeyup="revalidateForm('city')" />
                <span id="city-error" class="error invalid-feedback d-none" ></span>
              </div>                          
              <div class="form-group mt-2">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" required name="phone" id="phone" value="{{ $customer->phone ?? ''  }}" onkeyup="revalidateForm('phone')" />
                <span id="phone-error" class="error invalid-feedback d-none" ></span>
              </div>                                                                  
              <div class="form-group mt-2">
                <label for="gstin_number">GSTIN Number</label>
                <input type="text" class="form-control" required name="gstin_number" id="gstin_number" value="{{ $customer->gstin_number ?? ''  }}" onkeyup="revalidateForm('gstin_number')" />
                <span id="gstin_number-error" class="error invalid-feedback d-none" ></span>

              </div>                                
              <div class="form-group mt-2">
                <label for="pan_number">PAN Number</label>
                <input type="text" class="form-control" required name="pan_number" id="pan_number" value="{{ $customer->pan_number  ?? '' }}" onkeyup="revalidateForm('pan_number')" />
                <span id="pan_number-error" class="error invalid-feedback d-none" ></span>

              </div>               
            </form>