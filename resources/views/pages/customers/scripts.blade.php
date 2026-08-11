<script>
    function editCustomer(event){
        let url = event.target.dataset.url;
        let updateurl = event.target.dataset.storeurl;
        
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
                $("#modalTitle").html('Edit Customer');
                $("#addEditModal").modal('show');

                validateAndSubmitFormFields(true,updateurl);
        
            },
            error: function(err){

            }
        });
        
    }

    function showAddCustomerModal(){
        let url = '{{ route('customers.create') }}';
        let storeurl = '{{ route('customers.store') }}'

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
                $("#modalTitle").html('Add Customer');
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
            console.log(url);
            
            let values = $("#addEditForm").serializeArray();  
            let isFormValid = true;
            let errors = [];
            // values.some((v)=>{
                
            //     if(v.value.length == 0){
            //         errors.push({
            //             key: v.name,
            //             error: `${v.name} is required`
            //         });
            //         isFormValid = false
            //     }                    
            // });

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
        // if(elem.val().length > 0){
        //     elem.removeClass('is-invalid');
        //     errorDiv.addClass('d-none');
        // }
        // else{
        //     elem.addClass('is-invalid');
        //     errorDiv.removeClass('d-none');
        //     errorDiv.html(`${id} is required`)
        // }
    }

    function deleteCustomer(event){
        let status = confirm('Are you sure, you want to delete the customer?')
        if(status){
            let url = event.target.dataset.url;
            $.ajax({
            url: url, // The URL defined in your routes
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ), // CSRF Token
            },
            success: function (response) {
                if(response.success){
                    alert('Customer Deleted Successfully');
                    location.reload();
                }
            },
            error: function(err){

            }
        });

        }
        
    }

    function exportCustomers(){
        let url = '{{ route('exportCustomers') }}';
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

    function importCustomers(){
        $("#csv-file").click();                    
    }

    function uploadBulk(){
        var fileInput = $('#csv-file')[0];
            
            var file = fileInput.files[0];
            var formData = new FormData();

                formData.append('file_csv', file);

                // Send the file to the server using AJAX
                $.ajax({
                    url: "{{ route('importCustomers') }}",
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

    function paginate(){
        let perPage = $("#perPage").val();
        let currentPage = $("#currentPage").val();

        $("#custTable").hide();
        $(".loader-holder").show();

        let url = '{{ route('customers.index') }}';
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
                        currentPage:currentPage
                    },  
                    success: function(response){
                        $("#ctBody").html(response)
                        $("#custTable").show();
                        $(".loader-holder").hide();

                    },
                    error: function(ev){
                        
                    }
                });            
        
    }

    function searchCustomer(){
        $("#custTable").hide();
        $(".loader-holder").show();

        let searchString = $("#searchCustInput").val();
        

        let url = '{{ route('customers.index') }}';
        $.ajax({
                    url:url,
                    type: 'GET',
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ), // CSRF Token
                    },                      
                    data:{
                        searchString:searchString
                    },  
                    success: function(response){
                        $("#ctBody").html(response)
                        $("#custTable").show();
                        $(".loader-holder").hide();

                    },
                    error: function(ev){
                        
                    }
                });              
    }

</script>
