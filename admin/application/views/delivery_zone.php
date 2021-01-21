<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Delivery Zone</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Delivery Zone</strong>
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
                    <h5>Manage Delivery Zone</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content"> 
		    <a href="#" class="btn btn-primary pull-left importxl custombtn" data-toggle="modal" data-target=".mdl_importxl">Import Excel</a>		    
		    <a href="delivery_zone/export_xls" class="btn btn-info pull-left exportxls custombtn">Export / Download Excel</a>		    
		    <a href="#" class="btn btn-warning pull-left selectall custombtn">Select All In Page</a>
		    <a href="#" class="btn btn-info pull-left deselectall custombtn">Deselect All In Page</a>
		    <a href="#" class="btn btn-success pull-left active_all custombtn">Activate Selected</a>
		    <a href="#" class="btn btn-danger pull-left deactive_all custombtn">Deactivate Selected</a>
                    <span class="clearfix"></span>
                    <?php echo $content; ?>
                </div>
                
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

	//active all
	$(".active_all").on("click", function(e){
	    e.preventDefault();

	    var selected = [];
	    $(".select_multiple_checkbox:checkbox:checked").each(function() {
		selected.push($(this).val());
	    });

	    if(selected == '') {
		alert('Please select delivery charge.');
		return false;
	    }

	    $.ajax({
		url: "delivery_zone/active_all",
		type: "post",
		data: "pids="+selected,
		success: function (resp)
		{ 
		    if (resp == 1) {
			alert('Selected Delivery Zones Activated.');
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
		alert('Please select delivery charge.');
		return false;
	    }

	    $.ajax({
		url: "delivery_zone/deactive_all",
		type: "post",
		data: "pids="+selected,
		success: function (resp)
		{ 
		    if (resp == 1) {
			alert('Selected Delivery Zones Deactivated.');
			Xcrud.reload();
		    } 
		}
	    });	    
	});	
        
        //Upload xls
        $('.upload_importxl').click(function (e) {
            e.preventDefault();
            
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
                url: "delivery_zone/import_xls",
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
			alert('Delivery Zones Uploaded Successfully.');
			$('.upload_importxl').html('Save');
			window.location.reload();
		    } 
		    else if(resp1 == '1'){
		       alert('Please add city name in csv file.');
		       $('.upload_importxl').html('Save');     
		    }
		    else if(resp1 == '2'){
		       alert('Please add area code in csv file.');
		       $('.upload_importxl').html('Save');     
		    }
                }
            });           
        });
        
    });
</script>