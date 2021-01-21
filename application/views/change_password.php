<!--modal change password-->
<div class="modal fade" id="mdl_change_password" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-10">
                        <h4 class="mb-0">Change Password</h4>
                    </div>
                    <div class="col-2">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <p class="desc mb-0 mt-2">Update your new password</p>
                    </div>
                </div>
                <div class="apply_form">
                    <form action="#" method="post" name="frm_change_pass">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="form-group">
                                    <input class="form-control" name="password" type="password" placeholder="Current Password">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="form-group">
                                    <input class="form-control new_password" name="new_password" type="password" placeholder="New Password">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="form-group">
                                    <input class="form-control" name="cnf_password" type="password" placeholder="Re-type password">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-right">
                                <button type="button" class="btn btn-pink" id="changePasswordBtn">Save</button>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <span class="success_msg" style="color:green"> </span>
                                <span class="error_msg" style="color:red"> </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal change password-->
<script>
$(document).ready(function () {
    $('#changePasswordBtn').click(function (e) {
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
            $(this).attr("disabled", "disabled");
            $(this).html('Processing...');
            var datastring = $("form[name=\"frm_change_pass\"]").serialize();
            $.ajax({
                url: "<?php echo base_url() . 'login/change_password'; ?>",
                data: datastring,
                type: 'post',
                success: function (data) {
                   // console.log(data);
		    var resp = $.parseJSON(data);
                    $("#changePasswordBtn").removeAttr('disabled');
                    $('#changePasswordBtn').html('Change password');
//		    console.log(resp);
                    if (resp.status == "true") {
			setTimeout(function(){ 
			    $(".success_msg").html(""); 
			    $("#mdl_change_password").modal('hide');		    
			}, 3000);
                        $('.success_msg').html(resp.response_msg);
                    } 
		    else if (resp.status == "false") {
                        $('.error_msg').html(resp.response_msg);
			valid = false;
                    }
                }
            });
        }
    });

});
</script>