<?php //echo "<pre>"; print_r($product_details); echo "</pre>"; exit; ?>
<section class="product_detail">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-7 content-left text-center">
                <div class="prod_img_box">
                    <img src="<?php echo $product_details["feature_img"]; ?>" class="img-fluid" alt="">
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-5 content-right align-self-center ">
                <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_details["product_id"]; ?>">
                <h2 class="prod_title mb-3"><?php echo $product_details["product_name"]; ?></h2>
                <p class="prod_by_admin"><?php echo $product_details["product_name"]; ?> sold by <?php echo $product_details["seller"]["seller_name"]; ?> <label class="chips"><img class="img-fluid" src="<?php echo base_url(). 'assets/website/img/star_gray.svg'; ?>" alt=""> <?php echo $product_details["product_rating"]; ?> <span class="c-pink">(<?php echo $product_details["total_product_reviews"]; ?> Reviews)</span></label></p>
                <div class="row mb-3">
                    <div class="col-4 align-self-center">
                        <p class="prod_price mb-0 change_price"><?php echo $product_details["currency"].$product_details["price"]; ?></p>
                    </div>
                    <div class="col-8 text-right">
                        <label class="unit_box"><?php echo $product_details["abv_percent"]; ?>% ABV</label>
                        <label class="unit_box"><?php echo $product_details["alchol_units"]; ?> Unit</label>
                    </div>
                </div>
                <p class="desc mb-3"><?php echo $product_details["description"]; ?></p>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="p-4 bg-light-gray mb-3">
                            <div class="input-group mb-2">
                                <div class="input-group-append">
                                    <span class="input-group-text border-right-0 pr-0 bg-white fz-18"><i class="mdi mdi-map-marker"></i></span>
                                </div>
                                <input type="text" class="form-control border-left-0" placeholder="Enter Your Street Address">
                                <div class="input-group-prepend">
                                    <button class="btn btn-pink" type="button">View Price</button>
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="pretty p-image p-plain">
                                    <input type="radio" name="schd_time" checked/>
                                    <div class="state">
                                        <img class="image" src="<?php echo base_url(). 'assets/website/img/check.png'; ?>">
                                        <label>Today</label>
                                    </div>
                                </div>
                                <div class="pretty p-image p-plain ml-2">
                                    <input type="radio" name="schd_time"/>
                                    <div class="state">
                                        <img class="image" src="<?php echo base_url(). 'assets/website/img/check.png'; ?>">
                                        <label>Tomorrow</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-0">
                                <div class="time_sched">
                                    <input type="radio" class="d-none" id="sched_time1" name="sched_time">
                                    <label for="sched_time1" class="time_sched_lbl">07:00 to 08:00</label>
                                </div>
                                <div class="time_sched">
                                    <input type="radio" class="d-none" id="sched_time2" name="sched_time">
                                    <label for="sched_time2" class="time_sched_lbl">09:00 to 10:00</label>
                                </div>
                                <div class="time_sched">
                                    <input type="radio" class="d-none" id="sched_time3" name="sched_time">
                                    <label for="sched_time3" class="time_sched_lbl">11:00 to 12:00</label>
                                </div>
                                <div class="time_sched">
                                    <input type="radio" class="d-none" id="sched_time4" name="sched_time">
                                    <label for="sched_time4" class="time_sched_lbl">01:00 to 02:00</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <select class="form-control volumes" name="volume_id">
                            <?php foreach($product_details["volume_id"] as $key => $vol) { ?>
                                <option value="<?php echo $vol["volume_id"]; ?>" data-price="<?php echo $product_details["currency"].$vol["actual_price"]; ?>"><?php echo $vol["volumes"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <div class="input-group qnty">
                            <button type="button" id="sub" class="btn btn-outline-secondary sub">-</button>
                            <input name="qty" class="form-control text-center" type="text" id="1" value="1" min="1" maxlength="3" disabled/>
                            <button type="button" id="add" class="btn btn-outline-secondary add">+</button>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn addto_bag">Add to bag</button>
                    </div>
                </div>
            </div>
            <div class="wish_list">
                <label class="rating"><img src="<?php echo base_url(). 'assets/website/img/star_yellow.svg'; ?>" alt=""> <span><?php echo $product_details["product_rating"]; ?></span></label>
                <?php if ($this->session->userdata('loged_in')) { ?>    
                <input class="d-none like_unlike" type="checkbox" id="heart_1" name="heart_1" <?php if($product_details["is_favourite"] == 1){ ?> checked="checked" <?php } ?>>
                    <label class="like" for="heart_1"><i class="icon mdi mdi-heart"></i></label>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<section class="page_section prod_usr_ratings">
    <div class="container">
        <div class="row">
            <div class="col-8">
                <h2 class="main_title">Customer <small>Reviews</small></h2>
            </div>
            <div class="col-4 text-right align-self-center d-none">
                <?php if($product_details["show_review_list"] == 1) { ?>
                    <a class="view_all" href="javascript:void(0);">View All</a>
                <?php } ?>
            </div>
            <div class="col-12 prod_ratings mt-4">
                <div class="row">
                    <?php 
                    foreach($product_details["product_review_list"] as $reviews) { 
                        if( $reviews["profile_image"] != "" ) {
                            $profile_img = $reviews["profile_image"];
                        }
                        else {
                            $profile_img = base_url(). 'assets/website/img/avtar.jpg';
                        }
                    ?>
                        <div class="col-12 usr_review">
                            <div class="card">
                                <div class="media">
                                    <div class="review_left_box">
                                        <img class="mr-3 usr_avtar" src="<?php echo $profile_img; ?>" alt="">
                                    </div>
                                    <div class="media-body">
                                        <div class="d-flex">
                                            <h5 class="mt-0 mb-0 float-left"><?php echo $reviews["name"]; ?></h5><label class="chips mt-0 ml-2 float-left"><img class="img-fluid" src="<?php echo base_url(). 'assets/website/img/star_yellow.svg'; ?>" alt=""> <?php echo $reviews["rating"]; ?></label>
                                        </div>
                                        <p class="desc mt-0 mb-0"><?php echo $reviews["review"]; ?></p>
                                    </div>
                                </div>                            
                            </div>
                        </div>
                    <?php } ?>
                    
                    <div class="col-12 text-center mt-4 loadMore">
                        <a href="javascript:void(0);" class="btn btn-pink" id="loadMore">Load More</a>
                    </div>
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
                <a class="view_all" href="<?php echo base_url() . 'products/similar_drinks?pid=' . base64_encode($product_details["product_id"]); ?>">View All</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="owl-carousel similar_drinks_carousel">
                    <?php foreach ($similar_products as $tpicks) { ?>
                        <div class="pt-2 pb-2">
                            <div class="card product_card">
                                <a href="<?php echo base_url() . 'products/product_detail?pid=' . base64_encode($tpicks["product_id"]); ?>">
                                    <div class="product_img">
                                        <img src="<?php echo $tpicks["feature_img"]; ?>"" class="img-fluid" alt="">
                                    </div>
                                    <div class="product_body text-center">
                                        <p class="price"><?php echo $tpicks["currency"] . $tpicks["price"]; ?></p>
                                        <p class="title"><?php echo $tpicks["product_name"]; ?></p>
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
    $(".like_unlike").click(function() {
        var product_id = $("#product_id").val();
        if($(this).prop("checked") == true){
            var fav_val = 1;
        }
        else if($(this).prop("checked") == false){
            var fav_val = 0;
        }
            
        $.ajax({
            url: "products/like_unlike",
            type: "post",
            data: "fav_val="+fav_val+"&product_id="+product_id,
            success: function (resp)
            {
                console.log(resp);
            }
        });
    });
    
    $(".volumes").change(function() {
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
        if ($(this).prev().val() < 999) {
            $(this).prev().val(+$(this).prev().val() + 1);
        }
    });
    $('.sub').click(function () {
        if ($(this).next().val() > 1) {
            if ($(this).next().val() > 1)
                $(this).next().val(+$(this).next().val() - 1);
        }
    });
</script>
<script type="text/javascript">
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