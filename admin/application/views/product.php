<style>
    #progress-wrp {
	border: 1px solid #0099CC;
	padding: 1px;
	position: relative;
	border-radius: 3px;
	margin: 10px;
	text-align: left;
	background: #fff;
	box-shadow: inset 1px 3px 6px rgba(0, 0, 0, 0.12);
	height:24px;
    }
    #progress-wrp .progress-bar{
	height: 20px;
	border-radius: 3px;
	background-color: #28a745;
	width: 0;
	box-shadow: inset 1px 1px 10px rgba(0, 0, 0, 0.11);
    }
    #progress-wrp .status{
	top:3px;
	left:50%;
	position:absolute;
	display:inline-block;
	color: #000000;
    }
</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Products</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Products</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">                
                
                <div class="ibox-title">
                    <h5>Manage Products</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">                    
                    <a href="#" class="btn btn-primary pull-left importxl custombtn" data-toggle="modal" data-target=".mdl_importxl">Import Excel</a>
		    
		    <a href="product/export_xls" class="btn btn-info pull-left exportxls custombtn" data-type="product">Export / Download Excel</a>
		    
                    <a href="#" class="btn btn-success pull-left addproduct custombtn" data-toggle="modal" data-target=".mdl_product">Add Product</a>
		    
		    <a href="#" class="btn btn-warning pull-left bluk_upload custombtn" data-toggle="modal" data-target=".mdl_bluk_upload">Bulk Upload</a>
		    
		    <a href="#" class="btn btn-primary pull-left selectall custombtn">Select All Products In Page</a>
		    <a href="#" class="btn btn-info pull-left deselectall custombtn">Deselect All Products In Page</a>
		    <a href="#" class="btn btn-warning pull-left edit_all custombtn">Edit Price For Selected Products</a>
		    <a href="#" class="btn btn-success pull-left active_all custombtn">Activate Selected</a>
		    <a href="#" class="btn btn-danger pull-left deactive_all custombtn">Deactivate Selected</a>

                    <span class="clearfix"></span>
                    <?php echo $content; ?>
                </div>
                
                <div class="modal fade mdl_product" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close closebtn" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Add Product </h4>
                            </div>

                            <div class="modal-body">
                                <form name="frm_add_product" class="frm_add_product form-horizontal" enctype="multipart/form-data">
                                    <div class="panel-body">
                                        <div class="homediv">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Category(*)</b> </span>
                                                    <select class="form-control category_id" name="category_id">
                                                        <option value="0">--Select--</option>
                                                        <?php foreach($categories as $category) { ?>
                                                            <option value="<?php echo $category["category_id"]; ?>"><?php echo $category["category_name"]; ?></option>
                                                        <?php } ?>                                
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Sub Category</b> </span>
                                                    <select class="form-control sub_category_id" name="sub_category_id">
                                                        <option value="0">--Select--</option>     
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Brand(*)</b> </span>
                                                    <select class="form-control brand_id" name="brand_id">
                                                        <option value="0">--Select--</option>      
                                                    </select>
                                                </div>
                                            </div> 
                                        </div>
                                        
                                        <div class="row">                                            
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Supplier(*)</b> </span>
                                                    <select class="form-control supplier_id" name="supplier_id">
                                                        <option value="0">--Select--</option>
                                                        <?php foreach($suppliers as $supplier) { ?>
                                                            <option value="<?php echo $supplier["supplier_id"]; ?>"><?php echo $supplier["supplier_name"]; ?></option>
                                                        <?php } ?>                                
                                                    </select>
                                                </div>
                                            </div>  
                                            
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Seller(*)</b> </span>
                                                    <select class="form-control seller_id" name="seller_id">
                                                        <option value="0">--Select--</option>
                                                        <?php foreach($sellers as $sell) { ?>
                                                            <option value="<?php echo $sell["seller_id"]; ?>"><?php echo $sell["seller_name"]; ?></option>
                                                        <?php } ?>                                
                                                    </select>
                                                </div>
                                            </div>  
                                        
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Product Name(*)</b></span>
                                                    <input type="text" class="form-control product_name" placeholder="Product Name" name="product_name">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Country(*)</b> </span>
                                                    <?php /*
						    <select class="form-control country_id" name="country_id">
                                                        <option value="">--Select--</option>
                                                        <?php foreach($countries as $country) { ?>
                                                            <option value="<?php echo $country["country_id"]; ?>"><?php echo $country["name"]; ?></option>
                                                        <?php } ?>                                
                                                    </select>
						     * 
						     */ ?>
						    <input type="text" class="form-control country_id" placeholder="Country Name" name="country_id">
                                                </div>
                                            </div>
                                            
                                            <div class="col-sm-4">                            
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Product Image</b></span>
                                                    <input type="file" class="form-control feature_img" placeholder="Product Image"name="feature_img">
                                                    <div id="product_img"></div>                                                    
                                                </div>
                                            </div>                            

                                            <div class="col-sm-4">                            
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Product Gallery Image</b></span>
                                                    <input type="file" multiple="multiple" class="form-control pgallery" placeholder="Product Gallery" name="pgallery[]">
                                                    <div id="gimgs"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Product Description</b></span>
                                                    <textarea class="form-control description" placeholder="Description..." name="description" rows="4"></textarea>
                                                </div>
                                            </div> 
                                        </div>
                                        
                                        <div class="row">                                            
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>No of Days to Return Product</b> </span>
                                                        <input type="text" class="form-control no_of_return_days numeric" placeholder="5" name="no_of_return_days">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Drink Type (*)</b> </span>
                                                    <select class="form-control drink_type" name="drink_type">
                                                        <option value="1">Alcoholic</option>
                                                        <option value="2">Non - Alcoholic</option>       
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group abv_per" id="abv_per">
                                                    <span class="help-block m-b-none"><b>ABV Perentage (%)</b> </span>
                                                        <input type="text" class="form-control abv_percent numeric" placeholder="40" name="abv_percent">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group abv_per" id="abv_per">
                                                    <span class="help-block m-b-none"><b>Units</b> </span>
                                                        <input type="text" class="form-control alchol_units numeric" placeholder="20" name="alchol_units">
                                                </div>
                                            </div>
                                        </div>
                                            
                                        <div class="row">                                            
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Want to add in top pick list?</b> </span>
                                                    <select class="form-control top_pick" name="top_pick">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>       
                                                    </select>
                                                </div>
                                            </div>
<!--                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Want to add in loyalty club list?</b> </span>
                                                    <select class="form-control in_loyalty_club" name="in_loyalty_club">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>       
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Want to add in vip club list?</b> </span>
                                                    <select class="form-control in_vip_club" name="in_vip_club">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>       
                                                    </select>
                                                </div>
                                            </div>-->
                                        </div>
                                            
                                        <div class="row">                                            
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <a href="#" class="btn btn-primary pull-right addvolume" name="addvolume">Add Volume</a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        </div>
                                        
                                        <input type="hidden" class="product_id" name="product_id">
                                        <input type="hidden" class="count_vol_div" name="count_vol_div">
                                        <input type="hidden" class="action" name="action" value="add">
                                    </div>
                                </form>

                                <div class="clearfix"></div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary closebtn" data-dismiss="modal">Close</button>
                                <a class="btn submit_btn btn-primary pull-right">Save</a>
                                <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
                            </div>

                        </div>
                    </div>
                </div>
                
                <div class="modal fade mdl_importxl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Import CSV </h4>
                            </div>

                            <div class="modal-body">
                                <form name="frm_import_xls" class="frm_import_xls form-horizontal" enctype="multipart/form-data">
                                    <div class="panel-body">
                                        
                                        <div class="row">
                                            <div class="col-sm-12">                            
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Upload File</b></span>
                                                    <input type="file" class="form-control import_file_nm" name="import_file_nm">
                                                </div>
                                            </div>   
                                        </div>
                                    </div>
                                </form>

                                <div class="clearfix"></div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <a class="btn upload_importxl btn-primary pull-right">Save</a>
                                <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
                            </div>

                        </div>
                    </div>
                </div>
		
		<div class="modal fade mdl_bluk_upload" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Bulk Upload </h4>
                            </div>

                            <div class="modal-body">
                                <form name="frm_bluk_upload" class="frm_bluk_upload form-horizontal" enctype="multipart/form-data">
                                    <div class="panel-body">
                                        
                                        <div class="row">
                                            <div class="col-sm-12">                            
                                                <div class="form-group">
                                                    <span class="help-block m-b-none"><b>Upload Images ( Multiple )</b></span>
                                                    <input multiple="multiple" type="file" class="form-control import_image_nm" name="import_image_nm[]">
                                                </div>
                                            </div>   
                                        </div>
					<div id="progress-wrp"><div class="progress-bar"></div ><div class="status">0%</div></div>
                                    </div>
                                </form>

                                <div class="clearfix"></div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <a class="btn upload_bulk_img btn-primary pull-right">Save</a>
                                <span class="success_msg11 pull-right" style="color:green; padding: 7px;"></span>
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
	
	//select all
	$(".selectall").on("click", function(e){
	    e.preventDefault();	    
	    $('.select_multiple_checkbox').prop("checked", true);
	});
	
	//deselect all
	$(".deselectall").on("click", function(e){
	    e.preventDefault();	    
	    $('.select_multiple_checkbox').prop("checked", false);
	});
	
	//edit all
	$(".edit_all").on("click", function(e){
	    var selected = [];
	    $(".select_multiple_checkbox:checkbox:checked").each(function() {
		selected.push($(this).val());
	    });
	    
	    if(selected == '') {
		alert('Please select product to edit.');
	    }
	    else {
		window.location = "product/edit_all?pids="+btoa(selected);
	    }
	    return false;
	});
	
	//active all
	$(".active_all").on("click", function(e){
	    e.preventDefault();
	    
	    var selected = [];
	    $(".select_multiple_checkbox:checkbox:checked").each(function() {
		selected.push($(this).val());
	    });
	    
	    if(selected == '') {
		alert('Please select product.');
		return false;
	    }
	    
	    $.ajax({
		url: "product/active_all",
                type: "post",
                data: "pids="+selected,
                success: function (resp)
                { 
//                    console.log(resp);
		    if (resp == 1) {
                        alert('Selected Products Activated.');
			Xcrud.reload();
                    } 
                }
	    });	    
	});
	
	//deactive all
	$(".deactive_all").on("click", function(e){
	    e.preventDefault();
	    
	    var selected = [];
	    $(".select_multiple_checkbox:checkbox:checked").each(function() {
		selected.push($(this).val());
	    });
	    
	    if(selected == '') {
		alert('Please select product.');
		return false;
	    }
	    
	    $.ajax({
		url: "product/deactive_all",
                type: "post",
                data: "pids="+selected,
                success: function (resp)
                { 
//                    console.log(resp);
		    if (resp == 1) {
                        alert('Selected Products Deactivated.');
			Xcrud.reload();
                    } 
                }
	    });	    
	});	
        
        $(".drink_type").change(function(e){
            e.preventDefault();
            
            if($(this).val() == '1'){
                $(".abv_per").show();
            } else {
                $(".abv_per").hide();
            }
        });
	
	//Upload Images
        $('.upload_bulk_img').click(function (e) {
            e.preventDefault();
            
	    var progress_bar_id = '#progress-wrp'; //ID of an element for response output
	    //reset progressbar
	    $(progress_bar_id +" .progress-bar").css("width", "0%");
	    $(progress_bar_id + " .status").text("0%");
            $('.success_msg11').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_bluk_upload"]');
            
            $(this).html('Processing...');
	    $(this).attr("disabled", true);
            var data = new FormData(frm[0]);
            //console.log(data);
            $.ajax({
                url: "product/upload_bulk",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData:false,
		xhr: function(){			
		    //upload Progress
		    var xhr = $.ajaxSettings.xhr();
		    if (xhr.upload) {
			    xhr.upload.addEventListener('progress', function(event) {
				    var percent = 0;
				    var position = event.loaded || event.position;
				    var total = event.total;
				    if (event.lengthComputable) {
					    percent = Math.ceil(position / total * 100);
				    }
				    //update progressbar
				    $(progress_bar_id +" .progress-bar").css("width", + percent +"%");
				    $(progress_bar_id + " .status").text(percent +"%");
			    }, true);
		    }
		    return xhr;
		},
		mimeType:"multipart/form-data",
                success: function (resp)
                { 
                    //console.log(resp);
                    if (resp == 'success') {
                        alert('Products Images Successfully.');
                        $('.upload_bulk_img').html('Save');
			$('.upload_bulk_img').attr("disabled", false);
                        window.location.reload();
                    }                     
                }

            });
           
        });
        
        //Upload excel
        $('.upload_importxl').click(function (e) {
            e.preventDefault();
            $(this).attr("disabled", true);
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_import_xls"]');
            
            $(this).html('Processing...');
            var data = new FormData(frm[0]);
            //console.log(data);
            $.ajax({
                url: "product/import_xls",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData:false,
                success: function (resp1)
                {
		    $('.upload_importxl').attr("disabled", false);
		    //console.log(resp);
                    if (resp1 == 'success') {
                        alert('Products Uploaded Successfully.');
                        $('.upload_importxl').html('Save');
                        window.location.reload();
                    } 
                    else if(resp1 == '1'){
                       alert('Please add product name in excel file.');
                       $('.upload_importxl').html('Save');     
                    }
		    else {
			var resp = $.parseJSON(resp1);
			if(resp[0] == '2'){
			    alert('Please add product description in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '3'){
			    alert('Please product actual stock value should be in digits in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '4'){
			    alert('Please add product minimum stock value in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '5'){
			    alert('Please add product maximum stock value in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '6'){
			    alert('Please add seller code in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '7'){
			    alert('Please add supplier code in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '8'){
			    alert('Please add pack size in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '9'){
			    alert('Please add product volume in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '10'){
			    alert('Please add volume type in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '11'){
			    alert('Please add product brand code in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '12'){
			    alert('Please add product category code in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '13'){
			    alert('Please add product drink type in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '14'){
			    alert('Please add actual (mrp) product price in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '15'){
			    alert('Please add product image name in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '16'){
			    alert('Please product drink type should be in digits in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '17'){
			    alert('Please add product image name with extension in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '18'){
			    alert('Product category code is not valid which is added in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '19'){
			    alert('Product brand code is not valid which is added in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '20'){
			    alert('Product seller code is not valid which is added in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '21'){
			    alert('Product supplier code is not valid which is added in excel file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '22'){
			    alert('You have added wrong brand code for category code '+resp[2]+' in product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
		    }
                }
            });           
        });
        
        var rowNum = 0;

        $(".addvolume").on("click", function() {
            
            var frm = $('form[name = "frm_add_product"]');
            
            //Brand
            var brand_id = frm.find('[name = "brand_id"]').val();
            if (!brand_id || brand_id == '0' || !brand_id.trim()) {
                if($(".error").text() == ''){
                    frm.find('[name = "brand_id"]').addClass('input_error').parents('.form-group').append(error_msg('Please select brand'));
                    valid = false;
                }
            }   
            else { 
                
                if($(".count_vol_div").val() != ''){                    
                    var colval = $(".count_vol_div").val();
                    //console.log(colval);
                    rowNum = colval;
                } 
                rowNum++; 
                
                $.ajax({
                    url: 'product/get_volume',
                    data: 'brand=' + brand_id,
                    type: 'post',
                    success: function (volume) {
                        //console.log(volume);
                        
                        $(".volume_id_"+rowNum+"").html('');
                        allow_numeric();

                        if(volume) {
                            var volume_list = $.parseJSON(volume);
                            $.each(volume_list, function(index, value){
                                $(".volume_id_"+rowNum+"").append('<option value="'+value.volume_id+'">'+value.volumes+'</option>');
                            });
                        }
                    }
                });   
                //update_row(rowNum);
                addrow(rowNum);  
                
                if($(".in_loyalty_club").val() == 1){
                    $(".loyalty_club_options").show();
                }
                
                if($(".in_vip_club").val() == 1){
                    $(".vip_club_options").show();
                }
                
                $(".in_loyalty_club").change(function(){
                    if($(this).val() == '1'){
                        $(".loyalty_club_options").show();
                    } else {
                        $(".loyalty_club_options").hide();
                    }
                });

                $(".in_vip_club").change(function(){
                    if($(this).val() == '1'){
                        $(".vip_club_options").show();
                    } else {
                        $(".vip_club_options").hide();
                    }
                });
                
                //Calculate when original price changes
                $(".ori_price").on("input",function(e){ 
                    var ori_price = $(this).val();

                    var nor_dis = (($(this).closest('div.samevoldiv').find('.nor_discount').val() * ori_price) / 100);
                    $(this).closest('div.samevoldiv').find('.nor_sell_price').val(ori_price - nor_dis);

                    var lc_dis = (($(this).closest('div.samevoldiv').find('.lc_discount').val() * ori_price) / 100);
                    $(this).closest('div.samevoldiv').find('.lc_sell_price').val(ori_price - nor_dis);

                    var vip_dis = (($(this).closest('div.samevoldiv').find('.vip_discount').val() * ori_price) / 100);
                    $(this).closest('div.samevoldiv').find('.vip_sell_price').val(ori_price - nor_dis);
                    e.preventDefault();
                });

                //Calculate normal sell price
                $(".nor_discount").on("input",function(e){ 
                    var ori_price = $(this).closest('div.samevoldiv').find('.ori_price').val();  
                    //alert(ori_price);  
                    var nor_dis = (($(this).val() * ori_price) / 100);
                    $(this).closest('div.samevoldiv').find('.nor_sell_price').val(ori_price - nor_dis);
                    e.preventDefault();
                });

                //Calculate loyalty sell price
                $(".lc_discount").on("input",function(e){     
                    var ori_price = $(this).closest('div.samevoldiv').find('.ori_price').val();  
                    //alert(ori_price);  
                    var nor_dis = (($(this).val() * ori_price) / 100);
                    $(this).closest('div.samevoldiv').find('.lc_sell_price').val(ori_price - nor_dis);
                    e.preventDefault();
                });

                //Calculate vip sell price
                $(".vip_discount").on("input",function(e){     
                    var ori_price = $(this).closest('div.samevoldiv').find('.ori_price').val();  
                    //alert(ori_price);  
                    var nor_dis = (($(this).val() * ori_price) / 100);
                    $(this).closest('div.samevoldiv').find('.vip_sell_price').val(ori_price - nor_dis);
                    e.preventDefault();
                });
                
                $(".delete_vol").click(function() {
                    var count_vol_div = $(".count_vol_div").val();
                    count_vol_div = count_vol_div - 1;
                    $(".count_vol_div").val(count_vol_div);
                });
            }
            
            return false;
        });
        
        //Top Pick Product
        $('.top_pick_checkbox').click(function (e) {
            
            var products_id; var tp_status;
            
            if($(this).is(":checked")) {
                products_id = $(this).data('productid');
                tp_status = 1;
            } 
            else {
                products_id = $(this).data('productid');
                tp_status = 0;
            } 
            
            $.ajax({
                url: 'product/top_pick',
                data: 'product_id=' + products_id + '&tp_status=' + tp_status,
                type: 'post',
                success: function () {}
            });
           
        });
        
        //Get Brand
        $('.category_id').change(function (e) {
            
            var category = $(this).val();
            
            $.ajax({
                url: 'product/get_sub_category',
                data: 'category=' + category,
                type: 'post',
                success: function (sub_categories) {
                    //console.log(sub_categories);
                    $(".sub_category_id").html('');
                    $(".sub_category_id").append('<option value="0">--Select--</option>');
                    
                    if(sub_categories) {
                        var brands_list = $.parseJSON(sub_categories);
                        $.each(brands_list, function(index, value){
                            $(".sub_category_id").append('<option value="'+value.category_id+'">'+value.category_name+'</option>');
                        });
                    }
                }
            });
            
            $.ajax({
                url: 'product/get_brand',
                data: 'category=' + category,
                type: 'post',
                success: function (brands) {
                    //console.log(brands);
                    $(".brand_id").html('');
                    $(".brand_id").append('<option value="0">--Select--</option>');
                    
                    if(brands) {
                        var brands_list = $.parseJSON(brands);
                        $.each(brands_list, function(index, value){
                            $(".brand_id").append('<option value="'+value.brand_id+'">'+value.brand_name+'</option>');
                        });
                    }
                }
            });
           
        });
        
        $('.sub_category_id').change(function (e) {
            
            var category = $(this).val();
            
            $.ajax({
                url: 'product/get_brand',
                data: 'category=' + category,
                type: 'post',
                success: function (brands) {
                    //console.log(brands);
                    $(".brand_id").html('');
                    $(".brand_id").append('<option value="0">--Select--</option>');
                    
                    if(brands) {
                        var brands_list = $.parseJSON(brands);
                        $.each(brands_list, function(index, value){
                            $(".brand_id").append('<option value="'+value.brand_id+'">'+value.brand_name+'</option>');
                        });
                    }
                }
            });
           
        });
        
        //Get Volume
        $('.brand_id').change(function (e) {
            
            var brand = $(this).val();
            //console.log(brand);
            var countvol = $(".count_vol_div").val();
            //console.log(countvol);
            for(var rowNum=1; rowNum<=countvol; rowNum++) { 
                //console.log(rowNum);
                $.ajax({
                    url: 'product/get_volume',
                    data: 'brand=' + brand,
                    type: 'post',
                    success: function (volume) {
                        //console.log(volume);
                        rowNum = rowNum - 1;
                        //console.log(rowNum);
                        $(".volume_id_"+rowNum+"").find('option').remove();

                        if(!volume) {
                            $(".volume_id_"+rowNum+"").append('<option value="">No volume found</option>');
                        } else {                           
                            var volume_list = $.parseJSON(volume);
                            $.each(volume_list, function(index, value){
                                $(".volume_id_"+rowNum+"").append('<option value="'+value.volume_id+'">'+value.volumes+'</option>');
                            });
                        }
                    }
                });
            }       
        });
        
        $(".addproduct").click(function(){
           $('[name="frm_add_product"]').trigger("reset"); 
           $('select').prop('selectedIndex',0);
           $(".brand_id").html('');
           $(".brand_id").append('<option value="0">--Select--</option>');
           $("#product_img_view").remove();
           $("[name='product_gallery_view']").remove();
           $('#product_img,#gimgs').find('br').remove();
           $("[name='product_id']").val('');
           $("input[name='count_vol_div']").val('');
           $(".samevoldiv").remove();
            
       });
       
        $(".closebtn").on("click", function(){
           $("input[name='count_vol_div']").val('');
           $("div[class*='samevoldiv']").remove();
           $("#product_img_view").remove();
           $("[name='product_gallery_view']").remove();
           $('#product_img,#gimgs').find('br').remove();
           $("[name='product_id']").val('');
           $(".samevoldiv").remove();
       });       
       
        //Add Product        
        $('.submit_btn').click(function (e) {
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_product"]');
            
            //Category
            var category_id = frm.find('[name = "category_id"]').val();
            if (!category_id || category_id == '0' || !category_id.trim()) {
                frm.find('[name = "category_id"]').addClass('input_error').parents('.form-group').append(error_msg('Please select category'));
                valid = false;
            }   
            
            //Brand
            var brand_id = frm.find('[name = "brand_id"]').val();
            if (!brand_id || brand_id == '0' || !brand_id.trim()) {
                frm.find('[name = "brand_id"]').addClass('input_error').parents('.form-group').append(error_msg('Please select brand'));
                valid = false;
            }   
            
            //Supplier
            var supplier_id = frm.find('[name = "supplier_id"]').val();
            if (!supplier_id || supplier_id == '0' || !supplier_id.trim()) {
                frm.find('[name = "supplier_id"]').addClass('input_error').parents('.form-group').append(error_msg('Please select the supplier'));
                valid = false;
            }  
            
            //Seller 
            var seller_id = frm.find('[name = "seller_id"]').val();
            if (!seller_id || seller_id == '0' || !seller_id.trim()) {
                frm.find('[name = "seller_id"]').addClass('input_error').parents('.form-group').append(error_msg('Please select the seller'));
                valid = false;
            } 
            
            //Name
            var product_name = frm.find('[name = "product_name"]').val();
            if (!product_name || !product_name.trim()) {
                frm.find('[name = "product_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter product name'));
                valid = false;
            }    
            
            $('.check_blank').each(function(e){
                if( $(this).val() == "" ){
                  frm.find($(this)).addClass('input_error').parents('.form-group').append(error_msg('Please enter values'));
                  valid = false;
                } else if( $(this).text() == "" && e.tagName === 'SELECT' ){
                  frm.find($(this)).addClass('input_error').parents('.form-group').append(error_msg('Please select volume'));
                  valid = false;
                }
            });

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
                //console.log(data);
                $.ajax({
                    url: "product/save",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);
                        if (resp === 'exist') {
                            frm.find('[name = "product_name"]').addClass('input_error').parents('.form-group').append(error_msg('Product already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        } 
                        else if (resp === 'error') {
                            frm.find('[name = "addvolume"]').addClass('input_error').parents('.form-group').append(error_msg('Please add volume.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }
                        else if (resp === 'success'){
                            if($("input[name='product_id']").val() != ''){
                                $('.mdl_product').modal('toggle');
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "product";
                            }
                        }
                    }

                });
            }
        });
        
        $(document).on('click', '.edit_data', function () {
            var product_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'product/get_product',
                data: 'product_id=' + product_id,
                type: 'post',
                success: function (product) {
                    if (product) {
                        //console.log(product); 
                        allow_numeric();
                        product = JSON.parse(product);
                        //console.log(product);
                        $("[name='action'").val();
                        $("[name='action'").val('edit');
                        $("[name='no_of_return_days'").val(product.no_of_return_days);  
                        
                        $(".in_loyalty_club").change(function(){
                            if($(this).val() == '1'){
                                $(".loyalty_club_options").show();
                            } else {
                                $(".loyalty_club_options").hide();
                            }
                        });

                        $(".in_vip_club").change(function(){
                            if($(this).val() == '1'){
                                $(".vip_club_options").show();
                            } else {
                                $(".vip_club_options").hide();
                            }
                        });
                        

                        $('#product_img,#gimgs').find('br').remove();
                        $("#product_img_view").remove();
                        $("[name='product_gallery_view']").remove();                                
                        
                        $('.category_id option:selected').removeAttr('selected');
                        $('.sub_category_id option:selected').removeAttr('selected');                          
                        
                        $('select[name^="top_pick"] option[value="'+product.top_pick+'"]').attr("selected","selected");
                        $('select[name^="in_loyalty_club"] option[value="'+product.in_loyalty_club+'"]').attr("selected","selected");
                        $('select[name^="in_vip_club"] option[value="'+product.in_vip_club+'"]').attr("selected","selected");
                        $('select[name^="drink_type"] option[value="'+product.drink_type+'"]').attr("selected","selected");
                        
                        //Get brand
                        $.ajax({
                            url: 'product/get_brand',
                            data: 'category=' + product.category_id,
                            type: 'post',
                            success: function (brands) {
                                //console.log(brands);
                                $(".brand_id").html('');
                                $(".brand_id").append('<option value="0">--Select--</option>');

                                if(brands) {
                                    var brands_list = $.parseJSON(brands);
                                    $.each(brands_list, function(index, value){
                                        $(".brand_id").append('<option value="'+value.brand_id+'">'+value.brand_name+'</option>');
                                    });
                                    
                                    $('select[name^="brand_id"] option[value="'+product.brand_id+'"]').attr("selected","selected");
                                }
                            }
                        });                        
                        
                        //Fetch data from ajax
                        $.ajax({
                            url: 'product/get_volume',
                            data: 'brand=' + product.brand_id,
                            type: 'post',
                            success: function (volume) {
                                //console.log(volume);
                                if(volume) {
                                    var volume_list = $.parseJSON(volume);  
                                   // console.log(volume_list);
                                    $(".count_vol_div").val(product.count_vol_div);
                                    
                                    if($(".samevoldiv").length == 0) {                                    
                                        for(var rowNum=1; rowNum<=product.count_vol_div; rowNum++) {  
                                        
                                        var selected = '';

                                        var $homediv = $(".addvolume").parents('.homediv');  
                                        var $voldiv = '<div id="voldiv_'+rowNum+'" class="samevoldiv"><div class="row"><div class="col-sm-1"><div class="form-group"> <span class="help-block m-b-none" style="font-size:2rem; margin:20px 20px 0 15px;"><b>#'+rowNum+'</b> </span></div></div><div class="col-sm-3"><div class="form-group"> <span class="help-block m-b-none"><b>Volume(*)</b> </span> <select class="form-control check_blank volume_id_'+rowNum+'" name="volume_id_'+rowNum+'"></select></div></div><div class="col-sm-4"><div class="form-group"> <span class="help-block m-b-none"><b>Original Price(*)</b></span> <input type="text" class="form-control numeric ori_price actual_price_'+rowNum+'" maxlength="8" placeholder="Product Original Price" value="'+product["actual_price_"+rowNum]+'" name="actual_price_'+rowNum+'"></div></div><div class="col-sm-3"><div class="form-group"> <span class="help-block m-b-none"><b>Pack Size</b></span> <input type="text" class="form-control numeric pack_size_'+rowNum+'" placeholder="Pack Size" maxlength="3" value="'+product["pack_size_"+rowNum]+'" name="pack_size_'+rowNum+'"></div></div><div class="col-sm-1"><div class="form-group" style="display: block;vertical-align: middle;margin-top: 28px;"> <a class="btn btn-default btn-sm btn-danger delete_vol delete_'+rowNum+'" href="#" title="Remove" name="delete_'+rowNum+'" data-delete="'+rowNum+'" data-volid="'+product["volume_id_"+rowNum]+'"><i class="glyphicon glyphicon-trash"></i></a></div></div></div><div class="row"><div class="col-sm-4"><div class="form-group"> <span class="help-block m-b-none"><b>Total Stock Units(*)</b></span> <input type="text" class="form-control numeric check_blank units_'+rowNum+'" maxlength="8" placeholder="Total Stock Units" value="'+product["units_"+rowNum]+'" name="units_'+rowNum+'"></div></div><div class="col-sm-4"><div class="form-group"> <span class="help-block m-b-none"><b>Minimum Stock Limit(*)</b></span> <input type="text" class="form-control numeric check_blank min_stock_limit_'+rowNum+'" maxlength="8" value="'+product["min_stock_limit_"+rowNum]+'" placeholder="Minimum Stock Limit" name="min_stock_limit_'+rowNum+'"></div></div><div class="col-sm-4"><div class="form-group"> <span class="help-block m-b-none"><b>Maximum Stock Limit(*)</b></span> <input type="text" class="form-control numeric check_blank max_stock_limit_'+rowNum+'" maxlength="8" placeholder="Maximum Stock Limit" value="'+product["max_stock_limit_"+rowNum]+'" name="max_stock_limit_'+rowNum+'"></div></div></div><div class="row"> <div class="col-sm-6"><div class="form-group"><span class="help-block m-b-none"><b>Normal Discount (in %)</b></span> <input type="text" class="form-control nor_discount numeric normal_discount_'+rowNum+'" maxlength="8" placeholder="Product Discount" value="'+product["normal_discount_"+rowNum]+'" name="normal_discount_'+rowNum+'"></div></div><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>Normal Sell Price(*)</b></span> <input type="text" disabled="disabled" class="form-control nor_sell_price numeric normal_sell_price_'+rowNum+'" maxlength="8" placeholder="Product Sell Price" value="'+product["normal_sell_price_"+rowNum]+'" name="normal_sell_price_'+rowNum+'"></div></div></div><div class="row loyalty_club_options"> <div class="col-sm-6"><div class="form-group"><span class="help-block m-b-none"><b>Loyalty Club Discount (in %)</b></span> <input type="text" class="form-control lc_discount numeric loyalty_club_discount_'+rowNum+'" maxlength="8" placeholder="Loyalty Club Discount" value="'+product["loyalty_club_discount_"+rowNum]+'" name="loyalty_club_discount_'+rowNum+'"></div></div><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>Loyalty Club Sell Price(*)</b></span> <input type="text" class="form-control lc_sell_price numeric loyalty_club_sell_price_'+rowNum+'" maxlength="8" placeholder="Loyalty Club Sell Price" disabled="disabled" value="'+product["loyalty_club_sell_price_"+rowNum]+'" name="loyalty_club_sell_price_'+rowNum+'"></div></div></div><div class="row vip_club_options"> <div class="col-sm-6"><div class="form-group"><span class="help-block m-b-none"><b>VIP Club Discount (in %)</b></span> <input type="text" class="form-control vip_discount numeric vip_club_discount_'+rowNum+'" maxlength="8" placeholder="VIP Club Discount" value="'+product["vip_club_discount_"+rowNum]+'" name="vip_club_discount_'+rowNum+'"></div></div><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>VIP Club Sell Price(*)</b></span> <input type="text" class="form-control vip_sell_price numeric vip_club_sell_price_'+rowNum+'" maxlength="8" disabled="disabled" placeholder="VIP Club Sell Price" value="'+product["vip_club_sell_price_"+rowNum]+'" name="vip_club_sell_price_'+rowNum+'"></div></div></div></div>';
                                    
                                        $homediv.append($voldiv);
                                        
                                        if($(".in_loyalty_club").val() == "1"){
                                            $(".row.loyalty_club_options").show();
                                        } else {
                                            $(".row.loyalty_club_options").hide();
                                        }

                                        if($(".in_vip_club").val() == "1"){
                                            $(".vip_club_options").show();
                                        } else {
                                            $(".vip_club_options").hide();
                                        }

                                        $.each(volume_list, function(index, value){                                           
                                           // console.log(product["volume_id_"+rowNum]);
                                            if(product["volume_id_"+rowNum] === value.volume_id) {
                                                selected = "selected";
                                            } else {
                                                selected = "";
                                            }
                                            
                                            //console.log(value);
                                            $(".volume_id_"+rowNum+"").append('<option value="'+value.volume_id+'" '+selected+'>'+value.volumes+'</option>');
                                        });
                                        
                                    }
                                    }
                                    //Calculate when original price changes
                                    $(".ori_price").on("input",function(e){ 
                                        var ori_price = $(this).val();

                                        var nor_dis = (($(this).closest('div.samevoldiv').find('.nor_discount').val() * ori_price) / 100);
                                        $(this).closest('div.samevoldiv').find('.nor_sell_price').val(ori_price - nor_dis);

                                        var lc_dis = (($(this).closest('div.samevoldiv').find('.lc_discount').val() * ori_price) / 100);
                                        $(this).closest('div.samevoldiv').find('.lc_sell_price').val(ori_price - nor_dis);

                                        var vip_dis = (($(this).closest('div.samevoldiv').find('.vip_discount').val() * ori_price) / 100);
                                        $(this).closest('div.samevoldiv').find('.vip_sell_price').val(ori_price - nor_dis);
                                        e.preventDefault();
                                    });

                                    //Calculate normal sell price
                                    $(".nor_discount").on("input",function(e){ 
                                        var ori_price = $(this).closest('div.samevoldiv').find('.ori_price').val();  
                                        //alert(ori_price);  
                                        var nor_dis = (($(this).val() * ori_price) / 100);
                                        $(this).closest('div.samevoldiv').find('.nor_sell_price').val(ori_price - nor_dis);
                                        e.preventDefault();
                                    });

                                    //Calculate loyalty sell price
                                    $(".lc_discount").on("input",function(e){     
                                        var ori_price = $(this).closest('div.samevoldiv').find('.ori_price').val();  
                                        //alert(ori_price);  
                                        var nor_dis = (($(this).val() * ori_price) / 100);
                                        $(this).closest('div.samevoldiv').find('.lc_sell_price').val(ori_price - nor_dis);
                                        e.preventDefault();
                                    });

                                    //Calculate vip sell price
                                    $(".vip_discount").on("input",function(e){     
                                        var ori_price = $(this).closest('div.samevoldiv').find('.ori_price').val();  
                                        //alert(ori_price);  
                                        var nor_dis = (($(this).val() * ori_price) / 100);
                                        $(this).closest('div.samevoldiv').find('.vip_sell_price').val(ori_price - nor_dis);
                                        e.preventDefault();
                                    });
                                }
                            }
                        });
                    
			if(product.feature_img != "") {
			    //Feature Image
			    $("#product_img").append('<br><a href="<?php echo PRODUCT_S3_PATH; ?>'+product.feature_img+'" name="product_img_view" id="product_img_view">'+product.feature_img+'</a>');
			}
                        
			if(product.gallery_image != "") {
			    //Gallery Images                        
			    $.each( product.gallery_image, function( key, value ) {
				//alert( value );
				if(value != "") {
				    var v1 = value.split('.').slice(0, -1).join('.');
				    $("#gimgs").append('<br><a class="product_gallery_view" href="<?php echo PRODUCT_S3_PATH; ?>'+value+'" name="product_gallery_view" id="product_gallery_view_'+v1+'">'+value+'</a>&nbsp;&nbsp;<button type="button" class="btn btn-xs btn-danger del_gimg" data-id="'+value+'" id="del_gimg_'+v1+'"><i class="fa fa-close"></i></button>');
				}
			    });
			}
                                   
                        delete product.feature_img;

                        $('[name="frm_add_product"]').populate(product);
                        
                        //Drop Down 
                        if(product.parent_id > 0) {
                            //$('select[name^="category_id"] option[value="'+product.parent_id+'"]').attr("selected","selected");                        
                            $('select[name^="category_id"]').val(product.parent_id);                        
                            
                            //subcategory
                            $.ajax({
                                url: 'product/get_sub_category',
                                data: 'category=' + product.parent_id,
                                type: 'post',
                                success: function (sub_categories) {
                                    //console.log(sub_categories);
                                    $(".sub_category_id").html('');
                                    $(".sub_category_id").append('<option value="0">--Select--</option>');

                                    if(sub_categories) {
                                        var brands_list = $.parseJSON(sub_categories);
                                        $.each(brands_list, function(index, value){
                                            $(".sub_category_id").append('<option value="'+value.category_id+'">'+value.category_name+'</option>');
                                        });
                                        
                                        $('select[name^="sub_category_id"] option[value="'+product.category_id+'"]').attr("selected","selected");                        
                                    }
                                }
                            });
            
                            $('select[name^="category_id"] option[value="'+product.parent_id+'"]').attr("selected","selected");                        
                            
                        }
                        else {
                            $('select[name^="category_id"] option[value="'+product.category_id+'"]').attr("selected","selected");                        
                            
                            //subcategory
                            $.ajax({
                                url: 'product/get_sub_category',
                                data: 'category=' + product.category_id,
                                type: 'post',
                                success: function (sub_categories) {
                                    //console.log(sub_categories);
                                    $(".sub_category_id").html('');
                                    $(".sub_category_id").append('<option value="0">--Select--</option>');

                                    if(sub_categories) {
                                        var brands_list = $.parseJSON(sub_categories);
                                        $.each(brands_list, function(index, value){
                                            $(".sub_category_id").append('<option value="'+value.category_id+'">'+value.category_name+'</option>');
                                        });                      
                                    }
                                }
                            });
                        }
                    } 
                }
            });
        });
	
	$(document).on('click', '.del_gimg', function(e){
            var img_name = $(this).data("id");
	    var v1 = img_name.split('.').slice(0, -1).join('.');
	    var img_row = $(this);
	    $.ajax({
		url: 'product/delete_product_img',
		data: 'img_name=' + img_name,
		type: 'post',
		success: function (response) {
		    //console.log(response);
		    if(response == '1') {
			//console.log(img_row.closest("#del_gimg_"+v1+""));
			img_row.prev().closest("#product_gallery_view_"+v1+"").remove();
			img_row.closest("#del_gimg_"+v1+"").remove();
		    }
		}
	    });
        });
        
        $(document).on('click', '.delete_vol', function(e){
            var rowid = $(this).data("delete");
            $(this).closest("#voldiv_"+rowid+"").remove();
            e.preventDefault();
        });
        
        function addrow(rowNum){
            var $homediv = $(".addvolume").parents('.homediv');                
            var $voldiv = '<div id="voldiv_'+rowNum+'" class="samevoldiv"><div class="row"><div class="col-sm-1"><div class="form-group"> <span class="help-block m-b-none" style="font-size:2rem; margin:20px 20px 0 15px;"><b>#'+rowNum+'</b> </span></div></div><div class="col-sm-3"><div class="form-group"> <span class="help-block m-b-none"><b>Volume(*)</b> </span> <select class="form-control check_blank volume_id_'+rowNum+'" name="volume_id_'+rowNum+'"></select></div></div><div class="col-sm-4"><div class="form-group"> <span class="help-block m-b-none"><b>Original Price(*)</b></span> <input type="text" class="form-control numeric ori_price actual_price_'+rowNum+'" placeholder="12.99" maxlength="8" name="actual_price_'+rowNum+'"></div></div><div class="col-sm-3"><div class="form-group"> <span class="help-block m-b-none"><b>Pack Size</b></span> <input type="text" class="form-control numeric pack_size_'+rowNum+'" placeholder="6" maxlength="3" name="pack_size_'+rowNum+'"></div></div><div class="col-sm-1"><div class="form-group" style="display: block;vertical-align: middle;margin-top: 28px;"> <a class="btn btn-default btn-sm btn-danger delete_vol delete_'+rowNum+'" href="#" title="Remove" name="delete_'+rowNum+'" data-delete="'+rowNum+'"><i class="glyphicon glyphicon-trash"></i></a></div></div></div><div class="row"><div class="col-sm-4"><div class="form-group"> <span class="help-block m-b-none"><b>Total Stock Units(*)</b></span> <input type="text" class="form-control numeric check_blank units_'+rowNum+'" placeholder="15" maxlength="8" name="units_'+rowNum+'"></div></div><div class="col-sm-4"><div class="form-group"> <span class="help-block m-b-none"><b>Minimum Stock Limit(*)</b></span> <input type="text" class="form-control numeric check_blank min_stock_limit_'+rowNum+'" placeholder="2" maxlength="8" name="min_stock_limit_'+rowNum+'"></div></div><div class="col-sm-4"><div class="form-group"> <span class="help-block m-b-none"><b>Maximum Stock Limit(*)</b></span> <input type="text" class="form-control numeric check_blank max_stock_limit_'+rowNum+'" placeholder="25" maxlength="8" name="max_stock_limit_'+rowNum+'"></div></div></div><div class="row"><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>Normal Discount (in %)</b></span> <input type="text" class="form-control nor_discount numeric normal_discount_'+rowNum+'" placeholder="10.45" maxlength="5" name="normal_discount_'+rowNum+'"></div></div><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>Normal Sell Price(*)</b></span> <input type="text" disabled="disabled" class="form-control nor_sell_price numeric normal_sell_price_'+rowNum+'" placeholder="12.99" maxlength="8" name="normal_sell_price_'+rowNum+'"></div></div></div><div class="row loyalty_club_options"><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>Loyalty Club Discount (in %)</b></span> <input type="text" class="form-control lc_discount numeric loyalty_club_discount_'+rowNum+'" placeholder="10.45" maxlength="5" name="loyalty_club_discount_'+rowNum+'"></div></div><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>Loyalty Club Sell Price(*)</b></span> <input type="text" disabled="disabled" class="form-control lc_sell_price numeric loyalty_club_sell_price_'+rowNum+'" placeholder="12.99" maxlength="8" name="loyalty_club_sell_price_'+rowNum+'"></div></div></div><div class="row vip_club_options"><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>VIP Club Discount (in %)</b></span> <input type="text" class="form-control vip_discount numeric vip_club_discount_'+rowNum+'" placeholder="10.45" maxlength="5" name="vip_club_discount_'+rowNum+'"></div></div><div class="col-sm-6"><div class="form-group"> <span class="help-block m-b-none"><b>VIP Club Sell Price(*)</b></span> <input type="text" disabled="disabled" class="form-control vip_sell_price numeric vip_club_sell_price_'+rowNum+'" placeholder="12.99" maxlength="8" name="vip_club_sell_price_'+rowNum+'"></div></div></div></div>';
           
            $homediv.append($voldiv); 
            $(".count_vol_div").val(rowNum);
            
            $(".loyalty_club_options").hide();
            $(".vip_club_options").hide();
        }        
    });
</script>
