<!DOCTYPE html>
<html>
    <head>
        <base href="<?= site_url() ?>" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Janet-Collection - Seller Login</title>
        <script src="assets/js/jquery-2.1.1.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo.png"/>
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
            .middle-box2 {
                max-width: 500px;
                z-index: 100;
                margin: 0 auto;
                padding-top: 40px;
                border:1px dashed #ccc;
                padding-left: 20px;
                padding-right: 20px;
            }
            .middle-box3 {
                max-width: 600px;
                z-index: 100;
                margin: 0 auto;
                padding-top: 40px;
            }
        </style>
        <script>
            $(document).ready(function ()
            {                
//                $('.email_input').blur(function() {
//                    var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
//                    if (testEmail.test(this.value));
//                    else alert('Please enter valid email');
//                });
                
                $('.submit_btn').click(function () {

                    //var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                    var contact_no = document.userlogin.contact_no.value;
                    var password = document.userlogin.password.value;

                    if (contact_no == "")
                    {
                        alert("Please enter your mobile no!");
                        return false;
                    }
//                    else if (!testEmail.test(email)){
//                        alert("Please enter valid email!");
//                        return false;
//                    }
                    
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
            });
        </script>
    </head>

    <body class="gray-bg" style="background-color: #f3f3f4"> 
        <div class="middle-box2 text-center loginscreen animated fadeInDown">                                   
            <img alt="image" class="" src="../assets/logo.png"  height="100px"width="100px" />
            <h2><b style="">Janet-Collection - Seller Login</b></h2>                
            <form class="m-t userlogin" name="userlogin" role="form" >
                <div class="form-group">
                    <input type="text" name="contact_no" class="form-control numeric" placeholder="Mobile No" maxlength="15" required="">
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required="">
                </div>
                <div class="form-group">
                    <a type="submit" class="submit_btn btn btn-primary block full-width m-b">Login</a>
                </div>
                
            </form>            
        </div>
        
        <div class="middle-box3 text-center loginscreen animated fadeInDown">  
            <a href="register" class="btn btn-success m-b"><strong>Register</strong></a>&nbsp;&nbsp;
            <a href="forgot_password" class="btn btn-info m-b" id="forgot_password" name="forgot_password"><strong>Forgot Password</strong></a>
        
            <br>
            <p class="m-t" style=""> <small>Janet-Collection Seller  &copy; <?= date('Y') ?> </small> </p>
        
        </div>
        
        <script>
            $(document).ready(function () {
                allow_numeric();
            });

            function allow_numeric(){
                $(".numeric").keypress(function (e) {
                    //if the letter is not digit then display error and don't type anything
                    if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
                       //display error message
                       return false;
                   }
                });
            }
        </script>
    </body>
</html>
