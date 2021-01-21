<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Janet-Collection</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="ScreenOrientation" content="autoRotate:disabled">
        <meta name="description" content="">
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url() . 'assets/website/img/logo.png'; ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link href="https://fonts.googleapis.com/css?family=Abril+Fatface&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Rye&display=swap" rel="stylesheet">
        <link href="<?php echo base_url() . 'assets/website/css/bootstrap.min.css'; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url() . 'assets/website/css/owl.carousel.css'; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url() . 'assets/website/css/owl.theme.default.css'; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo base_url() . 'assets/website/css/pretty-checkbox.css'; ?>" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.0/css/ion.rangeSlider.min.css"/>
        <link href="<?php echo base_url() . 'assets/website/css/style.css'; ?>" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.materialdesignicons.com/2.0.46/css/materialdesignicons.min.css" async rel="stylesheet">
<!--        <link href="<?php //echo base_url(). 'assets/website/css/jquery.fileupload.css';       ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php //echo base_url(). 'assets/website/css/jquery.fileupload-ui.css';       ?>" rel="stylesheet" type="text/css"/>-->
        <link href="<?php echo base_url() . 'assets/font-awesome/css/font-awesome.css'; ?>" rel="stylesheet" type="text/css"/>        
        <link href="<?php echo base_url() . 'assets/css/plugins/datapicker/datepicker3.css'; ?>" rel="stylesheet">
        <script src="<?php echo base_url() . 'assets/website/js/jquery.min.js'; ?>" type="text/javascript"></script>
	<style type="text/css">.m_header{display: none !important;}</style>
	<script type="text/javascript">
	$(window).on("scroll", function () {
	    if ($(window).scrollTop() > 50) {
		$(".main_header").addClass("m_header");
	    } else {
		$(".main_header").removeClass("m_header");
	    }
	});
	</script>
    </head>
    <body class="cust_container <?php if(basename($_SERVER["REQUEST_URI"]) == "home" || basename($_SERVER["REQUEST_URI"]) == "") { ?> homecss<?php } ?>">
        <!--Header-->
	<nav class="navbar navbar-expand-md <?php if(basename($_SERVER["REQUEST_URI"]) == "home" || basename($_SERVER["REQUEST_URI"]) == "") { ?> main_header<?php } else { ?> fixed-top main_header <?php } ?>">
            <?php if ($this->session->userdata('loged_in')) { ?>      
                <label class="nav-item align-self-center mb-0">
                    <a class="nav-link menu_open_close p-0 mr-3 d-flex" href="javascript:void(0)">
                        <svg style="transform: rotate(180deg);" id="menu_icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 30 30" style="enable-background:new 0 0 30 30;" xml:space="preserve">
                        <g>
                        <path d="M28.807,25.393H2.195c-0.552,0-1-0.447-1-1.002c0-0.551,0.448-0.998,1-0.998h26.612   c0.551,0,0.998,0.447,0.998,0.998C29.805,24.945,29.357,25.393,28.807,25.393z"/>
                        <path d="M29.002,16H11c-0.552,0-1-0.449-1-1.001C10,14.448,10.448,14,11,14h18.002   C29.553,14,30,14.448,30,14.999C30,15.551,29.553,16,29.002,16z"/>
                        <path d="M28.807,6.607H4.611c-0.552,0-1-0.449-1-1c0-0.552,0.447-1,1-1h24.195c0.551,0,0.998,0.448,0.998,1   C29.805,6.158,29.357,6.607,28.807,6.607z"/>
                        </g>
                        </svg>
                    </a>
                </label>
            <?php } ?>
            <a class="navbar-brand" href="<?php echo base_url(); ?>home"><img class="img-fluid" src="<?php echo base_url(); ?>assets/website/img/logo.png" alt="logo"></a>

            <div class="ml-auto" id="top_menu">
                <ul class="navbar-nav">
                    <li class="nav-item align-self-center d-none d-sm-none d-md-inline d-lg-inline d-xl-inline">
                        <div class="nav-link search">
                            <form action="<?php echo base_url() . 'pps'; ?>" method="GET" name="search_form" id="search_form">
                                <div class="input-group">
				    <input type="text" autocomplete="off" class="form-control" placeholder="Search for Products" name="chr" id="chr" value="<?php if(!empty($_GET["chr"])) { echo $_GET["chr"]; } else { echo ''; } ?>">				    
                                    <div class="input-group-append">
                                        <button id="search_btn" class="btn search_btn" type="submit"><img src="<?php echo base_url() . 'assets/website/img/icons/search.svg'; ?>" alt=""></button>
                                    </div>
                                </div>
				<div style="display:none;" class="product_suggestion" id="product_suggestion"></div>
                            </form>
                        </div>
                    </li>
		    <?php $dir_name = str_replace('/', '', dirname($_SERVER["REQUEST_URI"])); 
		    if($dir_name == 'loyalty_points' || $dir_name == 'products') { ?>
		    <?php //if(dirname($_SERVER["REDIRECT_QUERY_STRING"]) == 'loyalty_points' || dirname($_SERVER["REDIRECT_QUERY_STRING"]) == 'products') { ?>
		    <li class="nav-item align-self-center d-none d-sm-none d-md-inline d-lg-inline d-xl-inline">
                        <a class="btn btn-filter" data-toggle="modal" data-target="#modal_main_filter" href="javascript:void(0);">
                            <img class="ml-1" src="<?php echo base_url() . 'assets/website/img/filter.svg'; ?>" alt="">
                        </a>
                    </li>
		    <?php } ?>

                    <li class="nav-item align-self-center d-none d-sm-none d-md-inline d-lg-inline d-xl-inline">
                        <a class="nav-link become_a_driver" href="<?php echo base_url() . 'become_a_driver'; ?>">
                            Become a Driver
                        </a>
                    </li>

                    <?php if (!$this->session->userdata('loged_in')) { ?>        
                        <li class="nav-item align-self-center">
                            <a class="nav-link signin_signup" href="javascript:void(0);" data-toggle="modal" data-target="#modal_login_resiter">Login</a>
                        </li>
                    <?php } ?>
			
                    <li class="nav-item align-self-center d-none d-sm-none d-md-none d-lg-inline d-xl-inline">
                        <a class="nav-link d-flex" href="<?php echo base_url() . 'cart'; ?>">
                            <svg id="shopping_bag_icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 489 489" style="enable-background:new 0 0 489 489;" width="24" height="24" xml:space="preserve">
                            <path d="M440.1,422.7l-28-315.3c-0.6-7-6.5-12.3-13.4-12.3h-57.6C340.3,42.5,297.3,0,244.5,0s-95.8,42.5-96.6,95.1H90.3   c-7,0-12.8,5.3-13.4,12.3l-28,315.3c0,0.4-0.1,0.8-0.1,1.2c0,35.9,32.9,65.1,73.4,65.1h244.6c40.5,0,73.4-29.2,73.4-65.1   C440.2,423.5,440.2,423.1,440.1,422.7z M244.5,27c37.9,0,68.8,30.4,69.6,68.1H174.9C175.7,57.4,206.6,27,244.5,27z M366.8,462   H122.2c-25.4,0-46-16.8-46.4-37.5l26.8-302.3h45.2v41c0,7.5,6,13.5,13.5,13.5s13.5-6,13.5-13.5v-41h139.3v41   c0,7.5,6,13.5,13.5,13.5s13.5-6,13.5-13.5v-41h45.2l26.9,302.3C412.8,445.2,392.1,462,366.8,462z"/>
                            </svg>
                        </a>
                    </li>

                    <?php
                    if ($this->session->userdata('loged_in')) {
                        $user_id = $this->session->userdata("user_id");
                    ?>                        

		    <li class="nav-item align-self-center d-none d-sm-none d-md-none d-lg-inline d-xl-inline">
			<a class="nav-link " href="javascript:void(0)">
			    <div class="media">
				<img class="mr-3 align-self-center" src="<?php echo $this->session->userdata("profile_image"); ?>" alt="">
				<div class="media-body align-self-center">
				    <h5 class="mb-0 c-white"><?php echo $this->session->userdata("user_name"); ?></h5>
				</div>
			    </div>
			</a>
		    </li>

                    <?php }  ?>
                </ul>
            </div>
        </nav>

        <?php if ($this->session->userdata('loged_in')) { ?>
            <div class="side_menu side_menu_close">
                <div class="row mb-2">
                    <div class="col-8">
                        <h1 class="main_title mb-0 mt-0 text-left">Menu</h1>
                    </div>
                    <div class="col-4 align-self-center text-right">
                        <label class="close_menu"><img src="<?php echo base_url() . 'assets/website/img/close-circular.svg'; ?>" alt="close menu"></label>
                    </div>
                </div>
                <ul class="pt-2">
                    <?php
                    //get unread notification count                        
                    $unread_notifications = $this->db->select("*")
                            ->where('notification.to_user_id', $user_id)
                            ->where("is_read", 1)
                            ->get('notification')
                            ->num_rows();
                    ?>     
                    <li class="slide_menu_usr_detail d-block d-sm-block d-md-none d-lg-none d-xl-none">
                        <div class="media">
                            <img class="mr-3 align-self-center" src="<?php echo $this->session->userdata('profile_image'); ?>" alt="">
                            <div class="media-body align-self-center">
                                <h5 class="mb-0"><?php echo $this->session->userdata('user_name'); ?></h5>
                            </div>
                        </div>
                    </li>
                    <li><a href="javascript:void(0);" data-toggle="modal" data-target="#mdl_profile">Profile</a></li>
                    <li><a href="javascript:void(0);" data-toggle="modal" data-target="#mdl_change_password">Change Password</a></li>
                    <li><a href="<?php echo base_url(); ?>cart">Shopping Bag</a></li>
                    <li><a href="<?php echo base_url(); ?>my_orders">My Orders</a></li>
                    <li><a href="<?php echo base_url(); ?>gift_card">Gift Card</a></li>
<!--                    <li><a href="<?php //echo base_url(); ?>loyalty_points">Loyalty Programs</a></li>-->
                    <li><a href="<?php echo base_url(); ?>products/favourite_products">Favourite</a></li>
                    <li><a href="<?php echo base_url(); ?>wallet">Wallet</a></li>		   
                    <li style="position: relative"><a href="<?php echo base_url(); ?>notifications">Notification</a> <label class="badge badge-pink totl_notif"><?php echo $unread_notifications; ?></label></li>
                    <li class="d-block d-sm-inline d-md-none d-lg-none d-xl-none"><a href="become_a_driver">Become a Driver</a></li>
                    <li><a href="<?php echo base_url(); ?>login/logout">Logout</a></li>                
                </ul>
            </div>
        <?php } ?>
        <div class="bg-menu-overlay"></div>
        <!--Header-->
