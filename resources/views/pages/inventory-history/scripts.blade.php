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
            console.log(url);
            
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

    function paginate(){
        let perPage = $("#perPage").val();
        let currentPage = $("#currentPage").val();

        $("#hiTable").hide();
        $(".loader-holder").show();

        let url = '{{ route('inventoryHistory') }}';
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
                        searchString: $("#searchHistory").val()
                    },  
                    success: function(response){
                        $("#hiBody").html(response)
                        $("#hiTable").show();
                        $(".loader-holder").hide();

                    },
                    error: function(ev){
                        
                    }
                });            
        
    }
</script>