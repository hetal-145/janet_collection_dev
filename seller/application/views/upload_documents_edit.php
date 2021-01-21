<?php //echo "<pre>"; print_r($documents); exit; ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Uploaded Documents</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Edit Uploaded Documents</strong>
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
                    <h5>Edit Uploaded Documents</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="panel-body">
                        <form name="frm_add_user" class="frm_add_user form-horizontal" enctype="multipart/form-data">
                            <div  class="row">   
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <span class="help-block m-b-none"><b>Name</b></span>
                                        <input type="text" class="form-control name" placeholder="Name" name="name" value="<?php echo $seller_name["seller_name"]; ?>" disabled="disabled">
                                        <input type="hidden" class="form-control seller_id" name="seller_id" value="<?php echo $seller_id; ?>">
                                    </div>
                                </div>
                            </div>
			    <?php $j=1; for($i=0; $i<4; $i++) { ?>
                            <div  class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <span class="help-block m-b-none"><b>Verification Document <?php echo $j; ?></b></span>
                                        <input type="file" class="form-control doc_name" name="doc_name[]">
                                    </div>
                                </div>
                                
                                <div class="col-sm-3">
                                    <div class="form-group pull-right">
                                        <?php if(!empty($documents[$i]["doc_name"])) { ?>
                                        <a target="_blank" href="<?php echo S3_PATH.'seller/',$documents[$i]["doc_name"]; ?>"><img src="<?php echo S3_PATH.'seller/',$documents[$i]["doc_name"]; ?>" width="150px" height="150px"></a>
                                        <?php } ?>
                                    </div>
                                </div>
				
				 <div class="col-sm-1">
                                    <div class="form-group pull-right">
                                        <?php if(!empty($documents[$i]["doc_name"])) { ?>
                                        <button data-id="<?php echo $documents[$i]["id"] ?>" type="button" class="btn btn-danger delete" name="del_<?php echo $documents[$i]["id"] ?>"><i class="fa fa-trash"></i></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php $j++; } ?>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <a class="form-control btn submit_btn btn-primary pull-right" style="background-color: #1ab394;border-color: 1ab394;color: #FFFFFF;margin-top: 10px;margin-bottom: 10px;">Update Documents</a>
                                    <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
                                    <span class="error_msg pull-right" style="color:red; padding: 7px;"></span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
	
	$(".delete").click(function (e) {
	    var id = $(this).data("id");
	    console.log(id);
	    $.ajax({
		url: "upload_documents/delete",
		type: "post",
		data: "id="+id,
		success: function (resp)
		{
		    //console.log(resp);
		    if (resp === 'success'){
			$('.submit_btn').html('Save');
			window.location.reload();
		    }
		}
	    });
	});
        
        $('.submit_btn').click(function (e) {
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_user"]');
            
            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
                //console.log(data);
                $.ajax({
                    url: "upload_documents/save",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);
                        if (resp === 'success'){
                            $('.submit_btn').html('Save');
                            window.location = "upload_documents";
                        }
                        else if (resp === 'noexists'){
                           $(".error_msg").append(error_msg('No data found.'));
                        }
                    }
                });
            }
        });
    });
</script>
