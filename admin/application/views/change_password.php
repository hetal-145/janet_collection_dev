
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Change Password</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Change Password</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row"> </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Change Password</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="row">

                        <div class="col-md-4 col-md-offset-4">
                            <div class="ibox-content">

                                <h2 class="font-bold">Change password</h2>

                                <p>
                                    Enter your old password as current password
                                </p>

                                <div class="row">

                                    <div class="col-lg-12">
                                        <form class="m-t" role="form" name="frm_change_pass">
                                            <div class="form-group">
                                                <input type="password" name="password" class="form-control" placeholder="Current password" required="" >
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="new_password" class="new_password form-control" placeholder="New password" required="" >
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="cnf_password" class="form-control" placeholder="Re-type password" required="">
                                            </div>
                                            <div class="form-group">
                                                <span class="success_msg" style="color:green"> </span>
                                            </div>
                                            <a class="submit_btn btn btn-primary block full-width m-b">Change password</a>

                                        </form>
                                    </div>
                                </div>
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
        $('.submit_btn').click(function (e) {

            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_change_pass"]');



            var password = frm.find('[name="password"]').val();
            if (!password || !password.trim()) {
                frm.find('[name="password"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter password'));
                valid = false;
            }


            var new_password = frm.find('[name="new_password"]').val();
            if (!new_password || !new_password.trim()) {
                frm.find('[name="new_password"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter new password'));
                valid = false;
            } else {
                var pattern = new RegExp(/^[a-zA-Z0-9_-]{5,15}$/);
                if (pattern.test(new_password)) {
                } else {
                    frm.find('[name="new_password"]').addClass('input_error').parents('.form-group').append(error_msg('Password must be 5-15 charaters'));
                       valid = false;
                }
            }


            var cnf_password = frm.find('[name="cnf_password"]').val();
            if (!cnf_password || !cnf_password.trim()) {
                frm.find('[name="cnf_password"]').addClass('input_error').parents('.form-group').append(error_msg('Please re-type password'));
                valid = false;
            } else if (new_password != cnf_password) {
                frm.find('[name="cnf_password"]').addClass('input_error').parents('.form-group').append(error_msg('Password does not match'));
                valid = false;
            }


            if (valid) {
                $(this).html('Processing...');
                var datastring = $("form[name=\"frm_change_pass\"]").serialize();
                $.ajax({
                    url: 'setting/change_password',
                    data: datastring,
                    type: 'post',
                    success: function (data) {
                        $('.submit_btn').html('Change password');
                        if (data == 'update') {
                            $('.success_msg').html('Your password is successfully changed');
                        } else if (data == 'not_match') {
                            frm.find('[name="password"]').addClass('input_error').parents('.form-group').append(error_msg('Current password is not match'));
                        }
                    }
                });
            }
        });

    });
</script>
