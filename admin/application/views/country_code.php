
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Countries</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Countries</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">

                
                
                <div class="ibox-title">
                    <h5>Manage Countries</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <!--<a href="#" class="btn btn-primary pull-right add_brand" data-toggle="modal" data-target=".mdl_brand">Add Brand</a>-->
                    <span class="clearfix"></span>
                    <?php echo $content; ?>
                </div>
                
                <div class="modal fade mdl_brand" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Add Brand </h4>
                            </div>

                            <div class="modal-body">
                                <form id="frm_add_brand" name="frm_add_brand" class="frm_add_brand form-horizontal" enctype="multipart/form-data">
                                    <div class="panel-body">
                                        
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Category</b></span>
                                                <select class="form-control category_id" name="category_id">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach($categories as $category) { ?>
                                                        <option value="<?php echo $category["category_id"]; ?>"><?php echo $category["category"]; ?></option>
                                                    <?php } ?>                                
                                                </select>                                                
                                            </div>
                                        </div> 

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Brand Name(*)</b></span>
                                                <input type="text" class="form-control brand_name" placeholder="Brand name" name="brand_name">
                                            </div>
                                        </div> 

                                        <div class="col-sm-12">                            
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Brand Logo</b></span>
                                                <input type="file" class="form-control brand_logo" placeholder="Brand logo"name="brand_logo">
                                                <br><a name="brand_logo_view" id="brand_logo_view"></a>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12">                            
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Top Brand?</b></span>
                                                <input type="checkbox" class="is_top_brand_checkbox" name="is_top_brand">
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12 slider_img_block">                            
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Slider Image</b></span>
                                                <input type="file" class="form-control slider_img" placeholder="Slider image"name="slider_img">
                                                <br><a name="slider_img_view" id="slider_img_view"></a>
                                            </div>
                                        </div>

                                        <input type="hidden" class="brand_id" name="brand_id">
                                    </div>
                                </form>

                                <div class="clearfix"></div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        
        $(".slider_img_block").hide();
        
        $(".ibox-content a.add_brand").click(function(e){
            $(".brand_name").val('');
            $("#slider_img_view").text('');
            $("#brand_logo_view").text('');
            $(".brand_id").val('');
            $('.category_id option:selected').attr('selectedIndex',0);
            $( ".is_top_brand_checkbox" ).attr( 'checked', false );
        });
        
        $(".is_top_brand_checkbox").click(function (e) {            
            if($(this).is(":checked")) {
                $(".slider_img_block").show();
            } 
            else {
                $(".slider_img_block").hide();
            } 
        });
        
        $(".is_top_brand_checkbox_view").click(function (e) {
            
            var brand_id; var tb_status;
            
            if($(this).is(":checked")) {
                $(".slider_img_block").show();
                brand_id = $(this).data('brand_id');
                tb_status = 1;
            } 
            else {
                $(".slider_img_block").hide();
                brand_id = $(this).data('brand_id');
                tb_status = 0;
            } 
            
            $.ajax({
                url: 'brand/top_Countries',
                data: 'brand_id=' + brand_id + '&tb_status=' + tb_status,
                type: 'post',
                success: function () {}
            });
        });
        
        //Add Data
        $('.submit_btn').click(function (e) {
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_brand"]');
            
            //Brand
            var brand_name = frm.find('[name = "brand_name"]').val();
            if (!brand_name || !brand_name.trim()) {
                frm.find('[name = "brand_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter brand name'));
                valid = false;
            } 

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
                //console.log(data);
                $.ajax({
                    url: "brand/save",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        console.log(resp);
                        if (resp == 'exist') {
                            frm.find('[name = "brand_name"]').addClass('input_error').parents('.form-group').append(error_msg('Brand already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }else{
                            window.location = "brand";
                        }
                    }

                });
            }
        });
        
        $(document).on('click', '.edit_data', function () {
            var brand_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'brand/get_brand',
                data: 'brand_id=' + brand_id,
                type: 'post',
                success: function (brand) {
                    if (brand) {
                        brand = JSON.parse(brand);
                        console.log(brand);
                        $('#brand_logo_view').html(brand.brand_logo);
                        $('#brand_logo_view').attr('href', '../upload/brand/'+brand.brand_logo);
                        
                        $('#slider_img_view').html(brand.slider_img);
                        $('#slider_img_view').attr('href', '../upload/brand/'+brand.slider_img);
                        
                        if(brand.is_top_brand == '1'){
                            $( ".is_top_brand_checkbox" ).attr( 'checked', 'checked' );
                            $(".slider_img_block").show();
                        } else if(brand.is_top_brand == '0') {
                            $( ".is_top_brand_checkbox" ).attr( 'checked', false );
                            $(".slider_img_block").hide();
                        }
                        
                        $('[name="frm_add_brand"]').populate(brand);
                    } else
                    {

                    }
                }
            });
        });
        
    });    
</script>

