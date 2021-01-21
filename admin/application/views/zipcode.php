
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Manage Zip Code</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Zip Code</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Manage Zip Code</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">                                        
                    <a href="#" class="btn btn-warning newadd pull-left select_all"><i class="fa fa-plus"></i> Select All</a>
                    <a href="#" class="btn btn-danger newadd pull-left delete_all"><i class="fa fa-trash"></i> Delete All</a>
                    <a href="#" class="btn btn-primary newadd pull-left importxl" data-toggle="modal" data-target=".mdl_importxl"><i class="fa fa-upload"></i> Import Excel</a>
		    
		    <a href="zipcode/export_xls" class="btn btn-info pull-left exportxls custombtn" data-type="product">Export / Download Excel</a>
                    
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
        
        //Select All
        var clicked = false;
        $("a.select_all").click(function(e) {
            e.preventDefault();           
            $(".zipcode_id_checkbox").prop("checked", !clicked);
            clicked = !clicked;
        });
        
        //delete all
        $("a.delete_all").click(function(e) {
            e.preventDefault();
            var zipcode_box = [];
            $.each($("input[name='zipcode_id']:checked"), function(){  
                zipcode_box.push($(this).data('zipcode_id'));
            }); 
            
            //console.log(zipcode_box);
            
            $.ajax({
                url: "zipcode/delete_all",
                type: "post",
                data: {'zipcode':zipcode_box},
                success: function (resp)
                { 
//                    console.log(resp);
                    if (resp == 'success') {
                        alert('Zipcode Deleted Successfully.');
                        window.location.reload();
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
                url: "zipcode/import_xls",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData:false,
                success: function (resp)
                { 
//                    console.log(resp);
                    if (resp == 'success') {
                        alert('Zipcode Uploaded Successfully.');
                        $('.upload_importxl').html('Save');
                        window.location.reload();
                    }
                }

            });
           
        });
        
    });
</script>