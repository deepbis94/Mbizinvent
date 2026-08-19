<script>
    function showAddInventoryModal(){
        let url = '{{ route('inventory.create') }}';
        let storeurl = '{{ route('inventory.store') }}'

        $.ajax({
            url: url, // The URL defined in your routes
            type: "GET",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ), // CSRF Token
            },
            success: function (response) {
                $("#addEditModalBody").html(response)
                $("#modalTitle").html('Add Inventory');
                $("#addEditModal").modal('show');

                validateAndSubmitFormFields(false,storeurl);
                
            },
            error: function(err){

            }
        });
    }      
     
    function validateAndSubmitFormFields(isEdit,url){
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

                $.ajax({
                    url:url,
                    type: isEdit ? 'PUT':'POST',
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
            
            // console.log('submmited',values);
                    
                    
        });

    }        

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
            errorDiv.html(`${id} is required`)
        }
    }        

    function exportInventory(){
        let url = '{{ route('exportInventory') }}';
        $.ajax({
                    url:url,
                    type: 'GET',
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ), // CSRF Token
                    },                        
                    success: function(response){
                        window.location.href=response.url_path                            
                    },
                    error: function(ev){
                        
                    }
                });            
        
    }

    function importInventory(){
        $("#csv-file").click();                    
    }

    function uploadBulk(){
        var fileInput = $('#csv-file')[0];
            
            var file = fileInput.files[0];
            var formData = new FormData();

                formData.append('file_csv', file);

                // Send the file to the server using AJAX
                $.ajax({
                    url: "{{ route('importInventory') }}",
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
                        if (response.success) {
                            alert('Successfully updated')
                        //     Swal.fire("Uploaded!", 'Successfully Uploaded!', "success");
                            location.reload();                            
                        } else if (response.error) {
                            alert(response.error)
                        }
                    },
                    error: function(xhr, status, error) {
                        alert(error)
                    }
                });        

    }

    function filterInventory(){
        let q = $("#searchInventory").val().toLowerCase();
        $("table tbody tr").each(function(){
            $(this).toggle($(this).text().toLowerCase().indexOf(q) !== -1);
        });
    }

</script>
