<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Third Party Keys Settings</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Third Party Keys Settings</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight col-sm-10 col-sm-offset-1">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Third Party Keys Settings</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form name="frm_settings" class="frm_settings form-horizontal" >
                        <div class="panel-body">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>API Client Key (*) </b></span>
                                    <input type="text" name="third_party_api_client_key" class="form-control" placeholder="" >
                                </div>
                            </div>
			    
			    <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>API Secret Key (*) </b></span>
                                    <input type="text" name="third_party_api_secret" class="form-control" placeholder="" >
                                </div>
                            </div>
			    
			    <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>API OAuth token (*) </b></span>
                                    <textarea rows="5" name="third_party_api_oauth_token" class="form-control"></textarea>
                                </div>
                            </div>
			    
			    <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Client ID </b></span>
                                    <input type="text" name="third_party_api_client_id" class="form-control" placeholder="" >
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
<?php // echo "<pre>"; print_r($setting_data); exit; ?>

<?php
if (isset($setting_data) && $setting_data) {
    ?>
    <script>
        $(document).ready(function () {
            
           var formdata = {                
                'third_party_api_client_key': '<?= (isset($setting_data[42]['value'])) ? $setting_data[42]['value'] : ''; ?>',
                'third_party_api_secret': '<?= (isset($setting_data[43]['value'])) ? $setting_data[43]['value'] : ''; ?>',
                'third_party_api_oauth_token': '<?= (isset($setting_data[44]['value'])) ? $setting_data[44]['value'] : ''; ?>',
                'third_party_api_client_id': '<?= (isset($setting_data[45]['value'])) ? $setting_data[45]['value'] : ''; ?>'
            };
            $('[name="frm_settings"]').populate(formdata);

<?php } ?>
    });
</script>

<script>
    $(document).ready(function () {       
            
        $('.submit_btn').click(function (e) {

            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_settings"]');

            var third_party_api_client_key = frm.find('[name="third_party_api_client_key"]').val();
            if (!third_party_api_client_key || !third_party_api_client_key.trim()) {
                frm.find('[name="third_party_api_client_key"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter client key'));
                valid = false;
            }
            
            var third_party_api_secret = frm.find('[name="third_party_api_secret"]').val();
            if (!third_party_api_secret || !third_party_api_secret.trim()) {
                frm.find('[name="third_party_api_secret"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter secret key'));
                valid = false;
            }
            
            var third_party_api_oauth_token = frm.find('[name="third_party_api_oauth_token"]').val();
            if (!third_party_api_oauth_token || !third_party_api_oauth_token.trim()) {
                frm.find('[name="third_party_api_oauth_token"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter OAuth token'));
                valid = false;
            }

            if (valid) {
                $(this).html('Processing...');
                var datastring = $("form[name=\"frm_settings\"]").serialize();
                $.ajax({
                    url: 'setting/update_settings',
                    data: datastring,
                    type: 'post',
                    success: function (data) {
                        $('.submit_btn').html('Save Settings');
                        if (data == 'success') {
                            $('.success_msg').html('Your settings is successfully saved');
                        } 
                    }
                });
            }
        });

    });
</script>
