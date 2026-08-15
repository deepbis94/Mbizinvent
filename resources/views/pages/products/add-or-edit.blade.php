
<form action="" id="addEditForm" method="post">
    <div class="form-group">
        <label for="hsn_code">HSN Code</label>
        <input type="text" class="form-control" required name="hsn_code" id="hsn_code" value="{{ $product->hsn_code ?? ''  }}" onkeyup="revalidateForm('hsn_code')" />
        <span id="hsn_code-error" class="error invalid-feedback d-none" ></span>
      </div>
      <div class="form-group mt-2">
        <label for="product_description">Product Description</label>
        <input type="text" class="form-control" required name="product_description" id="product_description" value="{{ $product->product_description ?? ''  }}" onkeyup="revalidateForm('product_description')" />
        <span id="product_description-error" class="error invalid-feedback d-none" ></span>
      </div>
      <div class="form-group mt-2">
          <label for="rate">Rate</label>
          <input type="text" class="form-control" required name="rate" id="rate" value="{{ $product->rate ?? ''  }}" onkeyup="revalidateForm('rate')" />
        <span id="rate-error" class="error invalid-feedback d-none" ></span>
      </div>
      <div class="form-group mt-2">
          <label for="rate">GST Percentage</label>
          <input type="text" class="form-control" required name="gst_percentage" id="gst_percentage" value="{{ $product->gst_percentage ?? ''  }}" onkeyup="revalidateForm('gst_percentage')" />
        <span id="gst_percentage-error" class="error invalid-feedback d-none" ></span>
      </div>         
    </form>