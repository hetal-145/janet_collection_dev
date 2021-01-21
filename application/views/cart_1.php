<style type="text/css">
    /* Important part */
    .mdl_gift_card .modal-dialog{overflow-y: initial !important;}
    .mdl_gift_card .modal-body{height: 500px;overflow-y: auto;}
    .cart .content-left .product_img{height: 100px;}
    .product_img img{max-width: 100%;height: auto;max-height: 100%;}
    .pac-container { z-index: 9999 !important; }
</style>
<?php //echo "<pre>"; print_r($checkout); exit; ?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places&key=AIzaSyDgn9Lquao1_1j91kZptSRRb59A37bgtZI"></script>
<script type="text/javascript">
    var geocoder = new google.maps.Geocoder();
    var lat = '21.285407';
    var lng = '39.237551';
    var placeSearch, autocomplete;
    var map, marker;
    var evnt;
    function geocodePosition(pos) {
        //var latLng = new google.maps.LatLng(23.2156, 72.6369);
        geocoder.geocode({
            latLng: pos
        }, function (responses) {
            if (responses && responses.length > 0) {
                updateMarkerAddress(responses[0].formatted_address);
            } else {
                //updateMarkerAddress('Cannot determine address at this location.');
                updateMarkerAddress('');
            }
        });
    }

    function updateMarkerStatus(str) {
        document.getElementById('markerStatus').innerHTML = str;
    }

    var second = 1;
    function updateMarkerPosition(latLng) {
        if (!second) {
            $('.latitude').val(latLng.lat());
            $('.longitude').val(latLng.lng());
        }
        second=0;
    }
    
    var first = 1;
    function updateMarkerAddress(str) {

        //document.getElementById('address').innerHTML = str;
        if (!first) {
            $('.address').val(str);
        }        
        first = 0;
    }

    function initialize() {


        var latLng = new google.maps.LatLng(lat, lng);
        map = new google.maps.Map(document.getElementById('mapCanvas'), {
            zoom: 12,
            center: latLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoomControl: true,
//	    styles: [
//		{elementType: 'div.pac-container', stylers: [{zindex: '9999'}]},
//	    ],
        });
        marker = new google.maps.Marker({
            position: latLng,
            title: 'Point A',
            map: map,
            draggable: true
        });

        //Update current position info.
        updateMarkerPosition(latLng);
        geocodePosition(latLng);

        // Add dragging event listeners.
        google.maps.event.addListener(marker, 'dragstart', function () {
            //updateMarkerAddress('Dragging...');
            updateMarkerAddress('');
        });

        google.maps.event.addListener(marker, 'drag', function () {
            //updateMarkerStatus('Dragging...');
            updateMarkerAddress('');
            updateMarkerPosition(marker.getPosition());
        });

        google.maps.event.addListener(marker, 'dragend', function () {
            //updateMarkerStatus('Drag ended');
            updateMarkerAddress('');
            geocodePosition(marker.getPosition());
        });

        autocomplete = new google.maps.places.Autocomplete(
                (document.getElementById('address')));
        // When the user selects an address from the dropdown,
        // populate the address fields in the form.
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            fillInAddress();
        });
    }

    function fillInAddress() {
        var place = autocomplete.getPlace().geometry.location.toString();

        place = place.replace("(", "");
        place = place.replace(")", "");
        place = place.split(',');
        var lat = place[0].trim();
        var lng = place[1].trim();

        $('.latitude').val(lat);
        $('.longitude').val(lng);
        var myCenter = new google.maps.LatLng(lat, lng);

        marker.setPosition(myCenter);
        marker.setMap(map);
        map.setCenter(myCenter);
    }



    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = new google.maps.LatLng(
                        position.coords.latitude, position.coords.longitude);
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }

// Onload handler to fire off the app.
    google.maps.event.addDomListener(window, 'load', initialize);
</script>

<section class="cart mt-66">
    <div class="container-fluid">
	<form name="confirm_order" action="#" method="post" class="confirm_order">
        <div class="row">
            <div class="col-12 col-12 col-md-12 col-lg-8 col-xl-8 p-4 content-left">
                <div class="row mb-4">
                    <div class="col-12 align-self-center">
                        <h4 class="title mb-0">Product Lists</h4>
                    </div>
                </div>
		<input type="hidden" id="user_id" name="user_id" value="<?php echo $user["user_id"]; ?>" />		
                <?php
                if (!empty($checkout["products"])) {
                    foreach ($checkout["products"] as $products) {
                        ?>
                        <div class="col-12 pb-3 pt-3 px-0 border-bottom">
                            <div class="row">
                                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 mb-3 mb-sm-3 mb-md-0 mb-lg-0 mb-xl-0">
                                    <a href="<?php echo base_url() . 'products/product_detail?pid=' . base64_encode($products["product_id"]); ?>"><div class="media">
                                        <div class="product_img text-center mr-2">
                                            <img src="<?php echo $products["feature_img"]; ?>" class="img-fluid" alt="">
                                        </div>
                                        <div class="media-body align-self-center">
                                            <h6 class="mt-0 mb-2 c-pink"><?php echo $products["product_name"]; ?></h6>
                                            <p class="desc mb-0"><?php echo $products["product_name"]; ?></p> 
					    <p class="desc mb-0">Sold By: <?php echo $products["seller"]["seller_name"]; ?> <?php if($products["seller"]["is_open"] == 1) { ?><strong style="color:green;">(Open)</strong> <?php } else { ?> <strong style="color:red;">(Closed)</strong> <?php } ?> </p>
                                            <p class="desc mb-0">Delivery Charges: <?php echo $checkout["currency"] . $products["delivery_charges"]; ?></p>
                                        </div>
					</div></a>
                                </div>
                                <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 align-self-center text-center">
                                    <div class="input-group qnty">
                                        <button type="button" name="sub" class="btn btn-outline-secondary sub">-</button>                            
					<input class="form-control text-center product_id" type="hidden" value="<?php echo $products["product_id"]; ?>" />
					<input class="form-control text-center volume_id" type="hidden" value="<?php echo $products["volume_id"]; ?>" />
                                        <input name="qty" class="form-control text-center" type="text" value="<?php echo $products["qty"]; ?>" min="1" maxlength="3" disabled/>
                                        <button type="button" name="add" class="btn btn-outline-secondary add">+</button>			
					
                                    </div>
                                </div>
                                <div class="col-4 col-sm-4 col-md-2 col-lg-2 col-xl-2 pl-xl-0 pl-lg-0 align-self-center text-center">
                                    <h5 class="mb-0"><?php echo $checkout["currency"] . $products["total"]; ?></h5>
                                </div>
                                <div class="col-2 col-sm-1 col-md-1 col-lg-1 col-xl-1 pl-xl-0 pl-lg-0 align-self-center text-right">
                                    <button type="button" class="btn btn-primary delete_btn remove_product" data-product_id="<?php echo $products["product_id"]; ?>"  data-volume_id="<?php echo $products["volume_id"]; ?>"><i class="mdi mdi-delete"></i></button>
                                </div>
                            </div>
                        </div>
		
			
                        <?php } ?>
                <?php } else { ?>
		<div class="col-12 text-center mt-66">
		    <label class="display_label">No Products In Cart</label>
		</div>
                <?php } ?>

                <!--total-->
                <div class="col-12 pb-3 pt-3 mt-3 product_total">
                    <div class="row">
                        <div class="col-8 col-sm-8 col-lg-10 col-md-10 col-xl-10 text-right">
                            <h4 class="mb-0">Price :</h4>
                        </div>
                        <div class="col-4 col-sm-4 col-lg-2 col-md-2 col-xl-2 text-right">
                            <h4 class="mb-0"><?php
                                if (!empty($checkout["total_amount"])) {
                                    echo $checkout["currency"] . $checkout["total_amount"];
                                } else {
                                    echo 0;
                                }
                                ?></h4>
			    <input type="hidden" id="gross_amount" name="gross_amount" value="<?php if(isset($checkout["total_amount"])) { echo $checkout["total_amount"]; } else { echo "0"; } ?>" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-8 col-sm-8 col-lg-10 col-md-10 col-xl-10 text-right">
                            <h4 class="mb-0">Delivery Charges :</h4>
                        </div>
                        <div class="col-4 col-sm-4 col-lg-2 col-md-2 col-xl-2 text-right">
                            <h4 class="mb-0"><?php
                                if (!empty($checkout["delivery_charge"])) {
                                    echo $checkout["currency"] . $checkout["delivery_charge"];
                                } else {
                                    echo 0;
                                }
                                ?></h4>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-8 col-sm-8 col-lg-10 col-md-10 col-xl-10 text-right">
                            <h4 class="mb-0"><strong>Amount Payable :</strong></h4>
                        </div>
                        <div class="col-4 col-sm-4 col-lg-2 col-md-2 col-xl-2 text-right">
                            <h4 class="mb-0"><strong><?php echo $checkout["currency"]; ?></strong><strong id="net_amt"><?php
                                    if (!empty($checkout["amount_payable"])) {
                                        echo $checkout["amount_payable"];
                                    } else {
                                        echo 0;
                                    }
                                    ?></strong></h4>
			    <input type="hidden" name="net_amount" value="<?php if(isset($checkout["amount_payable"])) { echo $checkout["amount_payable"]; } else { echo "0"; } ?>" />
                        </div>
                    </div>
                </div>
		<hr>
		<div class="row mb-4 mt-66">
		    <div class="col-12 form-group">
			<h4 class="mb-0">Additional Information :</h4>
			<textarea name="add_info" id="add_info" class="form-control" rows="5"></textarea>
		    </div>	
                </div>
            </div>
	    
            <div class="col-12 col-12 col-md-12 col-lg-4 col-xl-4 p-4 content-right">
                <div class="row mb-2 mb-sm-2 mb-md-4 mb-lg-4 mb-xl-4">
                    <div class="col-8 col-sm-8 col-md-8 col-lg-8 col-xl-8 align-self-center">
                        <h4 class="title mb-0">Shopping Bag</h4>
                    </div>
                    <div class="col-4 text-right">
                        <label class="total_prod"><?php
                            if (!empty($checkout["products"])) {
				echo count($checkout["products"]);
                            } else {
                                echo "0";
                            }
                            ?></label>
                    </div>
                </div>
		
                <div class="row mb-4">
                    <div class="col-8 col-sm-8 col-md-9 col-lg-9 col-xl-9 align-self-center">
                        <label class="title mb-0">Shipping Details</label>
                    </div>
                    <div class="col-4 col-sm-4 col-md-3 col-lg-3 col-xl-3 text-right">
                        <label class="mb-0 c-pink add_select_lbl" data-toggle="modal" data-target="#mdl_apply_now">Add/Select</label>
                    </div>
                    <?php if (!empty($current_shipping_details)) { ?>
                        <div class="col-12 mt-3">
                            <div class="card address_card p-3">
                                <p class="address desc"><?php echo $current_shipping_details["address"] . ', ' . $current_shipping_details["zipcode"]; ?></p>
                                <p class="mobile_num desc mb-0"><?php echo $current_shipping_details["contactno"]; ?></p>
                            </div>
                        </div>			
                    <?php } ?>
		    <input type="hidden" name="shipping_id" value="<?php if(!empty($current_shipping_details["shipping_id"])) { echo $current_shipping_details["shipping_id"]; } else { echo '0'; } ?>" />
                </div>
                
                <input id="points" name="points" type="hidden" value="0" />
		
                <div class="row mb-4">
<!--                   <div class="col-12 col-sm-12 col-md-6 col-lg-12 col-xl-12 mb-3 mb-md-0">		
			<div class="card loyalti_card p-3">	
			    <div class="row">
				<div class="col-9 pr-0 align-self-center">
				    <p class="desc mb-0">Loyalty Point</p>			   		    
				</div>
				<div class="col-3 pl-0 text-right">
				     <div class="pretty p-icon p-round p-smooth mt-0">
					 <input id="points" name="points" type="checkbox" value="<?php //echo $user["loyalty_point"]; ?>" />
					<div class="state p-success">
					    <i class="icon mdi mdi-check"></i>
					    <label> </label>
					</div>
				    </div>
				</div>
                                <div class="col-12">
                                <h5 class="mb-2 mt-2"><strong><?php //echo $user["loyalty_point"]; ?></strong></h5>
				    <p class="desc mb-0">Redeem Point</p>
                                </div>
			    </div>			    
			</div>
                    </div>-->
		    
                    <div class="col-12 col-sm-12 col-md-6 col-lg-12 col-xl-12 mt-3">
                        <div class="card loyalti_card p-3">
			    <div class="row">
				<div class="col-9 pr-0 align-self-center">
				    <p class="desc mb-0">Wallet Balance</p>
				</div>
				<div class="col-3 pl-0 text-right">
				     <div class="pretty p-icon p-round p-smooth mt-0">
					 <input id="wallet" name="wallet" type="checkbox" value="<?php echo $user["wallet"]; ?>" />
					<div class="state p-success">
					    <i class="icon mdi mdi-check"></i>
					    <label> </label>
					</div>
				    </div>
				</div>
                                <div class="col-12">
                                    <h5 class="mb-2 mt-2"><strong><?php echo $user["wallet"]; ?></strong></h5>
				    <p class="desc mb-0">Use Wallet</p>
				</div>
			    </div>
                        </div>
                    </div>
		    
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-3">
                        <div class="card p-3">
                            <div class="row other_opt">
                                <div class="col-10 align-self-center">
                                    <div class="media">
                                        <div class="media-body">
                                            <h6 class="mt-0 mb-0">Repeat Delivery</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 text-right">
                                    <div class="pretty p-icon p-round p-smooth mt-0">
                                        <input value="1" id="repeat_delivery" name="is_repeat_order" type="checkbox"/>
                                        <div class="state p-success">
                                            <i class="icon mdi mdi-check"></i>
                                            <label> </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
		    
		    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-3">
                        <div class="card p-3">
                            <div class="row other_opt">
                                <div class="col-10 align-self-center">
                                    <div class="media">
                                        <div class="media-body">
                                            <h6 class="mt-0 mb-0">Order Now</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 text-right">
                                    <div class="pretty p-icon p-round p-smooth mt-0">
                                        <input value="1" id="order_now" name="order_done_type" type="radio"/>
                                        <div class="state p-success">
                                            <i class="icon mdi mdi-check"></i>
                                            <label> </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
		    
		    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-3">  
			<div class="card p-3">
                            <div class="row other_opt">
                                <div class="col-10 align-self-center">
                                    <div class="media">
                                        <div class="media-body">
                                            <h6 class="mt-0 mb-0">Schedule for Later</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 text-right">
                                    <div class="pretty p-icon p-round p-smooth mt-0">
                                        <input value="2" id="schedule_for_later" name="order_done_type" type="radio"/>
                                        <div class="state p-success">
                                            <i class="icon mdi mdi-check"></i>
                                            <label> </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
		    
		    <?php if(!empty($checkout["seller_slots"])) { $ii = 0; ?>
			<div class="col-12 mt-3 show_slot">
			    <div class="card p-3">
				<div class="row other_opt">
				    <div class="col-12">
					<h6 class="mt-0 mb-3">Schedule for Later</h6>
					<div class="ord-sched">
					    <ul class="nav nav-tabs ord-sched-nav nav-justified" id="myTab" role="tablist">
						<?php foreach($checkout["seller_slots"] as $key => $slots) { 
						    $ii = $key + 1; 
						?>
						    <li class="nav-item">
							<a class="nav-link text-center" id="tab_<?php echo $ii; ?>" data-toggle="tab" href="#days_<?php echo $ii; ?>" role="tab_<?php echo $ii; ?>" aria-controls="tab" aria-selected="false">
							    <label class="w-100 text-uppercase"><b><?php echo $slots["day"]; ?></b></label>
							    <small class="text-capitalize"><?php echo date('d M', strtotime($slots["date"])); ?></small>
							</a>
						    </li>
						<?php } ?>
						
					    </ul>
					</div>
					<div class="tab-content" id="myTabContent">
					    <?php foreach($checkout["seller_slots"] as $key => $slots) { 
						$ii = $key + 1; 
					    ?>
					    <div class="tab-pane fade" id="days_<?php echo $ii; ?>" role="tabpanel">
						<?php if(!empty($slots["slots"])) { foreach($slots["slots"] as $slot_val) { ?>						
						    <div class="media mt-3">
							<div class="pretty p-icon p-round p-smooth mt-0">
							    <input data-to_be_delivered="<?php echo $slots["date"]; ?>" name="time_slot" type="radio" value="<?php echo $slot_val; ?>" />
							    <div class="state p-success">
								<i class="icon mdi mdi-check"></i>
								<label> </label>
							    </div>
							</div>
							<div class="media-body ml-2 align-self-center">
							    <h6 class="mt-0 mb-0"><?php echo $slot_val; ?></h6>
							</div>
						    </div>
						<?php } } ?>
					    </div>
					    <?php } ?>

					</div>
				    </div>
				</div>
			    </div>
			</div>
		    <?php } ?>
                </div>
		
                <div class="row mb-4 other_opt">
                    <div class="col-10">
                        <div class="media">
                            <img class="mr-3 other_opt_icon" src="assets/website/img/coupon.svg" alt="">
                            <div class="media-body align-self-center">
                                <h6 class="mt-0 mb-0">Do you have Promo-code?</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div class="pretty p-icon p-round p-smooth mt-0">
                            <input id="apply_promo_code" type="checkbox" name="promocode_id" value="" />
                            <div class="state p-success">
                                <i class="icon mdi mdi-check"></i>
                                <label> </label>
                            </div>
                        </div>
                    </div>
                </div>
		
                <div class="row mb-4 other_opt">
                    <div class="col-10">
                        <div class="media">
                            <img class="mr-3 other_opt_icon" src="assets/website/img/giftbox.svg" alt="">
                            <div class="media-body align-self-center">
                                <h6 class="mt-0 mb-0">Send as a Gift</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div class="pretty p-icon p-round p-smooth mt-0">
                            <input type="checkbox" name="send_as_gift" value="1"/>
                            <div class="state p-success">
                                <i class="icon mdi mdi-check"></i>
                                <label> </label>
                            </div>
                        </div>
                    </div>
                </div>
		
                <div class="row mb-4 other_opt">
                    <div class="col-10">
                        <div class="media">
                            <img class="mr-3 other_opt_icon" src="assets/website/img/credit-card.svg" alt="">
                            <div class="media-body align-self-center">
                                <h6 class="mt-0 mb-0">Pay by Card</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div class="pretty p-icon p-round p-smooth mt-0">
                            <input type="checkbox" name="online" value="yes" />
                            <div class="state p-success">
                                <i class="icon mdi mdi-check"></i>
                                <label> </label>
                            </div>
                        </div>
                    </div>
                </div>
		
                <div class="row mb-4 other_opt">
                    <div class="col-10">
                        <div class="media">
                            <img class="mr-3 other_opt_icon" src="assets/website/img/gift-card.svg" alt="">
                            <div class="media-body align-self-center">
                                <h6 class="mt-0 mb-0">Payment by Gift Card</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 text-center">
                        <div class="pretty p-icon p-round p-smooth mt-0">
                            <input id="gift_card" type="checkbox" name="card_id" value="" />
                            <div class="state p-success">
                                <i class="icon mdi mdi-check"></i>
                                <label> </label>
                            </div>
                        </div>
                    </div>
                </div>		

                <div class="row justify-content-center">
                    <div class="col-12">
                        <button type="button" class="btn btn-checkout confirm_order_btn">checkout</button>
			<div class="error" style="color:red;"></div>
			<div class="success" style="color:green;"></div>
                    </div>
                </div>
            </div>
        </div>
	</form>
    </div>
</section>

<?php 
if (!empty($top_picks)) { 
     include_once 'top_picks.php';
} 

//gift card
include_once 'cart_gift_card.php';

//shipping
include_once 'cart_shipping.php';

//gift card
include_once 'cart_promocode.php';

//repeat delivery
include_once 'cart_repeat_delivery.php';
?>

<script type="text/javascript">
    $(".latitude").hide();
    $(".longitude").hide();
    $(".show_slot").hide();
    
    $(".select_radio").click(function() {
	$(this).prev().attr('checked', 'checked');
	$("#mdl_repeat_delivery").modal("toggle");
    });
    
    $("#schedule_for_later").click(function() {
	if($(this).is(':checked')) {
	    $(".show_slot").show();
	}
    });
    
    $("#order_now").click(function() {
	if($(this).is(':checked')) {	    
	    $(".show_slot").hide();
	}
    });
    
    $('.confirm_order_btn').click(function () {	
        alert("You are not allowed to place order.");
        return false;
	$('.error').html('');
	$('.success').html('');
	$('.form-control').removeClass('input_error');
	var valid = true;
	var frm = $('form[name = "confirm_order"]');

	var to_be_delivered = $("input[name='time_slot']:checked").data("to_be_delivered");
	var rep_del_opt = $("input[name='repeat_order_on']:checked").val();
	var order_done_type = $("input[name='order_done_type']:checked").val();
	
	if($("#schedule_for_later").is(':checked')) {	
	    if($("input[name='time_slot']").is(':checked')) {}
	    else {
		$("#modal_submit_reviews").find(".alert_msg").html("");
		$("a.modal_submit_reviewss").trigger("click");
		$("#modal_submit_reviews").find(".alert_msg").html("Please select time slot for delivery");
		valid = false;
	    }
	}
	
	if($("input[name='shipping_id']").val() == '0') {
	    $("#modal_submit_reviews").find(".alert_msg").html("");
	    $("a.modal_submit_reviewss").trigger("click");
	    $("#modal_submit_reviews").find(".alert_msg").html("Please select / add your delivery address (shipping address)");
	    
	    valid = false;
	}
	
	if($("input[name='order_done_type']").is(':checked')) {}
	else{
	    $("#modal_submit_reviews").find(".alert_msg").html("");
	    $("a.modal_submit_reviewss").trigger("click");
	    $("#modal_submit_reviews").find(".alert_msg").html("Please select order delivery type - Order Now / Schedule for Later");
	    
	    valid = false;
	}
		
	
	if (valid) {
	    $(this).html('Processing...');
	    var datastring = $("form[name=\"confirm_order\"]").serialize();
	    $.ajax({
		url: 'cart/confirm_order',
		data: datastring+"&to_be_delivered_date="+to_be_delivered+"&repeat_order_on="+rep_del_opt+"&order_done_type="+order_done_type,
		type: 'post',
		success: function (data) {
		    //console.log(data);	
		    $('.confirm_order_btn').html('Checkout');
		    if (data == '8') {
			$('.error').html('');
			$('.success').html('Order placed');
			window.location.href="my_orders";
		    } 
		    else if (data == '1') {
			$('.error').html('Quantity out of stock.');
			$('.success').html('');
		    }
		    else if (data == '2') {
			$('.error').html('Gift card code not applied successfully.');
			$('.success').html('');
		    }
		    else if (data == '3') {
			$('.error').html('No amount left in your gift card.');
			$('.success').html('');
		    }
		    else if (data == '4') {
			$('.error').html('Code already expired or You are not valid user to use this gift card.');
			$('.success').html('');
		    }
		    else if (data == '5') {
			$('.error').html('No Order Found.');
			$('.success').html('');
		    }
		    else if (data == '9') {
			$('.error').html('You do not have enough balance in your wallet to pay.');
			$('.success').html('');
		    }
		    else if (data == '10') {
			$('.error').html('Please select any one way of payment.');
			$('.success').html('');
		    }
		    else {
			var resp = $.parseJSON(data);
			console.log(resp);			
			window.location.href="cart/do_payment?order_id="+resp["order_id"];
		    }
		}
	    });
	    return false;
	}
    });
    
    $('input#apply_promo_code[type="checkbox"]').on('change', function (e) {
        if (e.target.checked) {
            $('#mdl_apply_promo_code').modal();
        }
    });
    
     $('input#gift_card[type="checkbox"]').on('change', function (e) {
        if (e.target.checked) {
            $('#mdl_gift_card').modal();
        }
    });
    
    $('input#repeat_delivery[type="checkbox"]').on('change', function (e) {
        if (e.target.checked) {
            $('#mdl_repeat_delivery').modal();
        }
    });
    
    $('.add').click(function () {
        if ($(this).prev().val() < 999) {
	    $(this).prev().val(+$(this).prev().val() + 1);
        
	    var product_id = $(this).prev().prev().prev().val();
	    var volume_id = $(this).prev().prev().val();
	    var qty = +$(this).prev().val();
//	    console.log(product_id);
//	    console.log(volume_id);
//	    console.log(qty);

	    $.ajax({
		url: "<?php echo base_url() . 'cart/update_bag'; ?>",
		type: "post",
		data: "product_id=" + product_id + "&volume_id=" + volume_id + "&qty=" + qty,
		success: function (resp) {
		    //console.log(resp);
		    $("#modal_submit_reviews").find(".alert_msg").html("");
		    
		    if (resp == 1) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Item quantity updated.");
			window.location.href = "<?php echo base_url() . 'cart'; ?>";
		    } else if (resp == 2) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Quantity out of stock.");
		    } else if (resp == 3) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("No data found.");
		    } else if (resp == 4) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Quantity not updated.");
		    } 
		}
	    });
	}
        
    });

    $('.sub').click(function () {
        if ($(this).next().next().next().val() > 1) {            
            $(this).next().next().next().val(+$(this).next().next().next().val() - 1);
	    
	    var product_id = $(this).next().val();
	    var volume_id = $(this).next().next().val();
	    var qty = +$(this).next().next().next().val();
//	    console.log(product_id);
//	    console.log(volume_id);
//	    console.log(qty);

	    $.ajax({
		url: "<?php echo base_url() . 'cart/update_bag'; ?>",
		type: "post",
		data: "product_id=" + product_id + "&volume_id=" + volume_id + "&qty=" + qty,
		success: function (resp) {
		    //console.log(resp);
		    $("#modal_submit_reviews").find(".alert_msg").html("");
		    
		    if (resp == 1) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Item quantity updated.");
			window.location.href = "<?php echo base_url() . 'cart'; ?>";
		    } else if (resp == 2) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Quantity out of stock.");
			
		    } else if (resp == 3) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("No data found.");
		
		    } else if (resp == 4) {
			$("a.modal_submit_reviewss").trigger("click");
			$("#modal_submit_reviews").find(".alert_msg").html("Quantity not updated.");
			
		    } 
		}
	    });		
        }
    });    
    
    $(".apply_promo").click(function() {		
	$('.error_promocode').html('');
	$('.form-control').removeClass('input_error');
	var valid = true;

	var promocode = $('input[name="promocode"]').val();
	if (!promocode || !promocode.trim()) {
	    $('input[name="promocode"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter promocode!'));
	    valid = false;
	}

	if (valid) {
	    $(this).html('Processing...');
	    var promocodes = $("#promocode").val();
	    var gross_amount = $("#gross_amount").val();
	    var user_id = $("#user_id").val();

	    $.ajax({
		url: 'cart/apply_promocode',
		data: "promocode="+promocodes+"&user_id="+user_id+"&gross_amount="+gross_amount,
		type: 'post',
		success: function (data) {
		    //console.log(data);
		    $('.apply_promo').html('Apply');
		    if (data == '4') {
			$('.error_promocode').html('You will not be able to use this promocode.');
		    } 
		    else if (data == '2') {
			$('.error_promocode').html('Code Already Used..');
		    }
		    else if (data == '3') {
			$('.error_promocode').html('Code Already Expired.');
		    }
		    else {
			$('.error_promocode').html('');
			//alert(data);
			var resp = $.parseJSON(data);
			$("#apply_promo_code").val(resp[1]);
			$("input[name=net_amount]").val(resp[0]);
			$("#net_amt").text(resp[0]);
			$('#mdl_apply_promo_code').modal('toggle');			
		    }
		}
	    });
	}
    });
    
    $('.Add_new_btn').click(function () {
        $('.Add_new_address').show();
        $('.address_card_select').hide();
    });
    
    $('.save_address_btn').click(function () {	
	$('.error_shipping').html('');
	$('.form-control').removeClass('input_error');
	var valid = true;
	var frm = $('form[name = "add_new_address"]');

	var name = frm.find('[name="name"]').val();
	if (!name || !name.trim()) {
	    frm.find('[name="name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your full name!'));
	    valid = false;
	}

	var address = frm.find('[name="address"]').val();
	if (!address || !address.trim()) {
	    frm.find('[name="address"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your address!'));
	    valid = false;
	}

	var contactno = frm.find('[name="contactno"]').val();
	if (!contactno || !contactno.trim()) {
	    frm.find('[name="contactno"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter contact number!'));
	    valid = false;
	}

	var zipcode = frm.find('[name="zipcode"]').val();
	if (!zipcode || !zipcode.trim()) {
	    frm.find('[name="zipcode"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your zipcode!'));
	    valid = false;
	}

	var latitude = frm.find('[name="latitude"]').val();
	if (!latitude || !latitude.trim()) {
	    frm.find('[name="latitude"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter latitude!'));
	    valid = false;
	}
	
	var longitude = frm.find('[name="longitude"]').val();
	if (!longitude || !longitude.trim()) {
	    frm.find('[name="longitude"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter longitude!'));
	    valid = false;
	}

	if (valid) {
	    $(this).html('Processing...');
	    var datastring = $("form[name=\"add_new_address\"]").serialize();
	    var user_id = $("#user_id").val();
	    $.ajax({
		url: 'cart/add_shipping',
		data: datastring+"&user_id="+user_id,
		type: 'post',
		success: function (data) {
		    //console.log(data);
		    $('.save_address_btn').html('Save');
		    if (data == '1') {
			$('.error_shipping').html('');
			$('.success_shipping').html('Shipping details added to account.');
			$('.Add_new_address').hide();
			$('.address_card_select').show();
			window.location.reload();
		    } 
		    else if (data == '2') {
			$('.error_shipping').html('Shipping details cannot be added to account.');
			$('.success_shipping').html('');
		    }
		    else if (data == '3') {
			$('.error_shipping').html('Invalid zipcode.');
			$('.success_shipping').html('');
		    }
		    return false;
		}
	    });
	}
    });

    $(".delete_shipping_btn").on("click", function () {
        var shipping_id = $(this).data('shipping_id');
        $.ajax({
            url: "<?php echo base_url() . 'cart/delete_shipping_details'; ?>",
            type: "post",
            data: "shipping_id=" + shipping_id,
            success: function (resp)
            {
		$("#modal_submit_reviews").find(".alert_msg").html("");
		    
                if (resp == 'error') {
		    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html("Shipping details not deleted.");
                } else {
                    $(".delete_shipping_btn").parent().parent().parent().remove('remove_add_' + resp);
                    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html("Shipping details deleted.");
                    location.reload();
                }
            }
        });
    });

    $(".remove_product").on("click", function () {
        var product_id = $(this).data('product_id');
        var volume_id = $(this).data('volume_id');

        $.ajax({
            url: "<?php echo base_url() . 'cart/remove_product'; ?>",
            type: "post",
            data: "volume_id=" + volume_id + "&product_id=" + product_id,
            success: function (resp)
            {
		$("#modal_submit_reviews").find(".alert_msg").html("");
		    
                if (resp == 'error') {
                    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html("Product not deleted.");
                } else {
                    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html("Product deleted.");
                    location.reload();
                }
            }
        });
    });

    $(".update_gift_card").on("click", function () {
        var card_id = $(this).val();
        $("input[name=card_id]").val(card_id);
	$('#mdl_gift_card').modal('toggle');
    });
    
    $(".update_current_address").on("click", function () {
        var shipping_id = $(this).val();
        $.ajax({
            url: "<?php echo base_url() . 'cart/update_current_address'; ?>",
            type: "post",
            data: "shipping_id=" + shipping_id,
            success: function (resp)
            {
		$("#modal_submit_reviews").find(".alert_msg").html("");
		    
                if (resp == 2) {
                    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html("Shipping details not updated.");
                } else if (resp == 3) {
                    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html("Shipping details not found.");
                } else {
                    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html("Shipping details updated.");
                    location.reload();
                }
            }
        });
    });
</script>