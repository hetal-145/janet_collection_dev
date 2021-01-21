<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Social Media Links</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Social Media Links</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight col-sm-6 col-sm-offset-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Social Media Links</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form name="frm_social_setting" class="frm_social_setting form-horizontal" >
                        <div class="panel-body">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Facebook </b></span>
                                    <input type="text" class="form-control" placeholder="Facebook"name="facebook" value="<?php echo $setting_data[23]['value']; ?>" />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Instagram </b></span>
                                    <input type="text" class="form-control" placeholder="Instagram"name="instagram" value="<?php echo $setting_data[24]['value']; ?>" />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Twitter </b></span>
                                    <input type="text" class="form-control" placeholder="Twitter"name="twitter" value="<?php echo $setting_data[25]['value']; ?>" />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Linked In </b></span>
                                    <input type="text" class="form-control" placeholder="Linked In"name="linkedin" value="<?php echo $setting_data[26]['value']; ?>" />
                                </div>
                            </div>
			    <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Youtube Channel </b></span>
                                    <input type="text" class="form-control" placeholder="Youtube Channel"name="youtube" value="<?php echo $setting_data[27]['value']; ?>" />
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
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_social_setting"]');
            
            if (valid) {
                $(this).html('Processing...');
                var data = $(".frm_social_setting").serialize();
                $.ajax({
                    url: "setting/update_settings",
                    type: "post",
                    data: data,
                    success: function (plan)
                    {
                        if (plan) {

                            $('.submit_btn').html('Save');
                            $('.success_msg').html('Saved successfully ');
                        }
                    }

                });
            }
        });
    });
</script>

