<!DOCTYPE html>
<html>
    <head>
        <base href="<?= site_url() ?>" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php	
	if(basename($_SERVER["REQUEST_URI"]) == "home") { ?>
	    <meta http-equiv="refresh" content="300">
	<?php } ?>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo.png"/>
        <title>Janet-Collection Admin Panel</title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="assets/css/animate.css" rel="stylesheet">
        <link href="assets/css/style.css" rel="stylesheet">
        <script src="assets/js/jquery-2.1.1.js"></script>

        <script src="assets/js/jquery.populate.js"></script>
        <link href="assets/css/plugins/summernote/summernote.css" rel="stylesheet">
        <script src="assets/js/plugins/summernote/summernote.min.js"></script>
        <link rel="stylesheet" src="assets/css/plugins/datapicker/datepicker3.css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.2/multiple-select.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css">
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
	    
	    .btn-primary {
                color: #fff;
                background-color: #337ab7;
                border-color: #2e6da4;
            }
	    
	    .btn-secondary {
                color: #fff;
                background-color: #6c757d;
                border-color: #6c757d;
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
            
            .xcrud-searchdata{
                display: inline-block !important;
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
    } 
    else if ($uri[0] == 'product' && $uri[1] == 'archive_product') {
        echo '<script>$(document).ready(function(){$("a[href=\'product/archive_product\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'return_policy') {
        echo '<script>$(document).ready(function(){$("a[href=\'return_policy\']").parents(\'li\').addClass("active");});</script>';
    } else if ($uri[0] == 'volume') {
        echo '<script>$(document).ready(function(){$("a[href=\'volume\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'bonus') {
        echo '<script>$(document).ready(function(){$("a[href=\'bonus\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'brand') {
        echo '<script>$(document).ready(function(){$("a[href=\'brand\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'schedule_partition') {
        echo '<script>$(document).ready(function(){$("a[href=\'schedule_partition\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'drivers' && $uri[1] == 'view_driver_details') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers/view_history\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'drivers' && $uri[1] == 'view_history') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers/view_history\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'drivers' && $uri[1] == 'driver_details') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers/driver_stripe_details\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'drivers' && $uri[1] == 'add_delivery_zone') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers/driver_delivery_zone\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'drivers' && $uri[1] == 'driver_stripe_details') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers/driver_stripe_details\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'drivers' && $uri[1] == 'driver_delivery_zone') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers/driver_delivery_zone\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'drivers') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'admin_bonus') {
        echo '<script>$(document).ready(function(){$("a[href=\'admin_bonus\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'users' && $uri[1] == 'uorder_history') {
        echo '<script>$(document).ready(function(){$("a[href=\'users/uorder_history\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'users' && $uri[1] == 'user_order_history') {
        echo '<script>$(document).ready(function(){$("a[href=\'users/uorder_history\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'users' && $uri[1] == 'product_return') {
        echo '<script>$(document).ready(function(){$("a[href=\'users/product_return\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'users' && $uri[1] == 'product_return_history') {
        echo '<script>$(document).ready(function(){$("a[href=\'users/product_return\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'users') {
        echo '<script>$(document).ready(function(){$("a[href=\'users\']").parents(\'li\').addClass("active");});</script>';
    }    
    else if ($uri[0] == 'seller' && $uri[1] == 'view_history') {
        echo '<script>$(document).ready(function(){$("a[href=\'seller/view_history\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'seller' && $uri[1] == 'view_seller_details') {
        echo '<script>$(document).ready(function(){$("a[href=\'seller/view_history\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'seller' && $uri[1] == 'seller_trading_hours') {
        echo '<script>$(document).ready(function(){$("a[href=\'seller/seller_trading_hours\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'seller' && $uri[1] == 'seller_stripe_details') {
        echo '<script>$(document).ready(function(){$("a[href=\'seller/seller_stripe_details\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'seller' && $uri[1] == 'seller_details') {
        echo '<script>$(document).ready(function(){$("a[href=\'seller/seller_stripe_details\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'seller') {
        echo '<script>$(document).ready(function(){$("a[href=\'seller\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'business_users') {
        echo '<script>$(document).ready(function(){$("a[href=\'business_users\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'delivery_charges') {
        echo '<script>$(document).ready(function(){$("a[href=\'delivery_charges\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'comments') {
        echo '<script>$(document).ready(function(){$("a[href=\'comments\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'about_us') {
        echo '<script>$(document).ready(function(){$("a[href=\'about_us\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'faq_list') {
        echo '<script>$(document).ready(function(){$("a[href=\'faq_list\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'alcohol_awareness') {
        echo '<script>$(document).ready(function(){$("a[href=\'alcohol_awareness\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'seller_faq_list') {
        echo '<script>$(document).ready(function(){$("a[href=\'seller_faq_list\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'testimonials') {
        echo '<script>$(document).ready(function(){$("a[href=\'testimonials\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'privacy_policy') {
        echo '<script>$(document).ready(function(){$("a[href=\'privacy_policy\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'help_support') {
        echo '<script>$(document).ready(function(){$("a[href=\'help_support\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'terms_and_conditions') {
        echo '<script>$(document).ready(function(){$("a[href=\'terms_and_conditions\']").parents(\'li\').addClass("active");});</script>';
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
    else if ($uri[0] == 'orders' && $uri[1] == 'orders_not_completed') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/orders_not_completed\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'orders' && $uri[1] == 'orders_in_process') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/orders_in_process\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'orders' && $uri[1] == 'order_details') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'order_history' && $uri[1] == 'all_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'order_history/all_orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'order_history' && $uri[1] == 'cancelled_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'order_history/cancelled_orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'order_history' && $uri[1] == 'return_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'order_history/return_orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'orders' && $uri[1] == 'user_cancelled_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'orders/user_cancelled_orders\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'transaction_history') {
        echo '<script>$(document).ready(function(){$("a[href=\'transaction_history\']").parents(\'li\').addClass("active");});</script>';
    }
    else if ($uri[0] == 'order_rc' && $uri[1] == 'return_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'order_rc/return_orders\']").parents(\'li\').addClass("active");});</script>';
    }    
    else if ($uri[0] == 'order_rc' && $uri[1] == 'cancel_orders') {
        echo '<script>$(document).ready(function(){$("a[href=\'order_rc/cancel_orders\']").parents(\'li\').addClass("active");});</script>';
    }
        
    else if ($uri[0] == 'setting' && $uri[1] == 'email_setting') {
        echo '<script>$(document).ready(function(){$("a[href=\'setting/email_setting\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'setting' && $uri[1] == 'stuart_setting') {
        echo '<script>$(document).ready(function(){$("a[href=\'setting/stuart_setting\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'setting' && $uri[1] == 'payment_setting') {
        echo '<script>$(document).ready(function(){$("a[href=\'setting/payment_setting\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'setting' && $uri[1] == 'view_change_password') {
        echo '<script>$(document).ready(function(){$("a[href=\'setting/view_change_password\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'websetting' && $uri[1] == 'social_media') {
        echo '<script>$(document).ready(function(){$("a[href=\'websetting/social_media\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'websetting' && $uri[1] == 'website_home') {
        echo '<script>$(document).ready(function(){$("a[href=\'websetting/website_home\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'setting') {
        echo '<script>$(document).ready(function(){$("a[href=\'setting\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'drivers_request' && $uri[1] == 'driver_request') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers_request/driver_request\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'drivers_request' && $uri[1] == 'driver_vehicle_request') {
        echo '<script>$(document).ready(function(){$("a[href=\'drivers_request/driver_vehicle_request\']").parents(\'li\').addClass("active");});</script>';
    }
    
    else if ($uri[0] == 'website' && $uri[1] == 'homepage') {
        echo '<script>$(document).ready(function(){$("a[href=\'website/homepage\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'website' && $uri[1] == 'contactus') {
        echo '<script>$(document).ready(function(){$("a[href=\'website/contactus\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'website' && $uri[1] == 'become_driver') {
        echo '<script>$(document).ready(function(){$("a[href=\'website/become_driver\']").parents(\'li\').addClass("active");});</script>';
    } 
    else if ($uri[0] == 'website') {
        echo '<script>$(document).ready(function(){$("a[href=\'website\']").parents(\'li\').addClass("active");});</script>';
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
                                        <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">Admin</strong>
                                            </span>
                                        </span> 
                                    </a>
                                </center>
                                <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                    <li><a href="activity/logout">Logout</a></li>
                                </ul>
                            </div>
                            <div class="logo-element">
                                Janet-Collection
                            </div>
                        </li>

                        <li>
                            <a href="home"><i class="fa fa-dashboard "></i> <span class="nav-label">Dashboard</span></a>
                        </li>
                        
                        <li>
                            <a href="#"><i class="fa fa-users"></i> <span class="nav-label">App Users</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
				    <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Users</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
				    <ul class="nav metismenu">
					<li>
					    <a href="users"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">User Details</span></a>
					</li>
					<li>
					    <a href="users/uorder_history"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">User Order History</span></a>
					</li>
					<li>
					    <a href="users/product_return"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Refund History</span></a>
					</li>				
				    </ul>
				</li> 
<!--                                <li>
                                    <a href="business_users"><i class="fa fa-users" aria-hidden="true"></i> <span class="nav-label">Business Users</span></a>
                                </li>-->

				<li>
				    <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Sellers</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
				    <ul class="nav metismenu">
					<li>
					    <a href="seller"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Seller Details</span></a>
					</li>
					<li>
					    <a href="seller/view_history"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Seller History</span></a>
					</li>
					<li>
					    <a href="seller/seller_trading_hours"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Seller Trading Hours</span></a>
					</li>
					<li>
					    <a href="seller/seller_stripe_details"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Seller Stripe Details</span></a>
					</li>				
				    </ul>
				</li> 
				
				<li>
				    <a href="#"><i class="fa fa-user"></i> <span class="nav-label">Drivers</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
				    <ul class="nav metismenu">
					<li>
					    <a href="drivers"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Driver Details</span></a>
					</li>
					<li>
					    <a href="drivers/view_history"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Driver History</span></a>
					</li>
					<li>
					    <a href="drivers/driver_stripe_details"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Driver Stripe Details</span></a>
					</li>
					<li>
					    <a href="drivers/driver_delivery_zone"><i class="fa fa-plus-square" aria-hidden="true"></i> <span class="nav-label">Add Delivery Zone</span></a>
					</li>					
				    </ul>
				</li> 
                            </ul>
                        </li> 
                        
                        <li>
                            <a href="#"><i class="fa fa-flask"></i> <span class="nav-label">Products</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="product"><i class="fa fa-flask"></i> <span class="nav-label">Products</span></a>
                                </li>
				<li>
                                    <a href="product/archive_product"><i class="fa fa-flask"></i> <span class="nav-label">Archived Products</span></a>
                                </li>
				
                                <li>
                                    <a href="comments"><i class="fa fa-comments"></i> <span class="nav-label">Comments Moderation</span></a>
                                </li>
                                <li>
                                    <a href="return_policy"><i class="fa fa-file"></i> <span class="nav-label">Return Policy</span></a>
                                </li>
                            </ul>
                        </li>    
                        
                        <li>
                            <a href="#"><i class="fa fa-tags"></i> <span class="nav-label">App Masters</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="bonus"><i class="fa fa-tasks" aria-hidden="true"></i> <span class="nav-label">Bonus List</span></a>
                                </li>
				<li>
                                    <a href="suppliers"><i class="fa fa-tasks" aria-hidden="true"></i> <span class="nav-label">Suppliers</span></a>
                                </li>
				<li>
                                    <a href="delivery_charges"><i class="fa fa-tasks" aria-hidden="true"></i> <span class="nav-label">Delivery Charges</span></a>
                                </li>
                                <li>
                                    <a href="category"><i class="fa fa-tasks" aria-hidden="true"></i> <span class="nav-label">Category</span></a>
                                </li>
                                <li>
                                    <a href="sub_category"><i class="fa fa-tasks" aria-hidden="true"></i> <span class="nav-label">Sub Category</span></a>
                                </li>
                                <li>
                                    <a href="brand"><i class="fa fa-tasks" aria-hidden="true"></i> <span class="nav-label">Brand</span></a>
                                </li>
                                <li>
                                    <a href="volume"><i class="fa fa-tasks" aria-hidden="true"></i> <span class="nav-label">Volume</span></a>
                                </li>
                                
                                <li>
                                    <a href="delivery_zone"><i class="fa fa-tasks"></i> <span class="nav-label">Delivery Zone</span></a>
                                </li>

                                <li>
                                    <a href="zipcode"><i class="fa fa-tasks"></i> <span class="nav-label">Zip Code</span></a>
                                </li>
                                
                                <li>
                                    <a href="promocode"><i class="fa fa-tasks"></i> <span class="nav-label">Promocode</span></a>
                                </li> 
                                
                                <li>
                                    <a href="schedule_partition"><i class="fa fa-tasks"></i> <span class="nav-label">Schedule Partition List</span></a>
                                </li> 
                                
                                <li>
                                    <a href="country_code"><i class="fa fa-tasks"></i> <span class="nav-label">Country List</span></a>
                                </li>
                                
                            </ul>
                        </li>                                          
                        
                        <li>
                            <a href="#"><i class="fa fa-list"></i> <span class="nav-label">Order Management</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="#"><i class="fa fa-list"></i> <span class="nav-label">Orders</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                                    <ul class="nav metismenu">
                                        <li>
                                            <a href="orders/new_orders"><i class="fa fa-th-list"></i> <span class="nav-label">New Orders</span></a>
                                        </li>
                                        <li>
                                            <a href="orders/delivered_orders"><i class="fa fa-th-list"></i> <span class="nav-label">Completed Orders</span></a>
                                        </li>
                                        <li>
                                            <a href="orders/cancelled_orders"><i class="fa fa-th-list"></i> <span class="nav-label">Cancelled Orders</span></a>
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
                                    <a href="#"><i class="fa fa-list"></i> <span class="nav-label">Order History</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                                    <ul class="nav metismenu">
                                        <li>
                                            <a href="order_history/all_orders"><i class="fa fa-th-list"></i> <span class="nav-label">All Past Orders</span></a>
                                        </li>
                                        <li>
                                            <a href="order_history/cancelled_orders"><i class="fa fa-th-list"></i> <span class="nav-label">All Cancelled Orders</span></a>
                                        </li>
                                        <li>
                                            <a href="order_history/return_orders"><i class="fa fa-th-list"></i> <span class="nav-label">All Returned Orders</span></a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <li>
                                    <a href="#"><i class="fa fa-list"></i> <span class="nav-label">Order Return Management</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                                    <ul class="nav metismenu">
        <!--                                <li>
                                            <a href="order_rc/cancel_orders"><i class="fa fa-th-list"></i> <span class="nav-label">Cancel Order</span></a>
                                        </li>-->
                                        <li>
                                            <a href="order_rc/return_orders"><i class="fa fa-th-list"></i> <span class="nav-label">Return Product</span></a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <li>
                                    <a href="transaction_history"><i class="fa fa-money"></i> <span class="nav-label">Transaction History</span></a>
                                </li>
                            </ul>                            
                        </li> 
<!--                        <li>
                            <a href="order_history"><i class="fa fa-list"></i> <span class="nav-label">Order List</span></a>
                        </li> -->
                        
<!--                        <li>
                            <a href="#"><i class="fa fa-trophy"></i> <span class="nav-label">Loyalty Program</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="loyalty_program"><i class="fa fa-archive"></i> <span class="nav-label">Products</span></a>
                                </li>
                                <li>
                                    <a href="loyalty_program/brand_in_loyalty"><i class="fa fa-archive"></i> <span class="nav-label">Brands</span></a>
                                </li>
                                <li>
                                    <a href="loyalty_program/category_in_loyalty"><i class="fa fa-archive"></i> <span class="nav-label">Categories</span></a>
                                </li> 
                            </ul>
                        </li> -->
                        
<!--                        <li>
                            <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">VIP Club</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="vip_club"><i class="fa fa-archive"></i> <span class="nav-label">Products</span></a>
                                </li>
                                <li>
                                    <a href="loyalty_program/brand_in_loyalty"><i class="fa fa-archive"></i> <span class="nav-label">Brands</span></a>
                                </li>
                                <li>
                                    <a href="loyalty_program/category_in_loyalty"><i class="fa fa-archive"></i> <span class="nav-label">Categories</span></a>
                                </li> 
                            </ul>
                        </li>-->
			
			<li>
                            <a href="#"><i class="fa fa-flash"></i> <span class="nav-label">Driver Requests</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="drivers_request/driver_request"><i class="fa fa-file"></i> <span class="nav-label">Request To Update Personal Information</span></a>
                                </li>
				<li>
                                    <a href="drivers_request/driver_vehicle_request"><i class="fa fa-file"></i> <span class="nav-label">Request To Update Vehicle Information</span></a>
                                </li>
                            </ul>
                        </li>
			<li>
			    <a href="testimonials"><i class="fa fa-quote-right"></i> <span class="nav-label">Testimonials</span></a>
			</li> 
			
			<li>
			    <a href="alcohol_awareness"><i class="fa fa-list-alt"></i> <span class="nav-label">Alcohol Awareness</span></a>
			</li>
			
			<li>
			    <a href="admin_bonus"><i class="fa fa-money"></i> <span class="nav-label">Provide Bonus</span></a>
			</li>
			
<!--			<li>
                            <a href="#"><i class="fa fa-link"></i> <span class="nav-label">Website</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="website/homepage"><i class="fa fa-home"></i> <span class="nav-label">HomePage</span></a>
                                </li>
                            </ul>
                        </li>-->
                        
                        <li>
                            <a href="#"><i class="fa fa-cog"></i> <span class="nav-label">Website Settings</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="websetting/website_home"><i class="fa fa-home"></i> <span class="nav-label">Home Settings</span></a>
                                </li>				
                                <li>
                                    <a href="websetting/social_media"><i class="fa fa-share-alt"></i> <span class="nav-label">Social Media Links</span></a>
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
                                    <a href="notification"><i class="fa fa-info-circle"></i> <span class="nav-label">Send Notification</span></a>
                                </li> 
				<li>
                                    <a href="setting/stuart_setting"><i class="fa fa-cogs"></i> <span class="nav-label">Stuart Settings</span></a>
                                </li>
				
                                <li>
                                    <a href="setting/payment_setting"><i class="fa fa-money"></i> <span class="nav-label">Payment Settings</span></a>
                                </li>
                                <li>
                                    <a href="setting/email_setting"><i class="fa fa-envelope"></i> <span class="nav-label">Email Settings</span></a>
                                </li>
                                <li>
                                    <a href="setting/view_change_password"><i class="fa fa-key"></i> <span class="nav-label">Change Password</span></a>
                                </li> 
                            </ul>
                        </li> 
                        
                        <li>
                            <a href="#"><i class="fa fa-info"></i> <span class="nav-label">Help & Support</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">				
                                <li>
                                    <a href="about_us"><i class="fa fa-info-circle"></i> <span class="nav-label">About Us</span></a>
                                </li> 
                                <li>
                                    <a href="faq_list"><i class="fa fa-question-circle"></i> <span class="nav-label">FAQ</span></a>
                                </li>
				<li>
                                    <a href="seller_faq_list"><i class="fa fa-question-circle"></i> <span class="nav-label">Seller FAQ</span></a>
                                </li>
                                <li>
                                    <a href="privacy_policy"><i class="fa fa-file-powerpoint-o"></i> <span class="nav-label">Privacy Policy</span></a>
                                </li>
                                <li>
                                    <a href="terms_and_conditions"><i class="fa fa-terminal"></i> <span class="nav-label">Terms &  Conditions</span></a>
                                </li>
				<li>
                                    <a href="cookies"><i class="fa fa-adjust"></i> <span class="nav-label">Cookies</span></a>
                                </li>
                            </ul>
                        </li> 
                        
                        <li>
                            <a href="#"><i class="fa fa-question-circle"></i> <span class="nav-label">Contact Us</span><i class="fa fa-chevron-down" style="float:right;" aria-hidden="true"></i></a>
                            <ul class="nav metismenu">
                                <li>
                                    <a href="help_support"><i class="fa fa-user"></i> <span class="nav-label">User</span></a>
                                </li>
                                <li>
                                    <a href="seller_contact_us"><i class="fa fa-user"></i> <span class="nav-label">Seller</span></a>
                                </li>
                                <li>
                                    <a href="driver_contact_us"><i class="fa fa-user"></i> <span class="nav-label">Delivery drivers</span></a>
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
			    <div class="form-group">
				<!--<input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">-->
				<span style="font-size: 25px;">Administrator&nbsp;Panel</span>
			    </div>
                        </div>
                        <ul class="nav navbar-top-links navbar-right">
                            <li>
                                <a href="login/logout">
                                    <i class="fa fa-sign-out"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
