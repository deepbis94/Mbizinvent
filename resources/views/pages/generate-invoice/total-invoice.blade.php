<tbody>
    <tr>
        <td id="total_quantity">0</td>
        <td>
            <input type="text" name="total_discount" id="total_discount" class="form-control" onkeyup="calcGroupTotal()" value="0">            
        </td>
        <td>
            <input type="text" name="total_discount_amt" id="total_discount_amt" class="form-control" onkeyup="calcGroupTotal()" value="0">            
        </td>        
        <td id="total_taxable_value">0</td>
        <td>
            <input type="text" name="total_round_off" id="total_round_off" class="form-control" onkeyup="calcGroupTotal()" value="0">
        </td>
        <td id="total_grand">0</td>
    </tr>
</tbody>
<input type="hidden" name="total_quantity" id="total_quantity_h" value="0">
<input type="hidden" name="total_rate" id="total_rate_h" value="0">
<input type="hidden" name="total_gross_sum" id="total_gross_sum_h" value="0">
<input type="hidden" name="total_taxable_value" id="total_taxable_value_h" value="0">
<input type="hidden" name="total_gst" id="total_gst" value="0">

<input type="hidden" name="total_cgst" id="total_cgst_h" value="0">
<input type="hidden" name="total_cgst_perc" id="total_cgst_perc_h" value="0">
<input type="hidden" name="total_sgst" id="total_sgst_h" value="0">
<input type="hidden" name="total_sgst_perc" id="total_sgst_perc_h" value="0">
<input type="hidden" name="total_grand" id="total_grand_h" value="0">