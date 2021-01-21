
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Manage Alcohol Awareness</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Alcohol Awareness</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-3">
	<a href="#" style="margin-top: 30px;" class="btn btn-success pull-left addawareness custombtn" data-toggle="modal" data-target=".mdl_alcohol_awareness">Add Alcohol Awareness Article</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">                
                <div class="ibox-title">
                    <h5>Manage Alcohol Awareness</h5>
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

<div class="modal fade mdl_alcohol_awareness" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
	<div class="modal-content">
	    <div class="modal-header">
		<button type="button" class="close closebtn" data-dismiss="modal" aria-label="Close">
		    <span aria-hidden="true">&times;</span>
		</button>
		<h4 class="modal-title" id="myModalLabel">Add Alcohol Awareness Article</h4>
	    </div>

	    <div class="modal-body">
		<form name="frm_add_alcohol_awareness" class="frm_add_alcohol_awareness form-horizontal" enctype="multipart/form-data">
		    <div class="panel-body">
			<div class="homediv">
			    <div class="row">                                            
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Article Title</b> </span>
					    <input type="text" class="form-control title" placeholder="Article Title" name="title">
				    </div>
				</div>
			    </div>
			    
			    <div class="row">
				<div class="col-sm-12">                            
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Article Image</b></span>
					<input type="file" class="form-control image" placeholder="Article Image" name="image">
					<div id="article_img"></div>                                                    
				    </div>
				</div> 
			    </div>

			    <div class="row">
				<div class="col-sm-12">
				    <div class="form-group">
					<span class="help-block m-b-none"><b>Article Description</b></span>
					<div class="summernote" style="width: 100%; height: 250px;" id="description" name="description" >
					</div>
					<input type="hidden" class="description" name="description" />
				    </div>
				</div> 
			    </div>
			</div>

			<input type="hidden" class="aid" name="aid">
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
            var frm = $('form[name = "frm_add_alcohol_awareness"]');
	    var description = $('#description').code();
            
            //Title
            var title = frm.find('[name = "title"]').val();
            if (!title || !title.trim()) {
                frm.find('[name = "title"]').addClass('input_error').parents('.form-group').append(error_msg('Please add article title'));
                valid = false;
            } 

            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
		data.append('description', description);
                //console.log(data);
                $.ajax({
                    url: "alcohol_awareness/add_alcohol_awareness",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);                        
                        if (resp === 'success'){
                            if($("input[name='aid']").val() != ''){
                                $('.mdl_alcohol_awareness').modal('toggle');
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "alcohol_awareness";
                            }
                        }
                    }

                });
            }
        });
	
	$(document).on('click', '.edit_data', function () {
            var aid = $(this).attr('data-aid');
	    $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'alcohol_awareness/get_alcohol_awareness',
                data: 'aid=' + aid,
                type: 'post',
                success: function (response) {
                    //console.log(response); 
		    var resp = $.parseJSON(response);
		    //console.log(resp);  
		    $("[name='action'").val('');
		    $("#article_img").html("");
                    $("[name='action'").val('edit');
		    $("[name='aid'").val(resp.aid);
		    //Image
		    if(resp.image != "") {
			$("#article_img").append('<br><a href="<?php echo S3_PATH.'alcohol_awareness/'; ?>'+resp.image+'" name="article_img_view" id="article_img_view">'+resp.image+'</a>'); 
			delete resp.image;
		    }
		    
		    //Title
		    $('[name="title"]').val(resp.title);
		    
		    //Description
		    $('[name="description"]').code(resp.description);            
                }
            });
	    return false;
        });
    });
</script>