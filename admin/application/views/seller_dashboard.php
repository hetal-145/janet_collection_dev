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
</style>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Details</h2>
        <ol class="breadcrumb">
            <li>
                <a href="seller/view_history">Seller</a>
            </li>

            <li class="active">
                <strong>Details</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    
    <h2><?php echo $seller["seller_name"]; ?></h2>
    
    <div class="row">
	<div class="col-lg-3">
            <div class="widget style1 navy-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total New Orders </span>
                        <h2 class="font-bold"><?php echo $get_total_new_orders; ?></h2>
                    </div>
                </div>
	    </div>
        </div>
	
	<div class="col-lg-3">
            <div class="widget style1 blue-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Delivered Orders </span>
                        <h2 class="font-bold"><?php echo $get_total_delivered_orders; ?></h2>
                    </div>
                </div>
	    </div>
        </div>
	
	<div class="col-lg-3">
            <div class="widget style1 red-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Canceled Orders </span>
                        <h2 class="font-bold"><?php echo $get_total_cancelled_orders; ?></h2>
                    </div>
                </div>
	    </div>
        </div>
	
	<div class="col-lg-3">
            <div class="widget style1 yellow-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Orders </span>
                        <h2 class="font-bold"><?php echo $get_total_orders; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">	
	<div class="col-lg-4">
            <div class="widget style1 lazur-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Orders Picked Up By Drivers </span>
                        <h2 class="font-bold"><?php echo $orders_in_process; ?></h2>
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
                        <span class="font-bold"> Total Amount Yet To Receive </span>
			<h2 class="font-bold"><?php if($get_total_amount_to_receive > 0 ) { echo CURRENCY_CODE.$get_total_amount_to_receive; } else { echo CURRENCY_CODE.'0'; } ?></h2>
                    </div>
                </div>
            </div>
        </div>
	
	<div class="col-lg-4">
            <div class="widget style1 navy-bg">
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
    
    <div class="row">
	
	<div class="col-lg-3">
            <div class="widget style1 red-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-list fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Not Completed Orders </span>
                        <h2 class="font-bold"><?php echo $orders_not_completed; ?></h2>
                    </div>
                </div>
	    </div>
        </div>
	
	<div class="col-lg-3">
            <div class="widget style1 yellow-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-archive fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Schedule Orders </span>
                        <h2 class="font-bold"><?php echo $schedule_orders; ?></h2>
                    </div>
                </div>
            </div>
        </div>
	
	<div class="col-lg-3">
            <div class="widget style1 blue-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-archive fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Brands </span>
                        <h2 class="font-bold"><?php echo $get_total_brands; ?></h2>
                    </div>
                </div>
	    </div>
        </div>
	
	<div class="col-lg-3">
            <div class="widget style1 black-bg">
                <div class="row row_new">
                    <div class="col-4 col_new">
                        <i class="fa fa-archive fa-5x"></i>
                    </div>
                    <div class="col-8 col_new2">
                        <span class="font-bold"> Total Products </span>
                        <h2 class="font-bold"><?php echo $get_total_products; ?></h2>
                    </div>
                </div>
            </div>
        </div>	
	
    </div>
   
</div>

