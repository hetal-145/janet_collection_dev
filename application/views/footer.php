<footer class="">
    <div class="container">
        <div class="row mb-3">
            <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 mb-4 mb-sm-4 mb-md-0 mb-lg-0 mb-xl-0">
                <h5 class="c-white">Discover Janet-Collection</h5>
                <ul class="footer_menu">
<!--                    <li><a href="<?php //echo base_url() . 'home/about_us'; ?>">About Us</a></li>-->
                    <li><a href="<?php echo base_url(); ?>category/category_list">Categories</a></li>
                    <li><a href="<?php echo base_url(); ?>become_a_driver">Driver Signup</a></li>
		    <li><a href="<?php echo base_url(); ?>home/seller_faq_list">FAQs for Seller</a></li> 
                </ul>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 mb-4 mb-sm-4 mb-md-0 mb-lg-0 mb-xl-0">
                <h5 class="c-white">Legal</h5>
                <ul class="footer_menu">
                    <li><a href="<?php echo base_url(); ?>home/terms_and_conditions">Terms of Service</a></li>
                    <li><a href="<?php echo base_url(); ?>home/privacy_policy">Privacy Policy</a></li>
                    <li><a href="<?php echo base_url(); ?>home/cookies">Cookies</a></li>
                </ul>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-lg-3 col-xl-3 mb-4 mb-sm-4 mb-md-0 mb-lg-0 mb-xl-0">
                <h5 class="c-white">Help & Support</h5>
                <ul class="footer_menu">
                    <li><a href="<?php echo base_url(); ?>contact_us">Contact Us</a></li>
                    <li><a href="<?php echo base_url(); ?>home/faq_list">FAQs</a></li>
                    <li><a href="<?php echo base_url(); ?>home/licensed_retailers">Licensed Retailers</a></li>
                    <li><a href="<?php echo base_url(); ?>home/alcohol_awareness">Alcohol Awareness</a></li>
                </ul>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3 mt-md-3 mt-0 mt-sm-0 mt-lg-0 mt-xl-0">
		<?php if(basename($_SERVER["REQUEST_URI"]) != "driver_signup") { ?>
                <h5 class="c-white">Find Janet-Collection on</h5>
                <a href="javascript:void(0)"> <img width="150" class="mb-3" src="<?php echo base_url() . 'assets/website/img/app_store.png'; ?>" alt=""></a>
                <a href="javascript:void(0)"><img width="150" class="mb-3" src="<?php echo base_url() . 'assets/website/img/google_play.png'; ?>" alt=""></a>
		<?php } ?>
                <ul class="footer_social">
		    <li><a href="<?php if(!empty($facebook)) { echo $facebook; } else { ?> javascript:void(0); <?php } ?>"><img src="<?php echo base_url(); ?>assets/website/img/icons/fb.svg" alt=""></a></li>
		    
                    <li><a href="<?php if(!empty($instagram)) { echo $instagram; } else { ?> javascript:void(0); <?php } ?>"><img src="<?php echo base_url(); ?>assets/website/img/icons/insta.svg" alt=""></a></li>
		    
                    <li><a href="<?php if(!empty($twitter)) { echo $twitter; } else { ?> javascript:void(0); <?php } ?>"><img src="<?php echo base_url(); ?>assets/website/img/icons/tw.svg" alt=""></a></li>
		    
                    <li><a href="<?php if(!empty($linkedin)) { echo $linkedin; } else { ?> javascript:void(0); <?php } ?>"><img src="<?php echo base_url(); ?>assets/website/img/icons/in.svg" alt=""></a></li>
		    
                    <li><a href="<?php if(!empty($youtube)) { echo $youtube; } else { ?> javascript:void(0); <?php } ?>"><img src="<?php echo base_url(); ?>assets/website/img/icons/yt.svg" alt=""></a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr>
            </div>
        </div>
        <div class="row copyright">
            <div class="col-12 col-12 col-md-12 col-lg-12 col-xl-12 text-center">
                <small><strong>Copyright  &COPY; <?php echo date('Y'); ?></strong> By Janet-Collection</small>
            </div>
        </div>
    </div>
</footer>

<!--login register-->
<?php include_once 'register.php'; ?>

<!--forgot password-->
<?php include_once 'forgotpassword.php'; ?>

<!--modal profile-->
<?php include_once 'profile.php'; ?>

<!--modal change password-->
<?php include_once 'change_password.php'; ?>

<!-- filter -->
<?php include_once 'filter.php'; ?>

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

<a class="modal_age_notices" data-target="#modal_age_notice" data-toggle="modal" style="display:none"></a>
<div class="modal fade" id="modal_age_notice" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">	
        <div class="modal-content">
	    <div class="logo text-center mt-4">
		<p><img src="<?php echo base_url() . 'assets/logo.png'; ?>" height="100px" width="100px"> </p>
	    </div>
            <div class="modal-body text-center p-4">
                <h3 class="mb-0">Are You Over 18?</h3>
		<p>This website contains images of alcohol & tobacco products. Please confirm that you are over the age of 18 to continue. </p>
                <div class="text-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Exit now</button>
                    <button type="button" class="btn btn-danger" id="confirmed_age" data-dismiss="modal">Yes, I am over 18!</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() . 'assets/website/js/popper.min.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url() . 'assets/website/js/bootstrap.min.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url() . 'assets/website/js/owl.carousel.min.js'; ?>" type="text/javascript"></script>
<!-- date picker -->
<script src="<?php echo base_url() . 'assets/js/plugins/datapicker/bootstrap-datepicker.js'; ?>"></script>
<script src="<?php echo base_url() . 'assets/website/js/numscroller-1.0.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url() . 'assets/website/js/prism.js'; ?>" type="text/javascript"></script>
<script src="<?php echo base_url() . 'assets/website/js/custom.js'; ?>" type="text/javascript"></script>

<script>    
    function error_msg(msg) {
        return '<div class="error" style="color:red;">' + msg + '</div>';
    }

    function hide_msg() {
        setTimeout(function () {
            $('.success_msg').html('');
        }, 3000);
        setTimeout(function () {
            $('.error_msg').html('');
        }, 3000);
    }

    function allow_numeric() {
        $(".numeric").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 32 && e.which != 43 && e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
                //display error message
                return false;
            }
        });
    }

    $(document).ready(function () {
	//console.log($.cookie());
	if ($.cookie('age_verified') != '1') {
	    //show popup here
	    $("a.modal_age_notices").trigger("click");
	    $("#confirmed_age").click(function() {
		$.cookie('age_verified', '1', { path: "/", domain: "Janet-Collection.com", expires: 60}); 
	    });	    
	}

        //show hide modals
        $("#mdl_forgotpassword").on("click", function () {
            $("#modal_login_resiter").modal('hide');
        });

        var isMobile = {
            Android: function () {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function () {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function () {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function () {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function () {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function () {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };

        //allow to add numeric values only
        allow_numeric();

        //datepicker
        if ($(".date_picker").length) {
            if (isMobile.any()) {
                $('.date_picker').attr('type', 'date');
            } else {
                $('.date_picker').attr('type', 'text');
                $('.date_picker').datepicker({
                    startView: 3,
                    todayBtn: "linked",
                    keyboardNavigation: false,
                    forceParse: false,
                    autoclose: true,
                    format: "yyyy-mm-dd"
                });
            }
        }
	
	//birth datepicker
        if ($(".birt_date_picker").length) {
            if (isMobile.any()) {
                $('.birt_date_picker').attr('type', 'date');
            } else {
                $('.birt_date_picker').attr('type', 'text');
                $('.birt_date_picker').datepicker({
                    startView: 3,
                    todayBtn: "linked",
                    keyboardNavigation: false,
                    forceParse: false,
                    autoclose: true,
                    format: "yyyy-mm-dd",
		    endDate: '+0d',
                });
            }
        }

	$("#chr").keyup(function(){
	    $.ajax({
		url: "<?php echo base_url() . 'products/search_product_name'; ?>",
		type: "post",
		data:'chr='+$(this).val(),
		beforeSend: function(){
		    $("#search-box").css("background","#FFF url(assets/website/img/loading-sm.gif) no-repeat 165px");
		},
		success: function(data){
		    //console.log(data); return false;
		    $("#product_suggestion").show();
		    $("#product_suggestion").html(data);
		}
	    });
	});
    });
</script>
</body>
</html>
