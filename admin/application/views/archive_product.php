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
    <div class="row"> </div>
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
		    
		    <a href="product/export_xls_archive" class="btn btn-info pull-left exportxls custombtn" data-type="product">Export / Download Excel</a>
		    
		    <a href="#" class="btn btn-primary pull-left selectall custombtn">Select All Products In Page</a>
		    <a href="#" class="btn btn-info pull-left deselectall custombtn">Deselect All Products In Page</a>
		    <a href="#" class="btn btn-warning pull-left edit_all custombtn">Edit Price For Selected Products</a>
		    <a href="#" class="btn btn-success pull-left active_all custombtn">Activate Selected</a>
		    <a href="#" class="btn btn-danger pull-left deactive_all custombtn">Deactivate Selected</a>

                    <span class="clearfix"></span>
                    <?php echo $content; ?>
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
        
                
        //Upload xls
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
                       alert('Please add product name in csv file.');
                       $('.upload_importxl').html('Save');     
                    }
		    else {
			var resp = $.parseJSON(resp1);
			if(resp[0] == '2'){
			    alert('Please add product description in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '3'){
			    alert('Please product actual stock value should be in digits in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '4'){
			    alert('Please add product minimum stock value in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '5'){
			    alert('Please add product maximum stock value in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '6'){
			    alert('Please add seller code in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '7'){
			    alert('Please add supplier code in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '8'){
			    alert('Please add pack size in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '9'){
			    alert('Please add product volume in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '10'){
			    alert('Please add volume type in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '11'){
			    alert('Please add product brand code in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '12'){
			    alert('Please add product category code in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '13'){
			    alert('Please add product drink type in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '14'){
			    alert('Please add actual (mrp) product price in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '15'){
			    alert('Please add product image name in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '16'){
			    alert('Please product drink type should be in digits in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '17'){
			    alert('Please add product image name with extension in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '18'){
			    alert('Product category code is not valid which is added in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '19'){
			    alert('Product brand code is not valid which is added in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '20'){
			    alert('Product seller code is not valid which is added in csv file for product '+resp[1]);
			    $('.upload_importxl').html('Save');     
			 }
			 else if(resp[0] == '21'){
			    alert('Product supplier code is not valid which is added in csv file for product '+resp[1]);
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
    });
</script>
