
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-7">
        <h2>Manage Sub Categories</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Sub Categories</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-5">
        <a href="#" class="btn btn-primary pull-right" id="add_sub_cat" style="margin-top:30px;" data-toggle="modal" data-target=".mdl_sub_category">Add Sub Category</a>
	<a href="sub_category/export_xls" style="margin-top:30px;" class="btn btn-info pull-left exportxls custombtn" data-type="sub_category">Export / Download Excel</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                
                <div class="ibox-title">
                    <h5>Manage Sub Categories</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <?php echo $content; ?>
                </div>
                
                <div class="modal fade mdl_sub_category" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
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

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Select Category </b></span>
                                                <select class="form-control parent_id" name="parent_id">
                                                    <option  value="">--Select--</option>
                                                    <?php foreach ($categories as $row) { ?>
                                                        <option  value="<?php echo $row['category_id']; ?>"> <?php echo $row['category_name']; ?></option>
                                                    <?php } ?>               
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Sub Category Name(*)</b></span>
                                                <input type="text" class="form-control category_name" placeholder="Category Name" name="category_name">
                                            </div>
                                        </div> 

                                        <div class="col-sm-12">                            
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Sub Category Image</b></span>
                                                <input type="file" class="form-control category_img" placeholder="Category Image"name="category_img">
                                                <br><a name="category_img_view" id="category_img_view"></a>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Want to add in loyalty club list?</b> </span>
                                                <select class="form-control in_loyalty_club" name="in_loyalty_club">
                                                    <option value="">--Select--</option>
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>       
                                                </select>
                                            </div>
                                        </div>

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
        
        $("#add_sub_cat").click(function(){
            $('form[name = "frm_add_category"]')[0].reset();
        });
        
        $("#close_btn").click(function(){
            $('form[name = "frm_add_category"]')[0].reset();
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
                console.log(data);
                $.ajax({
                    url: "sub_category/save",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        console.log(resp);
                        if (resp === 'exist') {
                            frm.find('[name = "category_name"]').addClass('input_error').parents('.form-group').append(error_msg('Category already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }else if (resp === 'success'){
                            if($("input[name='category_id']").val() != ''){
                                $('.mdl_sub_category').modal('toggle');                                
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "sub_category";
                            }
                        }
                    }

                });
            }
        });
        
        $(document).on('click', '.edit_data', function () {
            var category_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'sub_category/get_subcategory',
                data: 'category_id=' + category_id,
                type: 'post',
                success: function (category) {
                    if (category) {
                        category = JSON.parse(category);
                        console.log(category);
                        $('select[name^="parent_id"] option[value="'+category.parent_id+'"]').attr("selected","selected");
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
