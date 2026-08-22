<script>

    var productCount = 0;
    var invProducts = new Set();
    var isValidForm = true;
    var setting_profit = 0;

    function addProduct(){
        invProducts.add(++productCount)   
                             
        
        let url = '{{ route('addSingleProduct') }}';
        $.ajax({
            url: url, // The URL defined in your routes
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ), // CSRF Token
            },
            data:{
                productCount:invProducts.size
            },
            success: function (response) {
                $("#invoice_product_tbody").append(response)
                $('.sel2input').select2();
                // $("#addEditModalBody").html(response)
                // $("#modalTitle").html('Add Inventory');
                // $("#addEditModal").modal('show');

                // validateAndSubmitFormFields(false,storeurl);
                
            },
            error: function(err){

            }
        });            
    }

    function populateValues(id,event){
        
        let url = '{{ route('getProductInfo') }}';
        $.ajax({
            url: url, // The URL defined in your routes
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ), // CSRF Token
            },
            data:{
                id:event.target.value,
            },
            success: function (response) {
                if(response.success){
                    // $(`#product_rate_${id}`).val(response.data.rate)
                    $(`#max_qty_${id}`).html('Max Qty: '+response.data.stock.available_stock);
                    $(`#max_qty_f_${id}`).val(response.data.stock.available_stock);
                    $(`#gst_percentage_${id}`).val(response.data.gst_percentage);

                    setting_profit = response.profit
                }                    
            },
            error: function(err){

            }
        });                    
    }

    function deleteProduct(id){
        $("#product_"+id).remove();
        invProducts.delete(id)
    }

    function calculateTotal(index){

        
        let product_qty = parseFloat($(`#product_qty_${index}`).val());
        let max_qty = parseFloat($(`#max_qty_f_${index}`).val());
        if (!isNaN(max_qty) && product_qty > max_qty) {
            alert('Qty cannot exceed available stock (' + max_qty + ')');
            $(`#product_qty_${index}`).val(max_qty);
            product_qty = max_qty;
        }
        let product_mrp = parseFloat($(`#product_mrp_${index}`).val());
        let product_profit = parseFloat($(`#product_profit_${index}`).val());
        let product_discount = parseFloat($(`#product_discount_${index}`).val());
        let product_discount_amt = parseFloat($(`#product_discount_amt_${index}`).val());
        let gst_percentage = parseFloat($(`#gst_percentage_${index}`).val());


        let prod_amt = product_mrp / setting_profit // 1.16
        

        prod_amt = prod_amt / product_profit; // 1.06
        



        prod_amt *= product_qty


        let finalAmt = prod_amt
        
        
        // let disc = (100+product_discount) / 100
        finalAmt = prod_amt / (1+(product_discount/100))
        $(`#product_gross_total_${index}`).val(finalAmt);

        finalAmt -= product_discount_amt
        
        $(`#product_rate_${index}`).val((finalAmt/product_qty));
        $(`#pd_rate_${index}`).html((finalAmt/product_qty))


        let taxable_value = finalAmt;
        $(`#product_taxable_value_${index}`).val(taxable_value);

        gst_perc = (100 + gst_percentage) / 100
        let withgst = (gst_percentage / 100) * finalAmt
        $(`#product_total_gst_${index}`).val(withgst);

        // finalAmt = finalAmt / gst_perc

        

        
        $(`#product_tax_rate_${index}`).val(gst_percentage);

        $("#product_cgst_perc_"+index).val(gst_percentage/2);
        $("#product_sgst_perc_"+index).val(gst_percentage/2);
        $("#product_cgst_"+index).val((withgst/2));
        $("#product_sgst_"+index).val((withgst/2));
        $("#product_sgst_"+index).val((withgst/2));

        $(`#product_total_amt_${index}`).val(finalAmt);

        // console.log(setting_profit);
        


        // let product_rate = $(`#product_rate_${index}`).val();
        // let product_gross_total = $(`#product_gross_total_${index}`).val();
        // let product_discount = $(`#product_discount_${index}`).val();
        // let product_taxable_value = $(`#product_taxable_value_${index}`).val();
        // let product_tax_rate = $(`#product_tax_rate_${index}`).val();
        // let max_qty = $(`#max_qty_f_${index}`).val();


        // product_qty = product_qty.length > 0 ? parseFloat(product_qty) : 0.00
        

        // product_rate = product_rate.length > 0 ? parseFloat(product_rate) : 0.00
        // product_gross_total = product_gross_total.length > 0 ? parseFloat(product_gross_total) : 0.00
        // product_discount = product_discount.length > 0 ? parseFloat(product_discount) : 0.00
        // product_taxable_value = product_taxable_value.length > 0 ? parseFloat(product_taxable_value) : 0.00
        // product_tax_rate = product_tax_rate.length > 0 ? parseFloat(product_tax_rate) : 0.00
        // max_qty = max_qty.length > 0 ? parseFloat(max_qty) : 0.00

        // if(product_qty < max_qty){
            
        //     isValidForm = true
        // }
        // else{
        //     isValidForm = false
        // }


        // let grosstotal = (product_qty*product_rate)
        
        // let taxableValue = (grosstotal-product_discount)

        // let addTax = (product_tax_rate / 100) * taxableValue;
        // $("#product_cgst_perc_"+index).val(product_tax_rate/2)
        // $("#product_sgst_perc_"+index).val(product_tax_rate/2)
        // $("#product_cgst_"+index).val((addTax/2))
        // $("#product_sgst_"+index).val((addTax/2))

        // let grandTotal = taxableValue + ((product_tax_rate / 100) * taxableValue) 
        
        // $(`#product_gross_total_${index}`).val(grosstotal);
        // $(`#product_taxable_value_${index}`).val(taxableValue)
        // $(`#product_total_amt_${index}`).val(grandTotal);

        calcGroupTotal()
    }

    function calcGroupTotal() {
        let qtys = document.querySelectorAll('.product_qty');
        $("#total_quantity").html(calcSingle(qtys))
        $("#total_quantity_h").val(calcSingle(qtys))

        let total_discount = parseFloat($("#total_discount").val());
        let total_discount_amt = parseFloat($("#total_discount_amt").val());

        // let discounts = document.querySelectorAll('.product_discount');
        // $("#total_discount").html(calcSingle(discounts))      
        // $("#total_discount_h").val(calcSingle(discounts))            

        let taxable_values = document.querySelectorAll('.product_taxable_value');
        let total_taxable_value=calcSingle(taxable_values);

        $("#total_taxable_value").html(calcSingle(taxable_values))  
        $("#total_taxable_value_h").val(calcSingle(taxable_values))            

        let product_gross_totals = document.querySelectorAll('.product_gross_total');
        let gross_total=calcSingle(product_gross_totals);
        $("#total_gross_sum_h").val(gross_total);


        let tax_rates = document.querySelectorAll('.product_tax_rate');
        $("#total_cgst_perc_h").val((calcSingle(tax_rates)/2));

        $("#total_sgst_perc_h").val((calcSingle(tax_rates)/2));
        
        let product_cgst = document.querySelectorAll('.product_cgst');
        $("#total_cgst_h").val(calcSingle(product_cgst));

        let product_sgst = document.querySelectorAll('.product_sgst');
        $("#total_sgst_h").val(calcSingle(product_sgst));

        
        let product_total_amts = document.querySelectorAll('.product_total_amt');


        let total_amts = calcSingle(product_total_amts);
        
        total_amts -= ((total_discount / 100) * total_amts);
        total_amts -= total_discount_amt


        $("#total_taxable_value").html(total_amts);  
        $("#total_taxable_value_h").val(total_amts);            


        let total_round_off = $("#total_round_off").val();
        let round_off = 0;
        round_off = total_round_off.length > 0 ? parseFloat(total_round_off) : 0.00

        total_amts += round_off

        let product_total_gst = document.querySelectorAll('.product_total_gst');
        let total_gst=parseFloat(calcSingle(product_total_gst));

        $("#total_gst").val(calcSingle(product_total_gst));

        total_amts += total_gst;

        // total_amts -= taxDisc
        

        $("#total_grand").html(total_amts);            
        $("#total_grand_h").val(total_amts);            
        


    }

    function calcSingle(entity) {
        let total = 0;
        entity.forEach((v)=>{
            total += v.value.length > 0 ? parseFloat(v.value) : 0.00;  
        })
        return total
    }

    function generateInvoice() {
        if(!isValidForm){
            alert('Invalid Form. Please check the inputs');
            return;
        }

            let fData = new FormData($("#invForm")[0]);
            // let values = $("#invForm").serialize();
            
        let url = '{{ route('generateInvoice') }}';
        $.ajax({
            url: url, // The URL defined in your routes
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ), // CSRF Token
            },
            processData: false,  // Important for FormData
            contentType: false,  // Important for FormData            
            data:fData,
            success: function (response) {

                $("#invForm")[0].reset();
                $("#total_quantity").html(0)
                $("#total_quantity_h").html(0)
                $("#total_rate").html(0)
                $("#total_rate_h").html(0)
                $("#total_gross_sum").html(0)
                $("#total_gross_sum_h").html(0)
                $("#total_discount").html(0)
                $("#total_discount_h").html(0)
                $("#total_taxable_value").html(0)
                $("#total_taxable_value_h").html(0)
                $("#total_cgst_perc").html(0)
                $("#total_cgst_perc_h").html(0)
                $("#total_sgst_perc").html(0)
                $("#total_sgst_perc_h").html(0)
                $("#total_cgst").html(0)
                $("#total_cgst_h").html(0)
                $("#total_sgst").html(0)
                $("#total_sgst_h").html(0)
                $("#total_grand").html(0)
                $("#total_grand_h").html(0)
                document.querySelectorAll('.invoiceProducts').forEach(el => el.remove());
                invProducts.clear();
                productCount = 0
                window.location.href = response.zipUrl;  
                
                setTimeout(function(){
                    window.location.reload()
                },500);
                // location.reload()

            },
            error: function(err){
                let msg = err.responseJSON && (err.responseJSON.message || (err.responseJSON.errors && Object.values(err.responseJSON.errors).flat().join('\n')));
                alert(msg || 'Could not generate invoice.');
            }
        });                            
    }
    
    function uploadInvoice(){
        $("#csv-file").click();                    
    }

    function uploadBulk(){
        var fileInput = $('#csv-file')[0];
            
            var file = fileInput.files[0];
            var formData = new FormData();

                formData.append('file_csv', file);
                $("#uploadinv").css('display','none');
                $("#uploading").css('display','block');
                // Send the file to the server using AJAX
                $.ajax({
                    url: "{{ route('geninv') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ), // CSRF Token
                    },
                    processData: false, // Don't process the data
                    contentType: false, // Don't set content-type header
                    success: function(response) {
                        window.location.href = response.zipUrl;   
                        $("#uploadinv").css('display','block');
                        $("#uploading").css('display','none');  
                        setTimeout(() => {
                            location.reload();
                        }, 1000);                      
                    },
                    error: function(xhr, status, error) {
                        alert(error)
                        $("#uploadinv").css('display','block');
                        $("#uploading").css('display','none');                        
                        
                    }
                });             
    }    
    
    function downloadSample(){
        const url = '{{ route('downloadSample') }}';
        const fileName = 'sample_invoice.csv';

    // Create a hidden link and simulate clicking it to download
    const link = document.createElement('a');
    link.href = url;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    }

</script>
