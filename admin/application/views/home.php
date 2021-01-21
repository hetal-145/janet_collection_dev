<style>
    .row_new{
        display: flex;
        flex-wrap: wrap; 
        margin-right: 0; 
        margin-left: 0;
    }
    
    .col_new{
        flex: 0 0 33.333333%;
    }
    
    .col_new2{
        text-align:right !important; 
        flex: 0 0 66.666667%;
    }
    .black-bg { color: #fff;}
    a .white-bg { color: #676a6c;}
    .show_notify{
	width: 50%;
	float: right;
    }
</style>

<div class="show_notify" id="show_notify"></div>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Dashboard</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>

            <li class="active">
                <strong>Dashboard</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <h2>Income Statistics</h2>
    
    <div class="row">
	<div class="col-lg-4">
            <div class="widget style1 red-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-money fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Amount Yet To Receive </span>
                        <h2 class="font-bold"><?php if($get_total_amount_to_receive > 0 ) { echo CURRENCY_CODE.$get_total_amount_to_receive; } else { echo CURRENCY_CODE.'0'; } ?></h2>
                    </div>
                </div>
            </div>
        </div>
	
	<div class="col-lg-4">
            <div class="widget style1 white-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-money fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Income </span>
                        <h2 class="font-bold"><?php if($get_total_income > 0 ) { echo CURRENCY_CODE.$get_total_income; } else { echo CURRENCY_CODE.'0'; } ?></h2>
                    </div>
                </div>
            </div>
        </div>
	
    </div>
    
    <hr><h2>Order Statistics</h2>
    
    <div class="row">
	<div class="col-lg-4">
            <a href="<?php echo base_url().'orders/new_orders'; ?>"><div class="widget style1 navy-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total New Orders </span>
                        <h2 class="font-bold"><?php echo $get_total_new_orders; ?></h2>
                    </div>
                </div>
		</div></a>
        </div>
	
	<div class="col-lg-4">
            <a href="<?php echo base_url().'orders/orders_in_process'; ?>"><div class="widget style1 yellow-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Orders Picked Up By Drivers </span>
                        <h2 class="font-bold"><?php echo $orders_in_process; ?></h2>
                    </div>
                </div>
		</div></a>
        </div>
	
	<div class="col-lg-4">
            <a href="<?php echo base_url().'orders/delivered_orders'; ?>"><div class="widget style1 blue-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Delivered Orders </span>
                        <h2 class="font-bold"><?php echo $get_total_delivered_orders; ?></h2>
                    </div>
                </div>
		</div></a>
        </div>
	
	<div class="col-lg-4">
            <a href="<?php echo base_url().'orders/cancelled_orders'; ?>"><div class="widget style1 red-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Canceled Orders </span>
                        <h2 class="font-bold"><?php echo $get_total_cancelled_orders; ?></h2>
                    </div>
                </div>
		</div></a>
        </div>	
	
	<div class="col-lg-4">
            <a href="<?php echo base_url().'orders/orders_not_completed'; ?>"><div class="widget style1 lazur-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Not Completed Orders </span>
                        <h2 class="font-bold"><?php echo $orders_not_completed; ?></h2>
                    </div>
                </div>
		</div></a>
        </div>
	
	<div class="col-lg-4">
            <div class="widget style1 white-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-reorder fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Orders </span>
                        <h2 class="font-bold"><?php echo $total_orders; ?></h2>
                    </div>
                </div>
            </div>
        </div> 
	
	<div class="col-lg-4">
	    <div class="widget style1 black-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Total Alcoholic Orders Received </span>
			<h2 class="font-bold"><?= $total_alcohol_products_orders ?></h2>
		    </div>
		</div>
	    </div>             
        </div>
    </div>
    
    <hr><h2>New Requests & Registrations</h2>
    
    <div class="row">
	<div class="col-lg-3">
	    <a href="drivers_request/driver_request"><div class="widget style1 yellow-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Driver request to update profile details  </span>
			<h2 class="font-bold"><?= $new_driver_request ?></h2>
		    </div>
		</div>
	    </div></a>           
	</div>
	
	<div class="col-lg-3">
	    <a href="drivers_request/driver_vehicle_request"><div class="widget style1 navy-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Driver request to update vehicle details </span>
			<h2 class="font-bold"><?= $new_driver_request_vehicle ?></h2>
		    </div>
		</div>
	    </div></a>           
        </div>
	
	<div class="col-lg-3">
	    <a href="seller"><div class="widget style1 blue-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> New seller to verify </span>
			<h2 class="font-bold"><?= $new_sellers ?></h2>
		    </div>
		</div>
		</div></a>
        </div>
	
	<div class="col-lg-3">
	    <a href="drivers"><div class="widget style1 lazur-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> New drivers to verify </span>
			<h2 class="font-bold"><?= $new_drivers ?></h2>
		    </div>
		</div>
		</div></a> 
        </div>  
    </div>
    
    <hr><h2>User Statistics</h2>
    
    <div class="row">
	<div class="col-lg-3">
	    <div class="widget style1 red-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> User Online Now </span>
			<h2 class="font-bold"><?= $users_online_day ?></h2>
		    </div>
		</div>
	    </div>            
        </div>
	
	<div class="col-lg-3">
	    <div class="widget style1 navy-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Drivers Online Now </span>
			<h2 class="font-bold"><?= $drivers_online_day ?></h2>
		    </div>
		</div>
	    </div>            
        </div>
	
	<div class="col-lg-3">
	    <div class="widget style1 blue-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> User Online This Week </span>
			<h2 class="font-bold"><?= $users_online_week ?></h2>
		    </div>
		</div>
	    </div>  
        </div>
	
	<div class="col-lg-3">
	    <div class="widget style1 black-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> User Online This Month </span>
			<h2 class="font-bold"><?= $users_online_month ?></h2>
		    </div>
		</div>
	    </div>  
        </div>  
    </div>
    
    <div class="row">	
	<div class="col-lg-3">
	    <div class="widget style1 yellow-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Total Users Sign Up This Week </span>
			<h2 class="font-bold"><?= $user_signedup_this_week ?></h2>
		    </div>
		</div>
	    </div>
        </div>		
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'users'; ?>"><div class="widget style1 lazur-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total App Users </span>
                        <h2 class="font-bold"><?php echo $total_users; ?></h2>
                    </div>
                </div>
            </div></a>
        </div> 	
    
	<div class="col-lg-3">
            <a href="<?php echo base_url().'seller'; ?>"><div class="widget style1 white-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Sellers </span>
                        <h2 class="font-bold"><?php echo $total_sellers; ?></h2>
                    </div>
                </div>
            </div></a>
        </div>  
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'drivers'; ?>"><div class="widget style1 red-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Drivers </span>
                        <h2 class="font-bold"><?php echo $total_drivers; ?></h2>
                    </div>
                </div>
            </div></a>
        </div> 
    </div>
    
    <hr><h2>Products Statistics</h2>
    
    <div class="row">	
	<div class="col-lg-3">
	    <div class="widget style1 blue-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Total Alcoholic Products </span>
			<h2 class="font-bold"><?= $total_alcohol_products ?></h2>
		    </div>
		</div>
	    </div>
        </div>
	
	<div class="col-lg-3">
	    <div class="widget style1 white-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Total Non-Alcoholic Products </span>
			<h2 class="font-bold"><?= $total_non_alcohol_products ?></h2>
		    </div>
		</div>
	    </div>
        </div>
	
	<div class="col-lg-3">
	    <div class="widget style1 black-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Total Alcoholic Products Sold </span>
			<h2 class="font-bold"><?= $total_alcohol_products_sold ?></h2>
		    </div>
		</div>
	    </div>           
        </div> 
    	
	<div class="col-lg-3">
	    <div class="widget style1 navy-bg">
		<div class="row row_new">
		    <div class="col-4 col_new">
			<i class="fa fa-tag fa-5x"></i>
		    </div>
		    <div class="col-8 col_new2 text-right">
			<span> Total Non-Alcoholic Products Sold </span>
			<h2 class="font-bold"><?= $total_non_alcohol_products_sold ?></h2>
		    </div>
		</div>
	    </div>            
        </div>
    </div>
    
    <div class="row">	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'product'; ?>"><div class="widget style1 lazur-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-flask fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Products </span>
                        <h2 class="font-bold"><?php echo $total_products; ?></h2>
                    </div>
                </div>
		</div></a>
        </div> 
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'loyalty_program'; ?>"><div class="widget style1 red-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-flask fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Loyalty Products </span>
                        <h2 class="font-bold"><?php echo $total_loyalty_products; ?></h2>
                    </div>
                </div>
            </div></a>
        </div>
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'vip_club'; ?>"><div class="widget style1 yellow-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-flask fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total VIP CLub Products </span>
                        <h2 class="font-bold"><?php echo $total_vip_products; ?></h2>
                    </div>
                </div>
            </div></a>
        </div>         
    </div>
    
    <hr><h2>Other Statistics</h2>
    
    <div class="row">	
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'category'; ?>"><div class="widget style1 blue-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Categories </span>
                        <h2 class="font-bold"><?php echo $total_categories; ?></h2>
                    </div>
                </div>
		</div></a>
        </div>
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'sub_category'; ?>"><div class="widget style1 white-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Sub Categories </span>
                        <h2 class="font-bold"><?php echo $total_subcategories; ?></h2>
                    </div>
                </div>
            </div></a>
        </div>
        
        <div class="col-lg-3">
            <a href="<?php echo base_url().'brand'; ?>"><div class="widget style1 black-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Brands </span>
                        <h2 class="font-bold"><?php echo $total_brands; ?></h2>
                    </div>
                </div>
            </div></a>
        </div> 
		
	<div class="col-lg-3">
            <a href="<?php echo base_url().'suppliers'; ?>"><div class="widget style1 navy-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-user fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Suppliers </span>
                        <h2 class="font-bold"><?php echo $total_suppliers; ?></h2>
                    </div>
                </div>
            </div></a>
        </div>		
    </div>
    
    <div class="row"> 
	<div class="col-lg-3">
            <a href="<?php echo base_url().'delivery_zone'; ?>"><div class="widget style1 lazur-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-map-marker fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Delivery Zones </span>
                        <h2 class="font-bold"><?php echo $total_dz; ?></h2>
                    </div>
                </div>
            </div></a>
        </div> 
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'promocode'; ?>"><div class="widget style1 red-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list-alt fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Promocodes </span>
                        <h2 class="font-bold"><?php echo $total_promocodes; ?></h2>
                    </div>
                </div>
            </div></a>
        </div>  
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'gift_card'; ?>"><div class="widget style1 yellow-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-gift fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Gift Cards </span>
                        <h2 class="font-bold"><?php echo $total_gift_cards_sent; ?></h2>
                    </div>
                </div>
            </div></a>
        </div>  
	
	<div class="col-lg-3">
            <a href="<?php echo base_url().'testimonials'; ?>"><div class="widget style1 blue-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-quote-right fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Testimonials </span>
                        <h2 class="font-bold"><?php echo $total_testimonials; ?></h2>
                    </div>
                </div>
            </div></a>
        </div>        
    </div>
</div>
<script>
//$(document).ready(function(){
//    setInterval(function(){
//	// this will run after every 5 seconds
//	$.ajax({
//	    url: 'home/show_notifications',
//	    data: '',
//	    type: 'post',
//	    success: function (response) {
//		$('#show_notify').html('');
//		//console.log(response);
//		if(response != "") {
//		    var resp = $.parseJSON(response);
//		    var countarr = resp.length;
//		   // alert(resp.length);
//		   
//		    setTimeout(function() {
//			console.log(resp);
//			console.log(resp.indexOf(countarr));
////			    $('#show_notify').append('<div class="alert alert-success alert-dismissable" id="div'+resp["countarr"]+'"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+resp["countarr"].message+'</div>');
////			    setInterval(function(){
////				$('#div'+resp["countarr"]+'').fadeOut('fast');
////			    }, 1000);
//		    }, 2000); // <-- time in milliseconds
//		    countarr++;
//		    
////		    $.each(resp, function(index, value){
////						
////		    });		    
//		}
//	    }
//	});
//    }, 10000);
//
//});
</script>
