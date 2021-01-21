<?php //echo "<pre>"; print_r($categories); exit;   ?>
<style type="text/css">
    .categories_card .product_img{background-size: 100%;}
    .categories_card .categories_body{min-height: inherit;}
</style>
<section class="page_section mt-66 product_list h-100vh">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                <h1 class="main_title">Categories - <?php echo $main_category; ?></h1>
            </div>
        </div>
        <div class="row">
            <?php foreach ($categories as $category) { ?>
                <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 mb-3 mt-3">
                    <div class="pt-2 pb-2">
                        <div class="card categories_card">
                            <div class="product_img">
                                <img src="<?php echo $category["category_img"]; ?>" class="img-fluid" alt="">
                            </div>
                            <div class="categories_body p-3 text-center">
                                <p class="title"><a href="<?php echo base_url() . 'ppl?cid='. urlencode(base64_encode($category["category_id"])); ?>" class="c-pink"><?php echo $category["category_name"]; ?></a></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>