
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>Manage Volumes</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Manage Volumes</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-4">
        <a href="#" style="margin-top:30px;" id="add_volume" class="btn btn-primary pull-right" data-toggle="modal" data-target=".mdl_volume">Add Volume</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">                
                
                <div class="ibox-title">
                    <h5>Manage Volumes</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <?php echo $content; ?>
                </div>
                
                <div class="modal fade mdl_volume" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="myModalLabel">Add Volume </h4>
                            </div>

                            <div class="modal-body">
                                <form name="frm_add_volume" class="frm_add_volume form-horizontal">
                                    <div class="panel-body row">

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Brand(*)</b> </span>
                                                <select class="form-control brand_id" name="brand_id">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach($brands as $brand) { ?>
                                                        <option value="<?php echo $brand["brand_id"]; ?>"><?php echo $brand["brand_name"]; ?></option>
                                                    <?php } ?>                                  
                                                </select>
                                            </div>
                                        </div>                            

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Volume(*)</b></span>
                                                <input maxlength="5" type="text" class="form-control numeric volumne_value" placeholder="Volume" name="volumne_value">
                                            </div>
                                        </div> 

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <span class="help-block m-b-none"><b>Type(*)</b> </span>
                                                <select class="form-control type" name="type">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach($volumes as $volume) { ?>
                                                        <option value="<?php echo $volume["volume_type_id"]; ?>"><?php echo $volume["volume_type"]; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" class="volume_id" name="volume_id">
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
        
        $("#add_volume").click(function(){
            $('form[name = "frm_add_volume"]')[0].reset();
        });
        
        $("#close_btn").click(function(){
            $('form[name = "frm_add_volume"]')[0].reset();
        });
        //Add Volume        
        $('.submit_btn').click(function (e) {
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_add_volume"]');
            
            //Category
            var volumne_value = frm.find('[name = "volumne_value"]').val();
            if (!volumne_value || !volumne_value.trim()) {
                frm.find('[name = "volumne_value"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter volume'));
                valid = false;
            } else if( !$.isNumeric(volumne_value) ){
                frm.find('[name = "volumne_value"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter numbers only.'));
                valid = false;
            }
            
            //Brand
            var brand_id = frm.find('[name = "brand_id"]').val();            
            if (brand_id == '' || brand_id == '0' || !brand_id.trim()) {
                frm.find('[name = "brand_id"]').addClass('input_error').parents('.form-group').append(error_msg('Please select brand'));
                valid = false;
            }   
            
            //Volume
            var type = frm.find('[name = "type"]').val();
            if (type == '' || type == '0' || !type.trim()) {
                frm.find('[name = "type"]').addClass('input_error').parents('.form-group').append(error_msg('Please select volume type'));
                valid = false;
            }   
            
            
            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
                //console.log(data);
                $.ajax({
                    url: "volume/save",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);
                        if (resp == 'exist') {
                            frm.find('[name = "volumne_value"]').addClass('input_error').parents('.form-group').append(error_msg('Volume already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }else{
                            if($("input[name='volume_id']").val() != ''){
                                $('.mdl_volume').modal('toggle');                                
                                $('.submit_btn').html('Save');
                                Xcrud.reload();
                            }
                            else {
                                window.location = "volume";
                            }
                        }
                    }

                });
            }
        });
        
        $(document).on('click', '.edit_data', function () {
            var volume_id = $(this).attr('data-primary');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            $.ajax({
                url: 'volume/get_volume',
                data: 'volume_id=' + volume_id,
                type: 'post',
                success: function (volume) {
                    if (volume) {
                        volume = JSON.parse(volume);
                        console.log(volume);
                        
                        $('[name="frm_add_volume"]').populate(volume);
                    } 
                }
            });
        });

    });
</script>

