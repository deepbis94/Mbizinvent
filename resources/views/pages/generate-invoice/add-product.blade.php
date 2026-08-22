
<tr id="product_{{ $index }}" class="invoiceProducts">
    <td style="width:200px">
        <div class="form-group">
            <select name="product_id[]" id="product_id_{{ $index }}" class="form-control sel2input" onchange="populateValues('{{$index}}',event)" style="width:100%">
                <option value="">Select</option>
                @forelse ($products as $p)
                    <option value="{{ $p->id }}">{{ $p->product_description }}</option>
                @empty
                    <option value=""></option>
                @endforelse
            </select>            
        </div>
    </td>
    <td>
        <div class="form-group">
            <input type="text" name="product_qty[]" id="product_qty_{{ $index }}" class="form-control product_qty" onkeyup="calculateTotal('{{$index}}')" value="1">
            <span style="font-size: 13px;color: red;font-weight: 400;" id="max_qty_{{ $index }}">Max Qty: </span>
            <input type="hidden" id="max_qty_f_{{ $index }}" value="0">     
            <input type="hidden" id="gst_percentage_{{ $index }}" value="0">            

        </div>
    </td>
    <td>
        <div class="form-group">
            <input type="text" name="product_mrp[]"  id="product_mrp_{{ $index }}" class="form-control product_mrp" onkeyup="calculateTotal('{{$index}}')" value="0">
            <input type="hidden" name="product_rate[]"  id="product_rate_{{ $index }}" class="form-control product_rate" value="0">            
        </div>
    </td>    
    <td>
        <div class="form-group">
            <input type="text" name="product_profit[]"  id="product_profit_{{ $index }}" class="form-control product_profit" onkeyup="calculateTotal('{{$index}}')" value="0">
        </div>
    </td>        
    <td>
        <div class="form-group">
            <input type="text" name="product_discount[]" id="product_discount_{{ $index }}" class="form-control product_discount" onkeyup="calculateTotal('{{$index}}')" value="0">
        </div>
    </td>            
    <td>
        <div class="form-group">
            <input type="text" name="product_discount_amt[]" id="product_discount_amt_{{ $index }}" class="form-control product_discount_amt" onkeyup="calculateTotal('{{$index}}')" value="0">
        </div>
    </td>     
    <td id="pd_rate_{{ $index }}">
        
    </td>
    <td>
            <input type="hidden" name="product_tax_rate[]" id="product_tax_rate_{{ $index }}" class="form-control product_tax_rate" value="0">
            <input type="hidden" name="product_gross_total[]" id="product_gross_total_{{ $index }}" class="form-control product_gross_total" value="0">

            <input type="hidden" name="product_cgst_perc[]" value="0" id="product_cgst_perc_{{$index}}">
            <input type="hidden" name="product_cgst[]" value="0" class="product_cgst" id="product_cgst_{{$index}}">
            <input type="hidden" name="product_sgst_perc[]" value="0" id="product_sgst_perc_{{$index}}">
            <input type="hidden" name="product_sgst[]" value="0" class="product_sgst" id="product_sgst_{{$index}}">
            <input type="hidden" name="product_total_gst[]" value="0" class="product_total_gst" id="product_total_gst_{{$index}}">


            <input type="hidden" name="product_taxable_value[]" id="product_taxable_value_{{ $index }}" class="form-control product_taxable_value" value="0">
            <input type="hidden" name="product_total_amt[]" id="product_total_amt_{{ $index }}" class="form-control product_total_amt" value="0">

        <button class="btn btn-danger" type="button" onclick="deleteProduct('{{ $index }}')">
            <i class="nav-icon bi bi-trash"></i>
        </button>
    </td>                   
</tr>