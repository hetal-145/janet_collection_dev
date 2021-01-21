<section class="cart mt-66 my_orders">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-12 col-md-12 col-lg-8 col-xl-8 p-3 p-sm-4 p-md-4 p-lg-4 p-xl-4 content-left">
                <div class="row mb-4">
                    <div class="col-12 align-self-center">
                        <h4 class="title mb-0">My Orders</h4>
                    </div>
                </div>
                <ul class="nav nav-tabs my_orders_catg mb-3">                    
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#pending_orders">pending orders</a>
                    </li>
		    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#delivered_orders">delivered orders</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane container fade" id="delivered_orders">
			<?php if(!empty($delivered_orders["list"])) { ?>
			    <input type="hidden" name="doffset" id="doffset" value="<?php echo $delivered_orders["offset"]; ?>">
			    <div class="row" id="delivered_ord">
				<?php foreach ($delivered_orders["list"] as $orders) { //echo "<pre>"; print_r($orders); ?>
				<div class="col-12 pb-3 pt-3 border-bottom">
				    <a href="javascript:void(0);" class="order_dt" data-order_id="<?php echo $orders["order_id"]; ?>">
					<div class="row">
					    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3 mb-sm-3 mb-md-0 mb-lg-0 mb-xl-0">
						<div class="media">
						    <div class="product_img mr-2">
							<img src="<?php echo $orders["products"][0]["feature_img"]; ?>" class="img-fluid" alt="">
						    </div>
						    <div class="media-body align-self-center">
							<h6 class="mt-0 mb-2 c-pink text-truncate"><?php echo $orders["products"][0]["product_name"]; ?></h6>
							<p class="desc mb-0"><?php echo $orders["products"][0]["product_name"]; ?> Sold By: <?php echo $orders["products"][0]["seller"]["seller_name"]; ?></p>
							<p class="desc mb-0">Order Id: <?php echo $orders["order_no"]; ?></p>
						    </div>
						</div>
					    </div>
					    <div class="col-5 col-sm-4 col-lg-3 col-md-2 col-xl-3 align-self-center">
						<p class="desc mb-1"><?php echo $orders["order_date"]; ?></p>
						<p class="desc mb-0">Qty. <strong><?php echo $orders["total_qty"]; ?></strong></p>
					    </div>
					    <div class="col-3 col-sm-4 col-lg-1 col-md-2 col-xl-1 pl-xl-0 pl-lg-0 align-self-center text-right">
						<label class="c-pink mb-0"><strong><?php echo $orders["products"][0]["volume"]; ?></strong></label>
					    </div>
					    <div class="col-4 col-sm-4 col-lg-2 col-md-2 col-xl-2 pl-xl-0 pl-lg-0 align-self-center text-right">
						<h5 class="mb-0"><?php echo $currency["value"].$orders["net_amount"]; ?></h5>
					    </div>
					</div>
				    </a>
				</div>
				<?php } ?>
			    </div>
			    <div class="col-12 text-center mt-4 loadMorePast">
				<a href="javascript:void(0);" class="btn btn-pink" id="loadMorePast">Load More</a>
			    </div>
			<?php } else { ?>
			    <div class="col-12 text-center mt-66">
				<label class="display_label">No Orders</label>
			    </div>
			<?php } ?>
                    </div>
		    
                    <div class="tab-pane active container" id="pending_orders">
			<?php if(!empty($pending_orders["list"])) { ?>
			    <input type="hidden" name="offset" id="offset" value="<?php echo $pending_orders["offset"]; ?>">
			    <div class="row" id="pending_ord">
				<?php foreach ($pending_orders["list"] as $orders) { //echo "<pre>"; print_r($orders); ?>
				<div class="col-12 pb-3 pt-3 border-bottom">
				    <a href="javascript:void(0);" class="order_dt" data-order_id="<?php echo $orders["order_id"]; ?>">
					<div class="row">
					    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3 mb-sm-3 mb-md-0 mb-lg-0 mb-xl-0">
						<div class="media">
						    <div class="product_img mr-2">
							<img src="<?php echo $orders["products"][0]["feature_img"]; ?>" class="img-fluid" alt="">
						    </div>
						    <div class="media-body align-self-center">
							<h6 class="mt-0 mb-2 c-pink text-truncate"><?php echo $orders["products"][0]["product_name"]; ?></h6>
							<p class="desc mb-0"><?php echo $orders["products"][0]["product_name"]; ?> Sold By: <?php echo $orders["products"][0]["seller"]["seller_name"]; ?></p>
							<p class="desc mb-0">Order Id: <?php echo $orders["order_no"]; ?></p>
						    </div>
						</div>
					    </div>
					    <div class="col-5 col-sm-4 col-lg-3 col-md-2 col-xl-3 align-self-center">
						<p class="desc mb-1"><?php echo $orders["order_date"]; ?></p>
						<p class="desc mb-0">Qty. <strong><?php echo $orders["total_qty"]; ?></strong></p>
					    </div>
					    <div class="col-3 col-sm-4 col-lg-1 col-md-2 col-xl-1 pl-xl-0 pl-lg-0 align-self-center text-right">
						<label class="c-pink mb-0"><strong><?php echo $orders["products"][0]["volume"]; ?></strong></label>
					    </div>
					    <div class="col-4 col-sm-4 col-lg-2 col-md-2 col-xl-2 pl-xl-0 pl-lg-0 align-self-center text-right">
						<h5 class="mb-0"><?php echo $currency["value"].$orders["net_amount"]; ?></h5>
					    </div>
					</div>
				    </a>
				</div>
				<?php } ?>
			    </div>
			    <div class="col-12 text-center mt-4 loadMore">
				<a href="javascript:void(0);" class="btn btn-pink" id="loadMore">Load More</a>
			    </div>
			<?php } else { ?>
			    <div class="col-12 text-center mt-66">
				<label class="display_label">No Orders</label>
			    </div>
			<?php } ?>
                    </div>
                </div>
            </div>
	    
	    <!-- order details -->
            <div class="col-12 col-12 col-md-12 col-lg-4 col-xl-4 p-3 p-sm-4 p-md-4 p-lg-4 p-xl-4 content-right">
                <div class="row mb-4">
                    <div class="col-10 pr-0 align-self-center">
                        <h4 class="title mb-0 otitle">Product</h4>
                    </div>
                    <div class="col-2 pl-0 text-right">
                        <label class="total_prod ototal_product">0</label>
                    </div>
                </div>

                <div class="row" id="view_product">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-12 col-xl-12">
                        <div class="row mb-4 justify-content-center">
                            <div class="col-12">
                                <div class="prod_img p-3 bg-white">
                                <img class="img-fluid oimg" src="<?php echo base_url() . '/assets/website/img/coktail.jpg'; ?>" alt="">
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 align-self-center col-lg-12 col-xl-12 mb-4">
                        <div class="row">                           
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-1">
                                <label class="title mb-0 odesc">Lorem ipsum simple dummy text.</label>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-1">
                                <label class="title mb-0">Delivered: <span class="odeliver">Date</span></label>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-1">
                                <label class="title mb-0">Sold By: <span class="oseller">Seller</span></label>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <label class="title mb-0">Order Status: <strong class="c-pink ostatus">Pending</strong></label>
                            </div>
                        </div>
                    </div>
		</div>
		<div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-4">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <label class="title mb-0">Shipping Details</label>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-2">
                                <div class="card address_card p-3">
                                    <p class="address desc oaddress">Address</p>
                                    <p class="mobile_num desc mb-0 ocontact">346-523-454</p>
                                </div>
                            </div>
                        </div>
                    </div>
		    
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-4">
                        <div class="row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <label class="title mb-0">Price Details</label>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-2">
                                <div class="card p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <p class="desc mb-0">Wallet</p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <p class="mb-0"><strong><?php echo $currency["value"]; ?></strong><strong class="owallet">0</strong></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8">
                                            <p class="desc mb-0">Price</p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <p class="mb-0"><strong><?php echo $currency["value"]; ?></strong><strong class="o_price">0</strong></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8">
                                            <p class="desc mb-0">Delivery</p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <p class="mb-0 odelviery">Free</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8">
                                            <p class="mb-0"><strong>Amount Payable</strong></p>
                                        </div>
                                        <div class="col-4 text-right">
                                            <p class="mb-0"><strong><?php echo $currency["value"]; ?></strong><strong class="o_amt_payable">0</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
		    
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mb-4" id="gift_card_sec">
                        <div class="row">
                            <div class="col-12">
                                <label class="title mb-0">Gift Card Details</label>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="card p-3">
                                    <div class="row">
                                        <div class="col-6 col-6 col-md-8 col-lg-7 col-xl-8">
                                            <p class="desc mb-0">Gift Card Sender</p>
                                        </div>
                                        <div class="col-6 col-6 col-md-4 col-lg-5 col-xl-4 text-right">
                                            <p class="mb-0 oname">John Doe</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-6 col-md-8 col-lg-7 col-xl-8">
                                            <p class="desc mb-0">Gift Code</p>
                                        </div>
                                        <div class="col-6 col-6 col-md-4 col-lg-5 col-xl-4 text-right">
                                            <p class="mb-0 text-uppercase ocode">GIFT123867</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-6 col-md-8 col-lg-7 col-xl-8">
                                            <p class="mb-0"><strong>Amount</strong></p>
                                        </div>
                                        <div class="col-6 col-6 col-md-4 col-lg-5 col-xl-4 text-right">
                                            <p class="mb-0"><strong><?php echo $currency["value"]; ?></strong><strong class="ogift_amt">0</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($top_picks)) { 
     include_once 'top_picks.php';
} ?>

<script type="text/javascript">
$(document).ready(function() {
    
    $("#loadMore").on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: "<?php echo base_url() . 'my_orders/get_upcoming_order'; ?>",
            type: "post",
            data: "offset="+$("#offset").val(), 
            success: function (resp)
            {
                //console.log(resp);
                if(resp == 'error') {
                    $(".loadMore").hide();
                }
                else {
                    var res = $.parseJSON(resp);  
		    //console.log(res);
                    $.each(res["list"], function(key, value) {
                        $("#pending_ord").append('<div class="col-12 pb-3 pt-3 border-bottom"><a href="javascript:void(0);" class="order_dt" data-order_id="'+ value["order_id"] +'"><div class="row"><div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3 mb-sm-3 mb-md-0 mb-lg-0 mb-xl-0"><div class="media"><div class="product_img mr-2"><img src="'+ value["products"][0]["feature_img"] +'" class="img-fluid" alt=""></div><div class="media-body align-self-center"><h6 class="mt-0 mb-2 c-pink text-truncate">'+ value["products"][0]["product_name"] +'</h6><p class="desc mb-0">'+ value["products"][0]["product_name"] +' Sold By: '+ value["products"][0]["seller"]["seller_name"] +'</p><p class="desc mb-0">Order Id: '+ value["order_no"] +'</p></div></div></div><div class="col-5 col-sm-4 col-lg-3 col-md-2 col-xl-3 align-self-center"><p class="desc mb-1">'+ value["order_date"] +'</p><p class="desc mb-0">Qty. <strong>'+ value["total_qty"] +'</strong></p></div><div class="col-3 col-sm-4 col-lg-1 col-md-2 col-xl-1 pl-xl-0 pl-lg-0 align-self-center text-right"><label class="c-pink mb-0"><strong>'+ value["products"][0]["volume"] +'</strong></label></div><div class="col-4 col-sm-4 col-lg-2 col-md-2 col-xl-2 pl-xl-0 pl-lg-0 align-self-center text-right"><h5 class="mb-0"><?php echo CURRENCY_CODE; ?>'+ value["products"][0]["net_total"] +'</h5></div></div></a></div>');
                    });
                    $("#offset").val("");
                    $("#offset").val(res["offset"]);
                }                
            }
        });
    });
    
    $("#loadMorePast").on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: "<?php echo base_url() . 'my_orders/get_past_order'; ?>",
            type: "post",
            data: "offset="+$("#doffset").val(), 
            success: function (resp)
            {
                //console.log(resp);
                if(resp == 'error') {
                    $(".loadMorePast").hide();
                }
                else {
                    var res = $.parseJSON(resp);  
		    //console.log(res);
                    $.each(res["list"], function(key, value) {
			
                        $("#delivered_ord").append('<div class="col-12 pb-3 pt-3 border-bottom"><a href="javascript:void(0);" class="order_dt" data-order_id="'+ value["order_id"] +'"><div class="row"><div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3 mb-sm-3 mb-md-0 mb-lg-0 mb-xl-0"><div class="media"><div class="product_img mr-2"><img src="'+ value["products"][0]["feature_img"] +'" class="img-fluid" alt=""></div><div class="media-body align-self-center"><h6 class="mt-0 mb-2 c-pink text-truncate">'+ value["products"][0]["product_name"] +'</h6><p class="desc mb-0">'+ value["products"][0]["product_name"] +' Sold By: '+ value["products"][0]["seller"]["seller_name"] +'</p><p class="desc mb-0">Order Id: '+ value["order_no"] +'</p></div></div></div><div class="col-5 col-sm-4 col-lg-3 col-md-2 col-xl-3 align-self-center"><p class="desc mb-1">'+ value["order_date"] +'</p><p class="desc mb-0">Qty. <strong>'+ value["total_qty"] +'</strong></p></div><div class="col-3 col-sm-4 col-lg-1 col-md-2 col-xl-1 pl-xl-0 pl-lg-0 align-self-center text-right"><label class="c-pink mb-0"><strong>'+ value["products"][0]["volume"] +'</strong></label></div><div class="col-4 col-sm-4 col-lg-2 col-md-2 col-xl-2 pl-xl-0 pl-lg-0 align-self-center text-right"><h5 class="mb-0"><?php echo $currency["value"]; ?>'+ value["products"][0]["net_total"] +'</h5></div></div></a></div>');
                    });
                    $("#doffset").val("");
                    $("#doffset").val(res["offset"]);
                }                
            }
        });
    });
    
    $(document).on("click", ".order_dt", function(e) {
	//e.preventDefault();
	var order_id = $(this).data("order_id");
	
        $.ajax({
            url: "<?php echo base_url() . 'my_orders/get_order_details'; ?>",
            type: "post",
            data: "order_id="+order_id, 
            success: function (resp)
            {
               // console.log(resp);
                if(resp == 'error') {
                    $(".loadMore").hide();
                }
                else {
                    var res = $.parseJSON(resp);  
		    //console.log(res);
		    $(".otitle").text(res["order_no"]);
		    $(".ototal_product").text(res["total_qty"]);
		    $("#view_product").html('');
		    
		    $.each(res["products"], function(key, value) {			
			$("#view_product").append('<div class="col-12 col-sm-12 col-md-6 col-lg-12 col-xl-12"><div class="row mb-4 justify-content-center"><div class="col-12"><div class="prod_img p-3 bg-white w-100"><img class="img-fluid oimg" src="'+value["feature_img"]+'" alt=""></div></div></div></div><div class="col-12 col-sm-12 col-md-6 align-self-center col-lg-12 col-xl-12 mb-4"><div class="row"><div class="col-12 mb-1"><label class="title mb-0 odesc">'+value["product_name"]+'</label></div><div class="col-12 mb-1"><label class="title mb-0">Delivered: <span class="odeliver">'+res["delivery_date"]+'</span></label></div><div class="col-12 mb-1"><label class="title mb-0">Sold By: <span class="oseller">'+value["seller"]["seller_name"]+'</span></label></div><div class="col-12"><label class="title mb-0">Order Status: <strong class="c-pink ostatus">'+res["orderStatus"]+'</strong></label></div></div></div>');
		    });
		    
		    $(".oaddress").text(res["shipping_details"]["address"]);
		    $(".ocontact").text(res["shipping_details"]["contactno"]);	
		    $(".owallet").text(res["wallet_amount"]);
		    $(".o_price").text(res["gross_amount"]);
		    $(".o_amt_payable").text(res["net_amount"]);
		    
		    if(res["delivery_charges"] == '0') {
			$(".odelviery").text('Free');
		    }
		    else {
			$(".odelviery").text(res["delivery_charges"]);
		    }
		    
		    if(res["gift_card_id"] == '0') {			
			$("#gift_card_sec").hide();			
		    }
		    else {
			$("#gift_card_sec").show();
			$(".oname").text(res["gift_card"]["sender_name"]);
			$(".ocode").text(res["gift_card"]["code"]);	
			$(".ogift_amt").text(res["gift_card"]["used_amount"]);	
		    }
		    
		}                                
            }
        });
	return false;
    });
});
</script>
