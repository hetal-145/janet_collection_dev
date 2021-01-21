
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Email Settings</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Email Settings</strong>
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
                    <h5>Email Settings</h5>

                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form name="frm_email_setting" class="frm_email_setting form-horizontal" >
                        <div class="panel-body">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Email (*) </b></span>
                                    <input type="text" class="form-control duration" placeholder="Email"name="smtp_user">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>Password (*) </b></span>
                                    <input type="password" class="form-control duration" placeholder="Password"name="smtp_pass">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>SMTP Port (*) </b></span>
                                    <input type="NUMBER" class="form-control duration" placeholder="SMTP Port"name="smtp_port">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <span class="help-block m-b-none"><b>SMTP Host (*) </b></span>
                                    <input type="text" class="form-control duration" placeholder="SMTP Host"name="smtp_host">
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




<?php
if (isset($setting_data) && $setting_data) {
    ?>
    <script>
        $(document).ready(function () {
            var formdata = {
                'smtp_user': '<?= $setting_data[0]['value'] ?>',
                'smtp_pass': '<?= $setting_data[1]['value'] ?>',
                'smtp_port': <?= $setting_data[3]['value'] ?>,
                'smtp_host': '<?= $setting_data[2]['value'] ?>'
            };
            $('[name="frm_email_setting"]').populate(formdata);

<?php } ?>
    });
</script>


<script>
    $(document).ready(function () {
        $('.submit_btn').click(function (e) {
            $('.success_msg').html('');
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_email_setting"]');
            var smtp_user = frm.find('[name = "smtp_user"]').val();
            if (!smtp_user || !smtp_user.trim()) {
                frm.find('[name = "smtp_user"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter the email'));
                valid = false;
            }
            var smtp_pass = frm.find('[name = "smtp_pass"]').val();
            if (!smtp_pass || !smtp_pass.trim()) {
                frm.find('[name = "smtp_pass"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter the password'));
                valid = false;
            }
            var smtp_port = frm.find('[name = "smtp_port"]').val();
            if (!smtp_port || !smtp_port.trim()) {
                frm.find('[name = "smtp_port"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter the port no'));
                valid = false;
            }
            var smtp_host = frm.find('[name = "smtp_host"]').val();
            if (!smtp_host || !smtp_host.trim()) {
                frm.find('[name = "smtp_host"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter the host name'));
                valid = false;
            }

            if (valid) {
                $(this).html('Processing...');
                var data = $(".frm_email_setting").serialize();
                $.ajax({
                    url: "setting/update_email_setting",
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

