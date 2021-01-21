<!DOCTYPE html>
<html>
    <head>
        <base href="<?= site_url() ?>" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Seller Forgot Password</title>
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
                $("#get_otp").hide();
                
                $('.submit_btn').click(function () {

                    var contact_no = document.forgot_password.contact_no.value;
                    var country_code = document.forgot_password.country_code.value;
                    
                    if (contact_no == "")
                    {
                        alert("Please enter your mobile no!");
                        return false;
                    }
                    
                    if (country_code == "")
                    {
                        alert("Please select your country code!");
                        return false;
                    }
                    
                    var data = $('form[name="forgot_password"]').serialize();
                    $.ajax({
                        url: 'forgot_password/get_password',
                        type: 'post',
                        data: data,
                        success: function (response)
                        {
			    //console.log(response);
                            var data = $.parseJSON(response);
                            if(data.status == 'true'){
                                $("#delivery_receipt_id").val(data.delivery_receipt_id);
                                $("#get_mobile").hide();
                                $("#get_otp").show();
                                //alert(data.otp);
                                
                            }
                            else if(data.status == 'false') {
                                alert('Mobile No not found');
                            }
                        }
                    });
                });
                
                $('.submit_btn2').click(function () {

                    var verify_otp = document.verify_otp.otp.value;
                    
                    if (verify_otp == "")
                    {
                        alert("Please enter OTP!");
                        return false;
                    }
                    
                    var data = $('form[name="verify_otp"]').serialize();
                    $.ajax({
                        url: 'forgot_password/verify_otp',
                        type: 'post',
                        data: data,
                        success: function (data)
                        {
                            //console.log(data);
                            if(data == 1){
                                alert('OTP Verified');
                                window.location = "login";
                                
                            }
                            else {
                                alert('Invalid OTP');
                            }
                        }
                    });
                });
                
                $(document).on('keypress', 'form[name="forgot_password"]', function (e) {
                    if (e.keyCode.toString() == '13') {
                        $('.submit_btn').trigger('click');
                    }
                });
            });
        </script>
    </head>

    <body class="gray-bg" style="background-color: #f3f3f4"> 
        <div class="middle-box2 text-center loginscreen animated fadeInDown">                                   
            <img alt="image" class="img-circle" src="../assets/logo.png"  height="100px"width="100px" />                           
            <form class="m-t forgot_password" name="forgot_password" role="form" id='get_mobile'>  
                <h2><b style="">Janet-Collection - Seller Forgot Password</b></h2> 
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <select name="country_code" id="country_code" class="form-control">
                                <option value="">--Select--</option>
                                <?php foreach ($code as $c){ ?>
                                <option value="<?php echo $c["code"]; ?>"><?php echo $c["code"]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-group">
                            <input type="text" name="contact_no" class="form-control numeric" placeholder="Mobile No" maxlength="15" required="">
                        </div>
                    </div>
                </div> 
                <div class="form-group">
                    <a type="submit" class="submit_btn btn btn-primary block full-width m-b">Get Password</a>
                </div>
                
            </form>  
            
            <form class="m-t verify_otp" name="verify_otp" role="form" id="get_otp">  
                <h2><b style="">Janet-Collection - Verify OTP</b></h2> 
                <div class="form-group">
                    <input type="text" name="otp" class="form-control numeric" placeholder="Verify OTP" maxlength="4" required="">
                    <input type="hidden" name="delivery_receipt_id" id="delivery_receipt_id" class="form-control numeric" value="">
                </div>
                <div class="form-group">
                    <a type="submit" class="submit_btn2 btn btn-primary block full-width m-b">Verify OTP</a>
                </div>
                
            </form>  
        </div>
        
        <div class="middle-box3 text-center loginscreen animated fadeInDown">  
            <a href="register" class="btn btn-success m-b"><strong>Register</strong></a>&nbsp;&nbsp;
            <a href="login" class="btn btn-info m-b"><strong>Login</strong></a>
        
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
