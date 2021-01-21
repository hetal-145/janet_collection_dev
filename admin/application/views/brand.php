<style>
    #progress-wrp, #progress-wrp2 {
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
    #progress-wrp .progress-bar, #progress-wrp2 .progress-bar{
	height: 20px;
	border-radius: 3px;
	background-color: #28a745;
	width: 0;
	box-shadow: inset 1px 1px 10px rgba(0, 0, 0, 0.11);
    }
    #progress-wrp .status, #progress-wrp2 .status{
	top:3px;
	left:50%;
	position:absolute;
	display:inline-block;
	color: #000000;
    }
</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Brands</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Brands</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">                
                <div class="ibox-title">
                    <h5>Manage Brands</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content"> 
		    <a href="#" class="btn btn-primary pull-left importxl custombtn" data-toggle="modal" data-target=".mdl_importxl">Import Excel</a>
		    
		    <a href="brand/export_xls" class="btn btn-info pull-left exportxls custombtn" data-type="brand">Export / Download Excel</a>
		    
                    <a href="#" class="btn btn-success pull-left add_brand custombtn" id="add_brand" data-toggle="modal" data-target=".mdl_brand">Add Brand</a>
		    
		    <a href="#" class="btn btn-warning pull-left bluk_upload custombtn" data-toggle="modal" data-target=".mdl_bluk_upload">Bulk Upload Brand Logos & Sliders Images</a>
	
		    <span class="clearfix"></span>
                    <?php echo $content; ?>
                </div>
                
                <div class="modal fade mdl_brand" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
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
                                                <select multiple="multiple" class="category_id" style="width:100%;" name="category_id[]">
                                                    <?php foreach($categories as $category) { ?>
                                                        <option value="<?php echo $category["category_id"]; ?>"><?php echo $category["category_name"]; ?></option>
                                                    <?php } ?>                                
                                                </select>                                                
                                            </div>
                                        </div> 
                                        
                                        <div class="col-sm-12 sub_category_id_div">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Sub Category</b></span>
                                                <select multiple="multiple" style="width:100%;" class="sub_category_id" name="sub_category_id[]">
                                                </select>                                                
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Brand Name(*)</b></span>
                                                <input type="text" class="form-control brand_name" placeholder="Brand name" name="brand_name">
                                            </div>
                                        </div> 
                                        
                                        <div class="col-sm-12 volume_hide">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Volume(*)</b></span>
                                                <input maxlength="5" type="text" class="form-control numeric volumne_value" placeholder="Volume" name="volumne_value">
                                            </div>
                                        </div> 

                                        <div class="col-sm-12 volume_hide">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Type(*)</b> </span>
                                                <select class="form-control type" name="type">
                                                    <option value="">--Select--</option>
                                                    <?php foreach($volumes as $volume) { ?>
                                                        <option value="<?php echo $volume["volume_type_id"]; ?>"><?php echo $volume["volume_type"]; ?></option>
                                                    <?php } ?>
                                                </select>
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
                                                <span class="help-block m-b-none"><b>Want to add in loyalty club list?</b> </span>
                                                <select class="form-control in_loyalty_club" name="in_loyalty_club">
                                                    <option value="">--Select--</option>
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>       
                                                </select>
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
                                        <input type="hidden" class="action" name="action" value="add">
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
					<!--<div id="progress-wrp2"><div class="progress-bar"></div ><div class="status">0%</div></div>-->
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
        
        $(".slider_img_block").hide();
        $(".sub_category_id_div").hide();        
        
        $("#add_brand").click(function(){
            $('form[name = "frm_add_brand"]')[0].reset();
        });
        
        $("#close_btn").click(function(){
            $('form[name = "frm_add_brand"]')[0].reset();
        });
       
        $(".category_id").multipleSelect({
            filter: true,
        });
        
        $(".sub_category_id").multipleSelect({
            filter: true,
        });        
        
        $("a.add_brand").click(function(){            
            $(".brand_name").val('');
            $(".volumne_value").val('');
            $('.action').val('add'); 
            $(".volume_hide").show();       
            $(".type").prop('selectedIndex',0);
            $('.ms-parent.category_id .ms-drop > ul').find('li.selected').removeClass('selected');
            $('.ms-parent.sub_category_id .ms-drop > ul').find('li.selected').removeClass('selected');
            $("#slider_img_view").text('');
            $("#brand_logo_view").text('');
            $(".brand_id").val('');          
            $('select.category_id').multipleSelect('uncheckAll', true);
            $('select.sub_category_id').multipleSelect('uncheckAll', true);
            $(".sub_category_id_div").hide();
            $( ".is_top_brand_checkbox" ).attr( 'checked', false );
            $(".slider_img_block").hide();
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
                url: 'brand/top_brands',
                data: 'brand_id=' + brand_id + '&tb_status=' + tb_status,
                type: 'post',
                success: function () {}
            });
        });
        
        //get sub category
        $('.category_id').on('click.multile.select', function (e, arg1) {
            var selected = [];
            $('.category_id > .ms-drop > ul').find('li.selected label input[type=checkbox]').each(function(i){
                selected.push($(this).val());
            });
            //console.log(selected);
            
            $.ajax({
                url: "brand/get_subcategory",
                type: "post",
                data: 'category_id=' + selected,
                success: function (subcategory)
                {   
                    //console.log(subcategory);
                    if(subcategory != ''){
                        $(".sub_category_id_div").show();
                       // $(".sub_cat").show();
                        if(subcategory) {
                            var subcategory_list = $.parseJSON(subcategory);
                            //console.log(subcategory_list);
                            $.each(subcategory_list, function(index, value){
                                $(".sub_category_id").append('<option value="'+value.category_id+'">'+value.category_name+'</option>');
                            });
                            
                            $(".sub_category_id").multipleSelect({
                                filter: true
                            });
                        }
                    }
                }
            });
        });
              
        //Add Data
        $('.submit_btn').click(function (e) {
            
            var selected = [];
            $('.sub_category_id .ms-drop > ul').find('li.selected label input[type=checkbox]').each(function(i){
                selected.push($(this).val());
            });
            
            var selected1 = [];
            $('.category_id .ms-drop > ul').find('li.selected label input[type=checkbox]').each(function(i){
                selected1.push($(this).val());
            });
            
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
            
            if($('.action').val() == 'add') {
                //Volume
                var volumne_value = frm.find('[name = "volumne_value"]').val();
                if (!volumne_value || !volumne_value.trim()) {
                    frm.find('[name = "volumne_value"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter volumne value'));
                    valid = false;
                } 

                //Volume Type
                var type = frm.find('[name = "type"]').val();
                if (!type || !type.trim()) {
                    frm.find('[name = "type"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter volumne type'));
                    valid = false;
                } 
            }

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);                
                data.append('sub_category_id', selected);
                data.append('category_id', selected1);
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
                        if (resp === 'exist') {
                            frm.find('[name = "brand_name"]').addClass('input_error').parents('.form-group').append(error_msg('Brand already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }else if (resp === 'success') {
                            if($("input[name='brand_id']").val() != ''){
                                $('.mdl_brand').modal('toggle');
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "brand";
                            }
                        }
                    }

                });
            }
        });
        
        $(document).on('click', '.edit_data', function () {  
            
            $('.ms-parent.category_id .ms-drop > ul').find('li.selected').removeClass('selected');
            $('.ms-parent.sub_category_id .ms-drop > ul').find('li.selected').removeClass('selected');
            $('select.category_id').multipleSelect('uncheckAll', true);
            $('select.sub_category_id').multipleSelect('uncheckAll', true);
            $(".sub_category_id_div").hide();
            $("button.ms-choice span").text('');
            $(".volume_hide").hide();                        
            $('.action').val('edit'); 
            
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
//                        console.log(brand);
                        
                        $('#brand_logo_view').html(brand.brand_logo);
                        $('#brand_logo_view').attr('href', '<?php echo S3_PATH; ?>brand/'+brand.brand_logo);
                        
                        $('#slider_img_view').html(brand.slider_img);
                        $('#slider_img_view').attr('href', '<?php echo S3_PATH; ?>brand/'+brand.slider_img);
                        
                        if(brand.is_top_brand == '1'){
                            $( ".is_top_brand_checkbox" ).attr( 'checked', 'checked' );
                            $(".slider_img_block").show();
                        } else if(brand.is_top_brand == '0') {
                            $( ".is_top_brand_checkbox" ).attr( 'checked', false );
                            $(".slider_img_block").hide();
                        }
                        
                        delete(brand.brand_logo);
                        delete(brand.slider_img);
                        $('[name="frm_add_brand"]').populate(brand);
                        
                        if(brand.sub_category.length > 0) {
                            $(".sub_category_id_div").show();
                            
			    $('select.sub_category_id').html('');
                            //sub category
                            $.each(brand.sub_category_list, function(index1, subcategory_list){   
                                $.each(subcategory_list, function(index, value){
                                    $('select.sub_category_id').append('<option value="'+value.category_id+'">'+value.category_name+'</option>').multipleSelect('refresh', true);
                                });                                
                            });
                           // $('select.sub_category_id').multipleSelect('refresh', true);
                            
                            $.each(brand.category, function(index2, value2){ 
                                //alert(value2.category_id);
                                $('.category_id .ms-drop > ul').find('li label input[value="' + value2.category_id + '"]').prop('checked',true);
                                $('.ms-parent.category_id').find('button.ms-choice span').append( $('.category_id .ms-drop > ul').find('li label input[value="' + value2.category_id + '"]').parent().find('span').text() + ',');
                                $('.category_id .ms-drop > ul').find('li label input[value="' + value2.category_id + '"]').parent().parent().addClass('selected');                                 
                            });
                            
                            $.each(brand.sub_category, function(index3, value3){
                               // alert(value3.category_id);
                                $('.sub_category_id .ms-drop > ul').find('li label input[value="' + value3.category_id + '"]').prop('checked',true);
                                $('.ms-parent.sub_category_id').find('button.ms-choice span').append( $('.sub_category_id .ms-drop > ul').find('li label input[value="' + value3.category_id + '"]').parent().find('span').text() + ',');
                                $('.sub_category_id .ms-drop > ul').find('li label input[value="' + value3.category_id + '"]').parent().parent().addClass('selected');                                 
                            });
                        }
                        else {
                            $(".sub_category_id_div").hide();
                            $.each(brand.category, function(index, value){ 
                                $('.category_id .ms-drop > ul').find('li label input[value="' + value.category_id + '"]').prop('checked',true);
                                $('.ms-parent.category_id').find('button.ms-choice span').append( $('.category_id .ms-drop > ul').find('li label input[value="' + value.category_id + '"]').parent().find('span').text() + ',');
                                $('.category_id .ms-drop > ul').find('li label input[value="' + value.category_id + '"]').parent().parent().addClass('selected');                                 
                            });
                        }
                    }
                }
             });
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
                url: "brand/upload_bulk",
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
                        alert('Brand Images Successfully.');
                        $('.upload_bulk_img').html('Save');
			$('.upload_bulk_img').attr("disabled", false);
                        window.location.reload();
                    }                     
                }

            });           
        });
	
	//Upload xls
        $('.upload_importxl').click(function (e) {
            e.preventDefault();
	    var progress_bar_id = '#progress-wrp2'; //ID of an element for response output
	    //reset progressbar
	    $(progress_bar_id +" .progress-bar").css("width", "0%");
	    $(progress_bar_id + " .status").text("0%");
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
                url: "brand/import_xls",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData:false,
		xhr: function(){
		    var xhr = new window.XMLHttpRequest();
		    xhr.upload.addEventListener("progress", function(evt) {
			if (evt.lengthComputable) {
			    var percentComplete = evt.loaded / evt.total;
			    //Do something with upload progress here
			    console.log(percentComplete);
			}
		    }, false);
		    return xhr;
		},
//		xhr: function(){			
//		    //upload Progress
//		    var xhr = $.ajaxSettings.xhr();
//		    if (xhr.upload) {
//			xhr.upload.addEventListener('progress', function(event) {
//				var percent = 0;
//				var position = event.loaded || event.position;
//				var total = event.total;
//				if (event.lengthComputable) {
//					percent = Math.ceil(position / total * 100);
//				}
//				//update progressbar
//				$(progress_bar_id +" .progress-bar").css("width", + percent +"%");
//				$(progress_bar_id + " .status").text(percent +"%");
//			}, true);
//		    }
//		    return xhr;
//		},
//		mimeType:"multipart/form-data",
                success: function (resp1)
                {
		    $('.upload_importxl').attr("disabled", false);
		    //console.log(resp);
                    if (resp1 == 'success') {
                        alert('Brands Uploaded Successfully.');
                        $('.upload_importxl').html('Save');
                        window.location.reload();
                    } 
                    else if(resp1 == '3'){
                       alert('Please add brand code in csv file.');
                       $('.upload_importxl').html('Save');     
                    }
		    else {
			var resp = $.parseJSON(resp1);
			if(resp[0] == '2'){
			    alert('Please add category code in csv file for brand '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '4'){
			    alert('Please add brand name in csv file for category code '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '5'){
			    alert('Please add brand logo in csv file for brand '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '6'){
			    alert('Please add brand logo name with extension in csv file for brand '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '7'){
			    alert('Product category code is not valid which is added in csv file for brand '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
		    }
                }
            });           
        });
    });    
</script>

