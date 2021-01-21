<!DOCTYPE html>
<html>
    <head>
        <base href="<?= site_url(); ?>" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php		
	if(basename($_SERVER["REQUEST_URI"]) == "home") { ?>
	    <meta http-equiv="refresh" content="300">
	<?php } ?>
	
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo.png"/>
        <title>Janet-Collection Seller Panel</title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="assets/css/animate.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">
        <script src="assets/js/jquery-2.1.1.js"></script>
        <script src="assets/js/jquery.populate.js"></script>
        <link href="assets/css/plugins/summernote/summernote.css" rel="stylesheet">
        <script src="assets/js/plugins/summernote/summernote.min.js"></script>
        <link rel="stylesheet" src="assets/css/plugins/datapicker/datepicker3.css">
        <link href="assets/css/plugins/switchery/switchery.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css">
       <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.2/multiple-select.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
        <style>
            .custombtn {
                margin-right: 20px;
            }
            .xcrud-nav ul.pagination{
                float:right;
            }
            .newadd{
                margin-right: 10px;
            }
            #xcrud-modal-window img{
                max-width: 100% !important;
            }
            .newlabel{
                font-family: sans-serif;
                font-size: 17px;
            }

            .error{
                color:#ed5565;
                text-align: right;
            }

            .input_error{
                border: thin solid #ed5565 !important;
            }

            .form-horizontal .form-group{
                margin-right: -10px;
                margin-left: -10px;
            }
            .datepicker, #ui-datepicker-div {
                z-index: 9999 !important;
            }
            .ibox .ibox-content .feed-element:last-child{
                border-bottom : none;
            }
            
            .btn-success {
                color: #fff;
                background-color: #47a447;
                border-color: #47a447;
            }
            
            .btn-info {
                color: #fff;
                background-color: #5bc0de;
                border-color: #46b8da;
            }
            
            .xcrud-search-toggle {
                display: none !important;
            }
            
            .xcrud-search.form-inline {
                display: inline-block !important;
                margin-bottom: 10px;
            }
            
            .xcrud-search.form-inline > .select2-container {
                display: inline-block !important;
            }
            
            .xcrud-search.form-inline > .btn-group {
                margin-left: 5px;
                margin-bottom: -4px;
            }
            
            select.xcrud-searchdata.xcrud-search-active{
                display: none !important;
            }
        </style>

    </head>
    <?php
    $uri = uri_string();
    $uri = explode('/', $uri);

    if (!isset($uri[0])) {
        $uri[0] = '';
    }
    if (!isset($uri[1])) {
        $uri[1] = '';
    }
    
    $seller_id = $this->session->userdata('user_id');

//print_r($uri); exit;

    if ($uri[0] == 'home') {
        echo '<script>$(document).ready(function(){$("a[href=\'home\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'category') {
        echo '<script>$(document).ready(function(){$("a[href=\'category\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'sub_category') {
        echo '<script>$(document).ready(function(){$("a[href=\'sub_category\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'suppliers') {
        echo '<script>$(document).ready(function(){$("a[href=\'suppliers\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'product') {
        echo '<script>$(document).ready(function(){$("a[href=\'product\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'volume') {
        echo '<script>$(document).ready(function(){$("a[href=\'volume\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'brand') {
        echo '<script>$(document).ready(function(){$("a[href=\'brand\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'about_us'  && $uri[1] == 'view_about_us') {
        echo '<script>$(document).ready(function(){$("a[href=\'about_us/view_about_us\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'privacy_policy'  && $uri[1] == 'view_privacy_policy') {
        echo '<script>$(document).ready(function(){$("a[href=\'privacy_policy/view_privacy_policy\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'terms_and_conditions' && $uri[1] == 'view_term_condition') {
        echo '<script>$(document).ready(function(){$("a[href=\'terms_and_conditions/view_term_condition\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'setting' && $uri[1] == 'email_setting') {
        echo '<script>$(document).ready(function(){$("a[href=\'setting/email_setting\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'profile') {
        echo '<script>$(document).ready(function(){$("a[href=\'profile\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'volume') {
        echo '<script>$(document).ready(function(){$("a[href=\'volume\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'product' && $uri[1] == 'brand') {
        echo '<script>$(document).ready(function(){$("a[href=\'product/brand\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'product' && $uri[1] == 'category') {
        echo '<script>$(document).ready(function(){$("a[href=\'product/category\']").parents(\'li\').addClass("active");});</script>';
    } 
   
    else if ($uri[0] == 'orders' && $uri[1] == 'new_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/new_orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'orders' && $uri[1] == 'delivered_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/delivered_orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'orders' && $uri[1] == 'cancelled_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/cancelled_orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'orders' && $uri[1] == 'order_details') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders\']").parents(\'li\').addClass("active");});</script>';
    }    
    else if ($uri[0] == 'orders' && $uri[1] == 'user_cancelled_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/user_cancelled_orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'orders' && $uri[1] == 'orders_not_completed') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/orders_not_completed\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'orders' && $uri[1] == 'orders_in_process') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/orders_in_process\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'transaction_history') {
        echo '<script>$(document).ready(function(){$("a[href=\'transaction_history\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'order_rc' && $uri[1] == 'return_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'order_rc/return_orders\']").parents(\'li\').addClass("active");});</script>';
    }       
    else if ($uri[0] == 'faq_list') {
        echo '<script>$(document).ready(function(){$("a[href=\'faq_list\']").parents(\'li\').addClass("active");});</script>';
    }
    
    else {
        echo '<script>$(document).ready(function(){$("a[href=\'' . $uri[0] . '\']").parents(\'li\').addClass("active");});</script>';
    }
    ?>

    <body class="pace-done skin-5">

        <div id="wrapper">

            <nav class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav metismenu" id="side-menu">
                        
                        <li class="nav-header">
                            <div class="dropdown profile-element"> <span>
                                    <center>
                                        <img alt="image" class="img-circle" src="../assets/logo.png"  height="100px"width="100px" />
                                    </center>
                                </span>
                                <center>
                                    <a  href="javascript:void(0);">
                                        <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">Seller</strong>
                                            </span>
                                        </span> 
                                    </a>
                                </center>
                            </div>
                            <div class="logo-element">
                                Janet-Collection
                            </div>
                        </li>

                        <li>
                            <a href="home"><i class="fa fa-dashboard "></i> <span class="nav-label">Dashboard</span></a>
                        </li>
                        
                        <li>
                            <a href="#"><i class="fa fa-list-alt"></i> <span class="nav-label">Products</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
<!--				<li>
                                    <a href="product/category"><i class="fa fa-archive"></i> <span class="nav-label">Categories</span></a>
                                </li>
				<li>
                                    <a href="product/brand"><i class="fa fa-archive"></i> <span class="nav-label">Brands</span></a>
                                </li>-->
                                <li>
                                    <a href="product"><i class="fa fa-flask"></i> <span class="nav-label">Products</span></a>
                                </li>
                                <li>
                                    <a href="comments"><i class="fa fa-user"></i> <span class="nav-label">Ratings & Review</span></a>
                                </li> 
                            </ul>
                        </li>
                        
                        <li>
                            <a href="#"><i class="fa fa-list"></i> <span class="nav-label">Order Management</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="orders/new_orders"><i class="fa fa-th-list"></i> <span class="nav-label">New Orders</span></a>
                                </li>
				<li>
                                    <a href="orders/delivered_orders"><i class="fa fa-th-list"></i> <span class="nav-label">Completed Orders</span></a>
                                </li>
				<li>
                                    <a href="orders/cancelled_orders"><i class="fa fa-th-list"></i> <span class="nav-label">Canceled Orders</span></a>
                                </li>
				<li>
                                    <a href="orders/orders_not_completed"><i class="fa fa-th-list"></i> <span class="nav-label">Not Completed Orders</span></a>
                                </li>
				<li>
                                    <a href="orders/orders_in_process"><i class="fa fa-th-list"></i> <span class="nav-label">Orders in Process of Delivery</span></a>
                                </li>
                            </ul>
                        </li>
			
			<li>
                            <a href="#"><i class="fa fa-user-md"></i> <span class="nav-label">Personal Profile</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">                                
                                <li>
                                    <a href="trading_hours"><i class="fa fa-clock-o"></i> <span class="nav-label">Trading Hours</span></a>
                                </li> 
				<li>
                                    <a href="stripe_account"><i class="fa fa-bank"></i> <span class="nav-label">Stripe Accounts</span></a>
                                </li>
                                <li>
                                    <a href="profile"><i class="fa fa-user"></i> <span class="nav-label">Profile</span></a>
                                </li>                                 
                                <li>
                                    <a href="upload_documents"><i class="fa fa-file-o"></i> <span class="nav-label">Verification Documents</span></a>
                                </li> 
				<li>
                                    <a href="notifications"><i class="fa fa-list-alt"></i> <span class="nav-label">Notification List</span></a>
                                </li> 
				
                            </ul>
                        </li>
                        
                        <li>
                            <a href="#"><i class="fa fa-cog"></i> <span class="nav-label">Settings</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="setting"><i class="fa fa-cog"></i> <span class="nav-label">General Settings</span></a>
                                </li>  
				<li>
                                    <a href="setting/view_change_password"><i class="fa fa-key"></i> <span class="nav-label">Change Password</span></a>
                                </li> 
<!--				<li>
                                    <a href="about_us/view_about_us"><i class="fa fa-info-circle"></i> <span class="nav-label">About Us</span></a>
                                </li>-->
				<li>
                                    <a href="faq_list"><i class="fa fa-info-circle"></i> <span class="nav-label">FAQs for Sellers</span></a>
                                </li>
                                <li>
                                    <a href="terms_and_conditions/view_term_condition"><i class="fa fa-terminal"></i> <span class="nav-label">Terms & Conditions</span></a>
                                </li>
                                <li>
                                    <a href="privacy_policy/view_privacy_policy"><i class="fa fa-file-powerpoint-o"></i> <span class="nav-label">Privacy Policy</span></a>
                                </li>
                                <li>
                                    <a href="contact_us"><i class="fa fa-phone"></i> <span class="nav-label">Contact Us</span></a>
                                </li> 
                            </ul>
                        </li> 
                    </ul>
                </div>
            </nav>

            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    <nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">
                        <div class="navbar-header">
                            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " ><i class="fa fa-bars"></i> </a>
                            
                        </div>
                        <ul class="nav navbar-top-links navbar-right">
                            <?php /*
                            <li class="dropdown">
                                <span class="success_msg pull-right" style="color:green; padding: 7px; display:block;"></span>
                                <span class="error_msg pull-right" style="color:red; padding: 7px; display:block;"></span>
                                &nbsp;&nbsp;
                                <!-- current status -->
                                <?php $is_online = $this->db->select("is_online")->where('seller_id', $seller_id)->get("seller")->row_array(); ?>
                                Current Store Status &nbsp;&nbsp;<input type="checkbox" data-seller = "<?php echo $seller_id; ?>" value="<?php echo $is_online["is_online"]; ?>" name="is_online_change" id="is_online_change" class="is_online_change js-switch" <?php if($is_online["is_online"] == 1) { ?> checked <?php } ?> />
                            </li>
			     * 
			     */ ?>
                            <li class="dropdown">
                                <!-- notification status -->
                                <?php                                    
                                    $notifications = $this->db->select("*")->where('seller_id', $seller_id)->where('is_read', 0)->get('website_notification')->num_rows();
                                ?>                                
                                <a class="count-info"href="<?php echo base_url().'orders/new_orders'; ?>">
                                    <i class="fa fa-bell"></i>  <span class="label label-primary"><?php echo $notifications; ?></span>
                                </a>
                            </li>
                               
                            <li>
                                <a href="login/logout">
                                    <i class="fa fa-sign-out"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
                