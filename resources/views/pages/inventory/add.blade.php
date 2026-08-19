
<form action="" id="addEditForm" method="post">
    <div class="form-group">
        <label for="product_id">Select Product</label>
        <select class="form-control" name="product_id" id="product_id">
            @forelse ($products as $p)
                <option value="{{ $p->id }}">{{ $p->product_description }}</option>
            @empty
                <option value=""></option>
            @endforelse
          </select>        
    </div>         
    <div class="form-group mt-2">
        <label for="stock_count">Stock Count</label>
        <input type="text" class="form-control" required name="stock_count" id="stock_count"  onkeyup="revalidateForm('stock_count')" />
        <span id="stock_count-error" class="error invalid-feedback d-none" ></span>
      </div> 
      <!-- <div class="form-group mt-2">
        <label for="buying_price">Buying Price</label>
        <input type="text" class="form-control" required name="buying_price" id="buying_price"  onkeyup="revalidateForm('buying_price')" />
        <span id="buying_price-error" class="error invalid-feedback d-none" ></span>
      </div>           -->
    </form>