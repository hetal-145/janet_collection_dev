<!DOCTYPE html>
<html>
    <head>
        <base href="<?= site_url() ?>" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Iviou! Admin Login</title>
        <script src="assets/js/jquery-2.1.1.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="assets/css/animate.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">

        <style>
            .gray-bg {
                background-color: #01aeec;
            }
            .btn-primary {
                background-color: #f44336;
                border-color: #1ab394;
                color: #FFFFFF;
            }

        </style>
        <script>
            $(document).ready(function ()
            {
                $('.submit_btn').click(function () {
		    
		    var email = document.userlogin.email.value;
                    var password = document.userlogin.password.value;

                    if (email == "")
                    {
                        $("#modal_submit_reviews").find(".alert_msg").html("");
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Please enter your email.");
                        return false;
                    }
                    if (password == "")
                    {
                        $("#modal_submit_reviews").find(".alert_msg").html("");
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Please provide your password.");
                        return false;
                    }
                    var data = $('form[name="userlogin"]').serialize();
                    $.ajax({
                        url: 'login/user_login',
                        type: 'post',
                        data: data,
                        success: function (data)
                        {
                            if (data == 1) {
                                location = 'home';
                            } else {
				$("#modal_submit_reviews").find(".alert_msg").html("");
				$("a.modal_submit_reviewss").trigger("click");
				$("#modal_submit_reviews").find(".alert_msg").html("Invalid Login ID or password.");                                
                            }
                        }
                    });
                });
                $(document).on('keypress', 'form[name="userlogin"]', function (e) {
                    if (e.keyCode.toString() == '13') {
                        $('.submit_btn').trigger('click');
                    }
                });
            });
        </script>
    </head>

    <body class="gray-bg" style="background-color: #fffefe"> 
        <div class="middle-box text-center loginscreen animated fadeInDown">  
	    <div class="logo">
		<p><img src="../../assets/img/logo.png" height="140px" width="140px"> </p>
		<h2><b style="">Janet-Collection Admin Login</b></h2>
	    </div>
	    <form class="m-t userlogin" name="userlogin" role="form" >
		<div class="form-group">
		    <input type="email" name="email" class="form-control" placeholder="Email" required="">
		</div>
		<div class="form-group">
		    <input type="password" name="password" class="form-control" placeholder="Password" required="">
		</div>
		<a type="submit" class="submit_btn btn btn-primary block full-width m-b">Login</a>

		<!--<a href="#"><small>Forgot password?</small></a>-->
    <!--                    <p class="text-muted text-center"><small>Do not have an account?</small></p>
		<a class="btn btn-sm btn-white btn-block" href="register">Create an account</a>-->
	    </form>
	    <p class="m-t" style=""> <small>Janet-Collection Admin  &copy; <?= date('Y') ?> </small> </p>
	</div>
	
<a class="modal_submit_reviewss" data-target="#modal_submit_reviews" data-toggle="modal" style="display:none"></a>
<div class="modal fade" id="modal_submit_reviews" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <h4 class="mb-0 alert_msg"></h4><br>
                <div class="text-center">
                    <button type="button" class="btn btn-success" id="confirmed" data-dismiss="modal">OK!</button>
                </div>
            </div>
        </div>
    </div>
</div>
    </body>
</html>
