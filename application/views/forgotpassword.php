<!--modal forgot password-->
<div class="modal fade" id="modal_forgotpassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-9 col-sm-9 col-lg-9 col-md-9 col-xl-9">
                        <h4 class="mb-0">Forgot Password</h4>
                    </div>
                    <div class="col-3 col-sm-3 col-lg-3 col-md-3 col-xl-3">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                        <p class="desc mb-0 mt-2">Please enter your login email address where the change password link will be sent.</p>
                    </div>                    
                </div>
                <div class="apply_form">
                    <form method="post" action="#" name="forgotpwd_form">
                        <div class="row">                            
                            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                                <div class="form-group">
                                    <input class="form-control" name="contact_email" id="contact_email" type="text" placeholder="E-mail">
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12 text-right">
                                <button class="btn btn-pink" id="btn_forgotpassword">Send</button>
                                <br>
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
<!--modal forgot password-->
<script>
$(document).ready(function(){
    $("#btn_forgotpassword").on("click", function() {
        $('.success_msg').html('');
        $('.error_msg').html('');
        $('.form-control').removeClass('input_error');
        var valid = true;
        var frm = $(this).closest($('form[name = "forgotpwd_form"]'));
        
        var contact_email = frm.find('[name = "contact_email"]').val();
        if (!contact_email || !contact_email.trim()) {
            frm.find('[name = "contact_email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your login email address'));
            valid = false;
        } 
            
        if (valid) {
            $(this).attr("disabled", "disabled");
            $(this).html('Processing...');
            var data = new FormData(frm[0]);
            $.ajax({
                url: "<?php echo base_url() . 'login/forgot_password'; ?>",
                type: "post",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data)
                {
		    var resp = $.parseJSON(data);
		    if (resp.status == "true") {
			frm.trigger('reset');
                        $("#btn_forgotpassword").removeAttr('disabled');
                        $("#btn_forgotpassword").html('Send');
                        $('.success_msg').html(resp.response_msg);
			setTimeout(function(){ 
			    $(".success_msg").html(""); 
			    $("#modal_forgotpassword").modal('hide');		    
			}, 3000);
                    } 
		    else if (resp.status == "false") {
			$("#btn_forgotpassword").removeAttr('disabled');
                        $("#btn_forgotpassword").html('Send');
                        $('.error_msg').html(resp.response_msg);
			valid = false;
                    }
                }
            });
        }
    });
});
</script>