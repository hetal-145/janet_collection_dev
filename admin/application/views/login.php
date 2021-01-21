<!DOCTYPE html>
<html>
    <head>
        <base href="<?= site_url() ?>" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Janet-Collection - Admin Login</title>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo.png"/>
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
                $('.email_input').blur(function() {
                    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                    if (testEmail.test(this.value));
                    else alert('Please enter valid email');
                });

                $('.submit_btn').click(function () {

                    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                    var email = document.userlogin.email.value;
                    var password = document.userlogin.password.value;

                    if (email == "")
                    {
                        alert("Please enter your email!");
                        return false;
                    }
                    else if (!testEmail.test(email)){
                        alert("Please enter valid email!");
                        return false;
                    }
                    
                    if (password == "")
                    {
                        alert("Please provide your password!");
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
                                alert('invalid login');
                            }
                        }
                    });
                });
                $(document).on('keypress', 'form[name="userlogin"]', function (e) {
                    if (e.keyCode.toString() == '13') {
                        $('.submit_btn').trigger('click');
                    }
                });
                
                function isEmail(email) {
                    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    return regex.test(email);
                }
            });
        </script>
    </head>

    <body class="gray-bg" style="background-color: #fffefe"> 
        <div class="middle-box text-center loginscreen animated fadeInDown">
            <img alt="image" class="" src="../assets/logo.png"  height="100px"width="100px" />
            <h2><b style="">Janet-Collection - Admin Login</b></h2>  
	    <form class="m-t userlogin" name="userlogin" role="form" >
		<div class="form-group">
		    <input type="email" name="email" class="form-control email_input" placeholder="Email" required="">
		</div>
		<div class="form-group">
		    <input type="password" name="password" class="form-control" placeholder="Password" required="">
		</div>
		<a type="submit" class="submit_btn btn btn-primary block full-width m-b">Login</a>


	    </form>
	    <p class="m-t" style=""> <small>Janet-Collection Admin Panel  &copy; <?= date('Y') ?> </small> </p>
        </div>
    </body>
</html>
