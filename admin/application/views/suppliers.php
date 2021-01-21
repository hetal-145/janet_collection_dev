
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Manage Suppliers</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Suppliers</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
        <a href="#" class="btn btn-primary pull-right add_suppliers" id="add_supplier" style="margin-top:30px;" data-toggle="modal" data-target=".mdl_supplier">Add Supplier</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">

                
                
                <div class="ibox-title">
                    <h5>Manage Suppliers</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    
                    <?php echo $content; ?>
                </div>
                
                <div class="modal fade mdl_supplier" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Add Brand </h4>
                            </div>

                            <div class="modal-body">
                                <form id="frm_add_supplier" name="frm_add_supplier" class="frm_add_supplier form-horizontal" enctype="multipart/form-data">
                                    <div class="panel-body">                                        

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Supplier Name(*)</b></span>
                                                <input type="text" class="form-control supplier_name" placeholder="Full Name" name="supplier_name">
                                            </div>
                                        </div> 
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Supplier Email(*)</b></span>
                                                <input type="email" class="form-control supplier_email" placeholder="Email ID" name="supplier_email">
                                            </div>
                                        </div> 
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Supplier Mobile No(*)</b></span>
                                                <input type="text" class="form-control supplier_mobileno" placeholder="Mobile no" name="supplier_mobileno">
                                            </div>
                                        </div> 
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Supplier Address</b></span>
                                                <textarea class="form-control supplier_address" placeholder="Address...." name="supplier_address" rows="5"></textarea>
                                            </div>
                                        </div>                                       

                                        <input type="hidden" class="supplier_id" name="supplier_id">
                                    </div>
                                </form>

                                <div class="clearfix"></div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="close_btn" data-dismiss="modal">Close</button>
                                <a class="btn submit_btn btn-primary pull-right">Save</a>
                                <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
                            </div>

                        </div>
                    </div>
                </div>
                
            
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        
        $("#add_supplier").click(function(){
            $('form[name = "frm_add_supplier"]')[0].reset();
        });
        
        $("#close_btn").click(function(){
            $('form[name = "frm_add_supplier"]')[0].reset();
        });
        
        //Add Data
        $('.submit_btn').click(function (e) {
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_supplier"]');
            
            //Name
            var supplier_name = frm.find('[name = "supplier_name"]').val();
            if (!supplier_name || !supplier_name.trim()) {
                frm.find('[name = "supplier_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter supplier name'));
                valid = false;
            } 
            
            //Email Id
            var supplier_email = frm.find('[name = "supplier_email"]').val();
            if (!supplier_email || !supplier_email.trim()) {
                frm.find('[name = "supplier_email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter supplier email id'));
                valid = false;
            } else if ( !validateEmail(supplier_email) ) {
                frm.find('[name = "supplier_email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter email in a valid format'));
                valid = false;
            }
            
            //Mobile No
            var supplier_mobileno = frm.find('[name = "supplier_mobileno"]').val();
            if (!supplier_mobileno || !supplier_mobileno.trim()) {
                frm.find('[name = "supplier_mobileno"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter supplier mobile no'));
                valid = false;
            } else if( !$.isNumeric(supplier_mobileno) ){
                frm.find('[name = "supplier_mobileno"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter numbers only.'));
                valid = false;
            }

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
                //console.log(data);
                $.ajax({
                    url: "suppliers/save",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);
                        if (resp == 'exist') {
                            frm.find('[name = "supplier_name"]').addClass('input_error').parents('.form-group').append(error_msg('Supplier already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }else{
                            if($("input[name='supplier_id']").val() != ''){
                                $('.mdl_supplier').modal('toggle');                                
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "suppliers";
                            }
                        }
                    }

                });
            }
        });
        
        $(document).on('click', '.edit_data', function () {
            var supplier_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'suppliers/get_suppliers',
                data: 'supplier_id=' + supplier_id,
                type: 'post',
                success: function (suppliers) {
                    if (suppliers) {
                        suppliers = JSON.parse(suppliers);
                        console.log(suppliers);
                        
                        $('[name="frm_add_supplier"]').populate(suppliers);
                    } else
                    {

                    }
                }
            });
        });
        
        function validateEmail($email) {
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            return emailReg.test( $email );
        }
        
    });    
</script>

