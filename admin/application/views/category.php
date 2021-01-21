
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Manage Categories</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Categories</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
        <a href="#" style="margin-top:30px;" id="add_category" class="btn btn-primary pull-right" data-toggle="modal" data-target=".mdl_category">Add Category</a>
	<a href="category/export_xls" style="margin-top:30px;" class="btn btn-info pull-left exportxls custombtn" data-type="category">Export / Download Excel</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                
                <div class="ibox-title">
                    <h5>Manage Categories</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">                    
                    <?php echo $content; ?>
                </div>
                
                <div class="modal fade mdl_category" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Add Category </h4>
                            </div>

                            <div class="modal-body">
                                <form id="frm_add_category" name="frm_add_category" class="frm_add_category form-horizontal" enctype="multipart/form-data">
                                    <div class="panel-body">                                        

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Category Name(*)</b></span>
                                                <input type="text" class="form-control category_name" placeholder="Category Name" name="category_name">
                                            </div>
                                        </div> 

                                        <div class="col-sm-4">                            
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Category Image</b></span>
                                                <input type="file" class="form-control category_img" placeholder="Category Image"name="category_img">
                                                <br><a name="category_img_view" id="category_img_view"></a>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <span class="help-block select2 m-b-none"><b>Want to add in loyalty club list?</b> </span>
                                                <select class="form-control in_loyalty_club" name="in_loyalty_club">
                                                    <option value="">--Select--</option>
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>       
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12 want_to_add_checkbox_div">                            
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Want to add a Sub Category?</b>&nbsp;&nbsp;<input type="checkbox" class="want_to_add_checkbox" name="want_to_add_checkbox"></span>
                                            </div>
                                        </div>                                        
                                        
                                        <div class="col-sm-6 sub_cat_block">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Category Name</b></span>
                                                <input readonly="readonly" type="text" id="add_category_name" class="form-control" placeholder="Category Name">
                                            </div>
                                        </div> 
                                        
                                        <div class="col-sm-6 sub_cat_block">
                                            <div class="form-group">
                                                <span class="select2 help-block m-b-none"><b>No of Sub Categories you want to add</b> </span>
                                                <select class="form-control no_of_categories" name="no_of_categories">
                                                    <option value="">--Select--</option>
                                                    <?php for($i=1; $i<=4; $i++) { ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div> 
                                        
                                        <div class="homediv"></div>

                                        <input type="hidden" class="category_id" name="category_id">
                                        
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
        
        $(".sub_cat_block").hide();
        
        $(".want_to_add_checkbox").click(function (e) {            
            if($(this).is(":checked")) {
                $(".sub_cat_block").show();
            } 
            else {
                $(".sub_cat_block").hide();
            } 
        });
        
        $("#add_category").click(function(){
            $('form[name = "frm_add_category"]')[0].reset();
        });
        
        $("#close_btn").click(function(){
            $('form[name = "frm_add_category"]')[0].reset();
        });
        
        //show category name
        $(".category_name").on('input', function(e){
            $("#add_category_name").val($(this).val());
        });
        
        //add sub category box
        $(".no_of_categories").change(function (e){            
            var nos = $(this).val();
            $(".homediv").html('');
            
            for(var i = 1; i <= nos; i++) {
                $(".homediv").append('<div class="col-sm-6 sub_cat_block"><div class="form-group"><span class="help-block m-b-none"><b>Sub Category Name '+ i +'</b></span> <input type="text" class="form-control sub_category_name_'+ i +'" placeholder="Sub Category Name" name="sub_category_name_'+ i +'"></div></div><div class="col-sm-6 sub_cat_block"><div class="form-group"><span class="help-block m-b-none"><b>Sub Category Image '+ i +'</b></span> <input type="file" class="form-control sub_category_img_'+ i +'" placeholder="Sub Category Image"name="sub_category_img_'+ i +'"></div></div>');
            }            
        });
        
        //Add Data
        $('.submit_btn').click(function (e) {
            e.preventDefault();
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_category"]');
            
            //Category
            var category_name = frm.find('[name = "category_name"]').val();
            if (!category_name || !category_name.trim()) {
                frm.find('[name = "category_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter category name'));
                valid = false;
            }                                

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
                //console.log(data);
                $.ajax({
                    url: "category/save",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);
                        if (resp === 'exist') {
                            frm.find('[name = "category_name"]').addClass('input_error').parents('.form-group').append(error_msg('Category already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }else if (resp === 'success'){
                            if($("input[name='category_id']").val() != ''){
                                $('.mdl_category').modal('toggle');                                
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "category";
                            }
                        }
                    }

                });
            }
        });
        
        $(document).on('click', '.edit_data', function () {
            
            $(".sub_cat_block").hide();
            $(".want_to_add_checkbox_div").hide();
            
            var category_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'category/get_category',
                data: 'category_id=' + category_id,
                type: 'post',
                success: function (category) {
                    if (category) {
                        category = JSON.parse(category);
                        //console.log(category);
                        $('#category_img_view').html(category.category_img);
                        $('#category_img_view').attr('href', '<?php echo S3_PATH; ?>category/'+category.category_img);
                        $('[name="frm_add_category"]').populate(category);
                    } else
                    {

                    }
                }
            });
        });
        
    });    
</script>
