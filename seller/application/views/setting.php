<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Settings</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Settings</strong>
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
                    <h5>Settings</h5>

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
                                    <span class="help-block m-b-none"><b>Email address where seller wants all notification mails (*) </b></span>
                                    <input type="text" name="seller_email_address" class="form-control" placeholder="Email address where seller wants all notification mails" >
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
<?php //echo "<pre>"; print_r($setting_data); exit; ?>

<?php
if (isset($setting_data) && $setting_data) {
    ?>
    <script>
        $(document).ready(function () {
            
           var formdata = {                
                'seller_email_address': '<?= (isset($setting_data[19]['value'])) ? $setting_data[19]['value'] : ''; ?>',
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

            var seller_email_address = frm.find('[name="seller_email_address"]').val();
            if (!seller_email_address || !seller_email_address.trim()) {
                frm.find('[name="seller_email_address"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter email address where seller wants all notification mails'));
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
