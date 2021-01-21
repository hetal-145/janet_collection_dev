<?php //echo "<pre>"; print_r($driver); exit; ?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Add Delivery Zone</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Add Delivery Zone</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight col-sm-6 col-sm-offset-3">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Add Delivery Zone</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form name="frm_delivery_zone" class="frm_delivery_zone form-horizontal">
                        <div class="panel-body">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Driver Pic </b></span>
                                    <img width="100px" height="100px" src="<?php echo S3_PATH.$driver["profile_image"]; ?>" />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Driver Name </b></span>
                                    <label class="form-control"><?php echo $driver["firstname"]." ".$driver["lastname"]; ?></label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Driver Email ID </b></span>
                                    <label class="form-control"><?php echo $driver["email"]; ?></label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Driver Contact No </b></span>
                                    <label class="form-control"><?php echo $driver["country_code"]." ".$driver["mobileno"]; ?></label>
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <input type="hidden" name="user_id" id="user_id" value="<?php echo $driver["user_id"]; ?>" />
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Delivery Zones</b></span>
                                    <select name="dzone_id" id="dzone_id" class="form-control">                                        
                                        <option value="">--Select Delivery Zone--</option>
                                        <?php if(!empty($delivery_zones)) { foreach($delivery_zones as $zones) { ?>
                                        <option value="<?php echo $zones["dzone_id"]; ?>" <?php if($driver["dzone_id"] == $zones["dzone_id"]) { ?> selected="selected" <?php } ?>><?php echo $zones["city"]." => ".$zones["area_code"]; ?></option>
                                        <?php } } ?>
                                    </select>
                                </div>
                            </div>

                            <a class="btn submit_btn btn-primary pull-right">Save</a>
                            <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
                        </div>
                    </form>
                </div>            
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {       
            
        $('.submit_btn').click(function (e) {

            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_delivery_zone"]');            
            
            var dzone_id = frm.find('[name="dzone_id"]').val();
            if (!dzone_id || !dzone_id.trim()) {
                frm.find('[name="dzone_id"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter delivery zone'));
                valid = false;
            }

            if (valid) {
                $(this).html('Processing...');
                var datastring = $("form[name=\"frm_delivery_zone\"]").serialize();
                $.ajax({
                    url: 'drivers/add_zone',
                    data: datastring,
                    type: 'post',
                    success: function (data) {
                        $('.submit_btn').html('Save Delivery Zone');
                        if (data == 'success') {
                            $('.success_msg').html('Your Delivery Zone is successfully saved');
                        } 
                    }
                });
            }
        });

    });
</script>
