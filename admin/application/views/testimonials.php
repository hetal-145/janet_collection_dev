
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Testimonials</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Testimonials</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
	<a href="#" style="margin-top: 30px;" class="btn btn-success pull-left addtestimonials custombtn" data-toggle="modal" data-target=".mdl_testimonials">Add Testimonial</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">                
                <div class="ibox-client_name">
                    <h5>Manage Testimonials</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <span class="clearfix"></span>
                    <?php echo $content; ?>
                </div>            
            </div>
        </div>
    </div>
</div>

<div class="modal fade mdl_testimonials" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
	<div class="modal-content">
	    <div class="modal-header">
		<button type="button" class="close closebtn" data-dismiss="modal" aria-label="Close">
		    <span aria-hidden="true">&times;</span>
		</button>
		<h4 class="modal-client_name" id="myModalLabel">Add Testimonial</h4>
	    </div>

	    <div class="modal-body">
		<form name="frm_add_testimonials" class="frm_add_testimonials form-horizontal" enctype="multipart/form-data">
		    <div class="panel-body">
			<div class="homediv">
			    <div class="row">                                            
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Client Name</b> </span>
					    <input type="text" class="form-control client_name" placeholder="Client Name" name="client_name">
				    </div>
				</div>
			    </div>
			    
			    <div class="row">
				<div class="col-sm-12">                            
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Image</b></span>
					<input type="file" class="form-control image" placeholder="Image" name="image">
					<div id="article_img"></div>                                                    
				    </div>
				</div> 
			    </div>

			    <div class="row">
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Description</b></span>
					<div class="summernote" style="width: 100%; height: 250px;" id="description" name="description" >
					</div>
					<input type="hidden" class="description" name="description" />
				    </div>
				</div> 
			    </div>
			</div>

			<input type="hidden" class="testimonials_id" name="testimonials_id">
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

<script>
    $(document).ready(function () {
        $('.summernote').summernote({
            height: 250,
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['view', ['fullscreen', 'codeview']],
            ]
        });
	
	//Add Article        
        $('.submit_btn').click(function (e) {
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_testimonials"]');
	    var description = $('#description').code();
            
            //client_name
            var client_name = frm.find('[name = "client_name"]').val();
            if (!client_name || !client_name.trim()) {
                frm.find('[name = "client_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please add article client_name'));
                valid = false;
            } 

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
		data.append('description', description);
                //console.log(data);
                $.ajax({
                    url: "testimonials/add_testimonials",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);                        
                        if (resp === 'success'){
                            if($("input[name='testimonials_id']").val() != ''){
                                $('.mdl_testimonials').modal('toggle');
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "testimonials";
                            }
                        }
                    }

                });
            }
        });
	
	$(document).on('click', '.edit_data', function () {
            var testimonials_id = $(this).attr('data-testimonials_id');
	    //alert(testimonials_id); return false;
	    $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'testimonials/get_testimonials',
                data: 'testimonials_id=' + testimonials_id,
                type: 'post',
                success: function (response) {
                    //console.log(response); 
		    var resp = $.parseJSON(response);
		    //console.log(resp);  
		    $("[name='action'").val('');
		    $("#article_img").html("");
                    $("[name='action'").val('edit');
		    $("[name='testimonials_id'").val(resp.testimonials_id);
		    //Image
		    $("#article_img").append('<br><a href="<?php echo S3_PATH.'testimonials/'; ?>'+resp.image+'" name="article_img_view" id="article_img_view">'+resp.image+'</a>'); 
		    delete resp.image;
		    
		    //client_name
		    $('[name="client_name"]').val(resp.client_name);
		    
		    //Description
		    $('[name="description"]').code(resp.description);            
                }
            });
	    return false;
        });
    });
</script>