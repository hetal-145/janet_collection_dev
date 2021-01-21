<script src="https://apis.google.com/js/platform.js" async defer></script>
<meta name="google-signin-scope" content="profile email">
<meta name="google-signin-client_id" content="950886894490-um9v0pr5mhnmpopqo2n9dloiqgj1k6lp.apps.googleusercontent.com">

<style>
    .abcRioButtonLightBlue {
	height: 30px !important;
	width: 32px !important;
	box-shadow: none !important;
    }
    
    .abcRioButtonIcon {
	padding: 5px !important;
    }
</style>

<!--login register-->
<div class="modal fade" id="modal_login_resiter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row ml-0 mr-0">
                    <div class="col-5 col-lg-5 col-xl-5 content-left d-none d-sm-none d-md-none d-lg-block d-xl-block">
                        <p class="lbl_text"></p>
                    </div>
                    <div class="col-12 col-md-12 col-md-12 col-lg-7 col-xl-7 content-right">
                        <div class="row">
                            <div class="col-8">
                                <nav class="mb-3">
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link active ml-0" data-toggle="tab" href="#login" role="tab">Sign in</a>
                                        <a class="nav-item nav-link" data-toggle="tab" href="#Register" role="tab">Register</a>
                                    </div>
                                </nav>
                            </div>
                            <div class="col-4">
                                <button class="close pt-2" data-dismiss="modal"><span class="mdi mdi-close"></span></button>
                            </div>
                        </div>

                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="nav-home-tab">
                                <form method="post" action="#" name="loginform" id="loginform">
                                    <div class="form-group">
                                        <label>E-mail</label>
                                        <input type="text" name="email" id="email" class="form-control" placeholder="E-mail@company.com">
                                    </div>
                                    <div class="form-group text-center">
                                        <small>OR</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" name="mobileno" maxlength="15" id="mobileno" class="form-control numeric" placeholder="+44 123 456 7890">
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" id="password" class="form-control" placeholder="********">
                                    </div>
                                    <div class="row mt-4">
					<div class="col-8 align-self-center txt-mob-center">
                                            <ul class="signup_with">
                                                <li><label>Sign up with :</label></li>
                                                <li><a class="fb-login-button" scope="public_profile" onlogin="checkLoginState();"></a></li>
                                                <li><a class="g-signin2" href="javascript:void(0);" id="signin_with_google" data-onsuccess="onSignIn"></a></li>
                                            </ul>
                                        </div>
                                        
                                        <div class="col-4 text-right">
                                            <button type="button" class="btn btn-pink" id="btn_sigin">Sign in</button>

                                        </div>
                                        <div class="col-12">
                                            <span class="success_msg" style="color:green"> </span>
                                            <span class="error_msg" style="color:red"> </span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12 col-sm-12 col-md-9 col-lg-9 col-xl-9 align-self-center ">
                                            <a id="mdl_forgotpassword" href="javascript:void(0);" data-toggle="modal" data-target="#modal_forgotpassword">Forgot Password?</a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="Register" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <form method="post" action="#" name="registerform" id="registerform">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" name="firstname" id="firstname" class="form-control" placeholder="John">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Doe">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>Birth Date</label>
                                                <input type="text" autocomplete="off" name="birthdate" id="birthdate" class="form-control birt_date_picker" placeholder="YYYY/MM/DD">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>Mobile</label>
                                                <input type="text" name="mobileno" maxlength="15" id="mobileno1" class="form-control numeric" placeholder="+44 123 456 7890">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>E-mail</label>
                                                <input type="email" name="email" id="email1" class="form-control" placeholder="E-mail@company.com">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>Password</label>
                                                <input type="password" name="password" id="password1" class="form-control" placeholder="********">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input type="password" name="cnf_password" id="cnf_password" class="form-control" placeholder="********">
                                            </div>
                                        </div>
                                    </div>
				    
                                    <div class="row mt-2">
                                        <div class="col-12 col-sm-12 col-md-9 col-lg-9 col-xl-9 align-self-center txt-mob-center"></div>
                                        <div class="col-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 text-right txt-mob-center">
                                            <button type="button" class="btn btn-pink" id="btn_register">Register</button>
                                        </div>
                                        <div class="col-12">
                                            <span class="success_msg1" style="color:green"> </span>
                                            <span class="error_msg1" style="color:red"> </span>
                                        </div>
                                        <div class="col-12 text-center mt-2">
                                            <small>By signing up you agree with the <a href="<?php echo base_url() . 'home/terms_and_conditions'; ?>">Terms of service</a> and <a href="<?php echo base_url() . 'home/privacy_policy'; ?>">Privacy Policy</a></small>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-none">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<!--login register-->
<script>   
//General Login
$(document).ready(function () {
    //normal signin with email / mobile
    $("#btn_sigin").on("click", function () {
	$('.success_msg').html('');
	$('.error_msg').html('');
	$('.form-control').removeClass('input_error');
	var valid = true;
	var frm = $(this).closest($('form[name = "loginform"]'));

	var email = frm.find('[id = "email"]').val();
	var mobileno = frm.find('[id = "mobileno"]').val();
	if (!email && !mobileno) {
	    frm.find('[name = "email"]').parents('.form-group').find(".error").remove();
	    frm.find('[name = "email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your username (email or mobileno)'));

	    frm.find('[name = "mobileno"]').parents('.form-group').find(".error").remove();
	    frm.find('[name = "mobileno"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your username (email or mobileno)'));
	    valid = false;
	}

	var password = frm.find('[name = "password"]').val();
	if (!password || !password.trim()) {
	    frm.find('[name = "password"]').parents('.form-group').find(".error").remove();
	    frm.find('[name = "password"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter password'));
	    valid = false;
	}

	if (valid) {
	    $(this).attr("disabled", "disabled");
	    $(this).html('Processing...');
	    var data = new FormData(frm[0]);
	    $.ajax({
		url: "<?php echo base_url() . 'login/user_login'; ?>",
		type: "post",
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		success: function (resp)
		{
//                    console.log(resp);
//                    $("#btn_sigin").removeAttr('disabled');
//                    $("#btn_sigin").html('Save');
		    if (resp === '1') {
			frm.trigger('reset');
			$("#btn_sigin").removeAttr('disabled');
			$("#btn_sigin").html('Save');
			$(".success_msg").html("Login Successfull!");
			//location = "<?php echo base_url() . 'home'; ?>";
			location.reload();
		    } else if (resp === '0') {
			$("#btn_sigin").removeAttr('disabled');
			$("#btn_sigin").html('Save');
			$(".error_msg").html("Invalid username or password!");
			valid = false;
		    }
		}
	    });
	}
    });
    
    //register yourself
    $("#btn_register").on("click", function () {
	$('.success_msg1').html('');
	$('.error_msg1').html('');
	$('.form-control').removeClass('input_error');
	var valid = true;
	var frm = $(this).closest($('form[name = "registerform"]'));

	var firstname = frm.find('[name = "firstname"]').val();
	if (!firstname || !firstname.trim()) {
	    frm.find('[name = "firstname"]').parents('.form-group').find(".error").remove();
	    frm.find('[name = "firstname"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your first name'));
	    valid = false;
	}

	var lastname = frm.find('[name = "lastname"]').val();
	if (!lastname || !lastname.trim()) {
	    frm.find('[name = "lastname"]').parents('.form-group').find(".error").remove();
	    frm.find('[name = "lastname"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your last name'));
	    valid = false;
	}

	var birthdate = frm.find('[name = "birthdate"]').val();
	if (!birthdate || !birthdate.trim()) {
	    frm.find('[name = "birthdate"]').parents('.form-group').find(".error").remove();
	    frm.find('[name = "birthdate"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your birthdate'));
	    valid = false;
	}

	var mobileno = frm.find('[name = "mobileno"]').val();
	if (!mobileno || !mobileno.trim()) {
	    frm.find('[name = "mobileno"]').parents('.form-group').find(".error").remove();
	    frm.find('[name = "mobileno"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your mobile number'));
	    valid = false;
	}

	var email = frm.find('[name = "email"]').val();
	if (!email || !email.trim()) {
	    frm.find('[name = "email"]').parents('.form-group').find(".error").remove();
	    frm.find('[name = "email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your email address'));
	    valid = false;
	} else {
	    var pattern = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
	    if (pattern.test(email)) {
	    } else {
		frm.find('[name = "email"]').parents('.form-group').find(".error").remove();
		frm.find('[name = "email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter valid email address'));
		valid = false;
	    }
	}

	var password = frm.find('[name="password"]').val();
	if (!password || !password.trim()) {
	    frm.find('[name = "password"]').parents('.form-group').find(".error").remove();
	    frm.find('[name="password"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter password'));
	    valid = false;
	} else {
	    var pattern = new RegExp(/^[a-zA-Z0-9_-]{5,15}$/);
	    if (pattern.test(password)) {
	    } else {
		frm.find('[name = "password"]').parents('.form-group').find(".error").remove();
		frm.find('[name="password"]').addClass('input_error').parents('.form-group').append(error_msg('Password must be 5-15 charaters'));
		valid = false;
	    }
	}

	var cnf_password = frm.find('[name="cnf_password"]').val();
	if (!cnf_password || !cnf_password.trim()) {
	    frm.find('[name = "cnf_password"]').parents('.form-group').find(".error").remove();
	    frm.find('[name="cnf_password"]').addClass('input_error').parents('.form-group').append(error_msg('Please re-type password'));
	    valid = false;
	} else if (password != cnf_password) {
	    frm.find('[name = "cnf_password"]').parents('.form-group').find(".error").remove();
	    frm.find('[name="cnf_password"]').addClass('input_error').parents('.form-group').append(error_msg('Password does not match'));
	    valid = false;
	}

	if (valid) {
	    $(this).attr("disabled", "disabled");
	    $(this).html('Processing...');
	    var data = new FormData(frm[0]);
	    $.ajax({
		url: "<?php echo base_url() . 'login/user_register'; ?>",
		type: "post",
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		success: function (resp)
		{
		    //console.log(resp);
		    if (resp === '1') {
			frm.trigger('reset');
			$("#btn_register").removeAttr('disabled');
			$("#btn_register").html('Register');
			$(".success_msg1").html("Register Successfully!");
			location = "<?php echo base_url() . 'home'; ?>";
		    } else if (resp === '2') {
			$("#btn_register").removeAttr('disabled');
			$("#btn_register").html('Register');
			$(".error_msg1").html("Email already exists!");
			valid = false;
		    } else if (resp === '3') {
			$("#btn_register").removeAttr('disabled');
			$("#btn_register").html('Register');
			$(".error_msg1").html("Your age is still not verified by the administrator!");
			valid = false;
		    } else if (resp === '4') {
			$("#btn_register").removeAttr('disabled');
			$("#btn_register").html('Register');
			$(".error_msg1").html("Already registered!");
			valid = false;
		    } else if (resp === '5') {
			$("#btn_register").removeAttr('disabled');
			$("#btn_register").html('Register');
			$(".error_msg1").html("Error while registration!");
			valid = false;
		    } else if (resp === '6') {
			$("#btn_register").removeAttr('disabled');
			$("#btn_register").html('Register');
			$(".error_msg1").html("You cannot register as you are under 18 years!");
			valid = false;
		    }
		}
	    });
	}
    });
});

//Facebook Login
function statusChangeCallback(response) {  // Called with the results from FB.getLoginStatus().
    if (response.status === 'connected') {   // Logged into your webpage and Facebook.
	FB.api('/me', { fields: 'email, name, id, first_name, last_name, middle_name, picture, short_name' }, function(response) {	    
//	    console.log(response.id); 
//	    console.log(response.first_name); 
//	    console.log(response.last_name); 
//	    console.log(response.email); 
	    
	    var encode_id = window.btoa(response.id);
	    var encode_firstname = window.btoa(response.first_name);    
	    var encode_lastname = window.btoa(response.last_name);  
	    var encode_email = window.btoa(response.email); 
	    
	    var dataString = "encode_id="+encode_id+"&encode_firstname="+encode_firstname+"&encode_lastname="+encode_lastname+"&encode_email="+encode_email+"&type=1";
	    
	    FB.logout(function(response) {
		console.log('Facebook signed out.');
	    });
			
	    $.ajax({
		url: "<?php echo base_url() . 'login/user_register'; ?>",
		type: "post",
		data: dataString,
		success: function (resp)
		{
		    //console.log(resp);
		    if (resp === '1') {			
			$(".success_msg1").html("Login Successfull!");
			location = "<?php echo base_url() . 'home'; ?>";
		    } 
		    else if (resp === '2') {
			$(".error_msg1").html("Your age is still not verified by the administrator!");
			valid = false;
		    } 
		}
	    });
	});
    }
}

function checkLoginState() {               // Called when a person is finished with the Login Button.
    FB.getLoginStatus(function(response) {   // See the onlogin handler
      statusChangeCallback(response);
    });
}

window.fbAsyncInit = function() {
    FB.init({
      appId      : '2343077949256106',
      cookie     : true,                     // Enable cookies to allow the server to access the session.
      xfbml      : true,                     // Parse social plugins on this webpage.
      version    : 'v5.0'           // Use this Graph API version for this call.
    });

    FB.getLoginStatus(function(response) {   // Called after the JS SDK has been initialized.
      statusChangeCallback(response);        // Returns the login status.
    });
};
  
(function(d, s, id) {                      // Load the SDK asynchronously
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function statusLogoutCallback(response) {  // Called with the results from FB.getLoginStatus().
    if (response.status === 'connected') {   // Logged into your webpage and Facebook.
	FB.logout(function(response) {
	    FB.Auth.setAuthResponse(null, 'unknown');
	    console.log('Facebook signed out.');
	});
    }
}

//Google Login
window.onbeforeunload = function(e){
    //Google signout
    var auth2 = gapi.auth2.getAuthInstance();
    auth2.signOut().then(function () {
      console.log('User signed out.');
    });
    
    //Facebook signout    
    FB.getLoginStatus(function(response) {   // Called after the JS SDK has been initialized.
	statusLogoutCallback(response);        // Returns the login status.
    });
};

//signin with google
function onSignIn(googleUser) {
    var profile = googleUser.getBasicProfile();
    //console.log(profile); return false;
    
    var token_id = googleUser.getAuthResponse().id_token;
    var encode_id = window.btoa(profile.getId());
    var encode_firstname = window.btoa(profile.getGivenName());    
    var encode_lastname = window.btoa(profile.getFamilyName());    
    var encode_email = window.btoa(profile.getEmail());    
    var dataString = "token_id="+token_id+"&encode_id="+encode_id+"&encode_firstname="+encode_firstname+"&encode_lastname="+encode_lastname+"&encode_email="+encode_email+"&type=2";
    //console.log(dataString); return false;
    
    $.ajax({
	url: "<?php echo base_url() . 'login/user_register'; ?>",
	type: "post",
	data: dataString,
	success: function (resp)
	{
	    //console.log(resp);
	    if (resp === '1') {
		$(".success_msg1").html("Login Successfull!");
		location = "<?php echo base_url() . 'home'; ?>";
	    } 
	    else if (resp === '2') {
		$(".error_msg1").html("Your age is still not verified by the administrator!");
		valid = false;
	    } 
	}
    });
}
</script>