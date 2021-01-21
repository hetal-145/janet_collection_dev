<?php // echo "<pre>"; print_r($product_details); echo "</pre>"; exit;     ?>
<section class="product_detail mt-66 prod_det">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 content-left text-center">
                <div id="prod_carousel" class="carousel slide prod_img_box" data-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="<?php echo $product_details["feature_img"]; ?>" class="img-fluid" alt="">
                        </div>
			<?php 
			if(!empty($product_details["gallery_img"][0]["image_name"])) { 
			    foreach($product_details["gallery_img"] as $key => $gallery_img) {
				$k = $key+1;
			?>
			<div class="carousel-item">
                            <img src="<?php echo $gallery_img["image_name"]; ?>" class="img-fluid" alt="">
                        </div>
			<?php
			    }
			}
			?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 content-right align-self-center ">
                <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_details["product_id"]; ?>">
                <h2 class="prod_title mb-3"><?php echo $product_details["product_name"]; ?></h2>
                <p class="prod_by_admin"><?php echo $product_details["product_name"]; ?> sold by <?php echo $product_details["seller"]["seller_name"]; ?> <label class="chips"><img class="img-fluid" src="<?php echo base_url() . '/assets/website/img/star_gray.svg'; ?>" alt=""> <?php echo $product_details["product_rating"]; ?> <span class="c-pink">(<?php echo $product_details["total_product_reviews"]; ?> Reviews)</span></label></p>
                <div class="row mb-3">
                    <div class="col-4 align-self-center">
                        <p class="prod_price mb-0" id="price"><?php echo CURRENCY_CODE . $product_details["volume_id"][0]["normal_sell_price"]; ?> <small class="discount_price"><strike id="actual_price"><?php echo CURRENCY_CODE . $product_details["volume_id"][0]["actual_price"]; ?></strike></small></p>
                    </div>
                    <div class="col-8 text-right">
                        <label class="unit_box"><?php echo $product_details["abv_percent"]; ?>% ABV</label>
                        <label class="unit_box"><?php echo $product_details["alchol_units"]; ?> Unit</label>
                    </div>
                </div>
                <p class="desc mb-3"><?php echo $product_details["description"]; ?></p>
		
		<div class="row">
		    <div class="col-6 text-left">
			<p class="prod_by_admin">Category: <span class="c-pink"><?php echo $product_details["category_name"]; ?></span></p>
		    </div>

		    <div class="col-6 text-right">
			<p class="prod_by_admin">Brand: <span class="c-pink"><?php echo $product_details["brand_name"]; ?></span></p>
		    </div>
		</div>
		<p class="prod_by_admin">Country of Origin: <span class="c-pink"><?php echo $product_details["country_id"]; ?></span></p>
		
                <span class="text-danger" id="outstock"></span>
		<span class="text-success" id="instock"></span>
		
                <div class="row mb-3 mt-3">
		    <?php if($product_details["isvolume"]) { ?>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-4">
                        <select class="form-control volumes" name="volume_id" id="volume_id" style="height: 100% !important;">
                            <?php foreach ($product_details["volume_id"] as $key => $vol) { ?>
				<option value="<?php echo $vol["volume_id"]; ?>" id="v_<?php echo $vol["volume_id"]; ?>" data-price="<?php echo CURRENCY_CODE . $vol["normal_sell_price"]; ?>" data-actual_price="<?php echo CURRENCY_CODE . $vol["actual_price"]; ?>" data-stock="<?php echo $vol["units"]; ?>"><?php echo $vol["volumes"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
		    <?php } ?>
		    
                    <div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-4 show_qty">
                        <div class="input-group qnty">
                            <button type="button" id="sub" class="btn btn-outline-secondary sub">-</button>
                            <input name="qty" class="form-control text-center" type="text" id="qty" value="1" min="1" maxlength="3" disabled/>
                            <button type="button" id="add" class="btn btn-outline-secondary add">+</button>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 mt-3 mt-sm-3 mt-md-0 mt-lg-0 mt-xl-0 show_bag_btn">
                        <?php if ($this->session->userdata('loged_in')) { ?>
                            <button class="btn addto_bag add_to_bag">Add to bag</button>
                        <?php } else { ?>
                            <a class="btn addto_bag signin_signup" href="javascript:void(0);" data-toggle="modal" data-target="#modal_login_resiter">Add to bag</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="wish_list">
                <label class="rating"><img src="<?php echo base_url() . '/assets/website/img/star_yellow.svg'; ?>" alt=""> <span><?php echo $product_details["product_rating"]; ?></span></label>
                <?php if ($this->session->userdata('loged_in')) { ?>    
                    <input class="d-none like_unlike" type="checkbox" id="heart_1" name="heart_1" <?php if ($product_details["is_favourite"] == 1) { ?> checked="checked" <?php } ?>>
                    <label class="like" for="heart_1"><i class="icon mdi mdi-heart"></i></label>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<section class="page_section prod_usr_ratings">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="main_title no_devide text-left">Customer <small>Reviews</small></h2>
            </div>
            <div class="col-4 text-right align-self-center">
                <?php if ($product_details["show_review_list"] == 1) { ?>
                    <a class="view_all" href="javascript:void(0);">View All</a>
                <?php } ?>
            </div>
            <div class="col-12 prod_ratings mt-4">
                <div class="row">
                    <?php
                    if (!empty($product_details["product_review_list"])) {
                        foreach ($product_details["product_review_list"] as $reviews) {
                            if ($reviews["profile_image"] != "") {
                                $profile_img = $reviews["profile_image"];
                            } else {
                                $profile_img = base_url() . 'assets/website/img/avtar.jpg';
                            }
                            ?>
                            <div class="col-12 usr_review">
                                <div class="card">
                                    <div class="media">
                                        <div class="review_left_box">
                                            <img class="mr-3 usr_avtar" src="<?php echo $profile_img; ?>" alt="">
                                        </div>
                                        <div class="media-body align-self-center">
                                            <div class="d-flex">
                                                <h5 class="mt-0 mb-0 float-left"><?php echo $reviews["name"]; ?></h5><label class="chips mt-0 ml-2 float-left"><img class="img-fluid" src="<?php echo base_url() . 'assets/website/img/star_yellow.svg'; ?>" alt=""> <?php echo $reviews["rating"]; ?></label>
                                            </div>
                                            <p class="desc mt-0 mb-0"><?php echo $reviews["review"]; ?></p>
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <?php if (!empty($product_details["product_review_list"])) { ?>
                        <div class="col-12 text-center mt-4 loadMore">
                            <a href="javascript:void(0);" class="btn btn-pink" id="loadMore">Load More</a>
                        </div>
                    <?php } ?>

                    <?php if (empty($product_details["product_review_list"])) { ?>
                        <div class="col-12 text-center mt-4 loadMore">
                            <span class="mt-0 mb-0 float-left">No reviews</span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="page_section bg-light-gray top_picks">
    <div class="container">
        <div class="row mb-4">
            <div class="col-8">
                <h2 class="main_title no_devide text-left">Similar <small>Drinks</small></h2>
            </div>
            <div class="col-4 text-right align-self-center">
                <a class="view_all" href="<?php echo base_url() . 'psd?pid=' . urlencode(base64_encode($product_details["product_id"])); ?>">View All</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="owl-carousel similar_drinks_carousel">
                    <?php foreach ($similar_products as $tpicks) { ?>
                        <div class="pt-2 pb-2">
                            <div class="card product_card">
                                <a title="<?php echo $tpicks["product_name"]; ?>" href="<?php echo base_url() . 'ppd?pid=' . urlencode(base64_encode($tpicks["product_id"])); ?>">
                                    <div class="product_img">
                                        <img src="<?php echo $tpicks["feature_img"]; ?>" class="img-fluid" alt="">
                                    </div>
                                    <div class="product_body text-center">
                                        <p class="price"><?php echo CURRENCY_CODE . $tpicks["price"]; ?> <small class="discount_price"><strike><?php echo CURRENCY_CODE . $tpicks["actual_price"]; ?></strike></small></p>
                                        <p class="title d-block text-truncate"><?php echo $tpicks["product_name"]; ?></p>
                                        <p class="sub_title"><?php echo $tpicks["seller"]["seller_name"]; ?></p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
$(".show_qty").hide();
$(".show_bag_btn").hide();

$(document).ready(function () {
    //check stock value with volume
    if($("#volume_id").val() != "") {
	var vol = $("#volume_id").val();
	var stock = $("#v_"+vol).data("stock");
	var price = $("#v_"+vol).data("price");
	var actual_price = $("#v_"+vol).data("actual_price");	

	if( parseInt(stock) > 0 ) {
	    $(".show_qty").show();
	    $(".show_bag_btn").show();
	    $("#instock").html("Instock");
	    $("#outstock").html("");
	}
	else {
	    $(".show_qty").hide();
	    $(".show_bag_btn").hide();
	    $("#outstock").html("Out Of Stock");
	    $("#instock").html("");
	}
    }    
    
    //show according to volumne
    $("#volume_id").change(function(){
	var vol = $(this).val();	
	var stock = $("#v_"+vol).data("stock");
	var price = $("#v_"+vol).data("price");
	var actual_price = $("#v_"+vol).data("actual_price");
	
	$("#price").html(price);
	$("#actual_price").html(actual_price);
	
	if( parseInt(stock) > 0 ) {
	    $(".show_qty").show();
	    $(".show_bag_btn").show();
	    $("#instock").html("Instock");
	    $("#outstock").html("");
	}
	else {
	    $(".show_qty").hide();
	    $(".show_bag_btn").hide();
	    $("#outstock").html("Out Of Stock");
	    $("#instock").html("");
	}
    });
    
    //Add to bag
    $(".add_to_bag").click(function () {
	var product_id = $("#product_id").val();
	var volume_id = $("#volume_id").val();
	var qty = $("#qty").val();

	$.ajax({
	    url: "<?php echo base_url() . 'products/add_to_bag'; ?>",
	    type: "post",
	    data: "product_id=" + product_id + "&volume_id=" + volume_id + "&qty=" + qty,
	    success: function (resp) {
		//console.log(resp);
		$("#modal_submit_reviews").find(".alert_msg").html("");		    
		var rep = $.parseJSON(resp);
//		console.log(rep);
		if (rep.status == 'true') {
		    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html(rep.response_msg);

		    $("#confirmed").click(function () {
			window.location.href = "<?php echo base_url() . 'cart'; ?>";
		    });

		} 
		else if (rep.status == 'false') {
		    $("a.modal_submit_reviewss").trigger("click");
		    $("#modal_submit_reviews").find(".alert_msg").html(rep.response_msg);

		} 
	    }
	});
    });

});

$(".like_unlike").click(function () {
    var product_id = $("#product_id").val();
    if ($(this).prop("checked") == true) {
	var fav_val = 1;
    } else if ($(this).prop("checked") == false) {
	var fav_val = 0;
    }

    $.ajax({
	url: "<?php echo base_url() . 'products/like_unlike'; ?>",
	type: "post",
	data: "fav_val=" + fav_val + "&product_id=" + product_id,
	success: function (resp)
	{}
    });
});

$(".volumes").change(function () {
    var price = $(this).find(':selected').data("price");
    //console.log(price);
    $(".change_price").html('');
    $(".change_price").html(price);
});

$('.btn-filter').click(function () {
    $(".filter_drop_menu").addClass("filter_menu_visible");
});

$(document).click(function (event) {
    if (!$(event.target).closest(".filter_drop_menu,.btn-filter").length) {
	$("body").find(".filter_drop_menu").removeClass("filter_menu_visible");
    }
});

$('.add').click(function () {
    var vol = $("#volume_id").val();	
    var min_stock = $("#v_"+vol).data("min_stock");
    
    if($(this).prev().val() < min_stock) {	
	if ($(this).prev().val() < 999) {
	    $(this).prev().val(+$(this).prev().val() + 1);
	}
    }
    else {
	$(this).attr("disabled", true);
    }
});

$('.sub').click(function () {
    if ($(this).next().val() > 1) {
	if ($(this).next().val() > 1)
	    $(this).next().val(+$(this).next().val() - 1);
    }
});

$(function () {
    $(".usr_review").slice(0, 2).show();
    $("#loadMore").on('click', function (e) {
	e.preventDefault();
	$(".usr_review:hidden").slice(0, 3).slideDown();
	if ($(".usr_review:hidden").length == 0) {
	    $("#load").fadeOut('slow');
	    $(".loadMore").hide();
	}
    });
});
</script>