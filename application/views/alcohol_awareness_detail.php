<?php //echo "<pre>"; print_r($alcohol_awareness); echo "</pre>"; ?>
<section class="product_detail mt-66 prod_det">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 content-left text-center">
                <div class="prod_img_box">
		    <img src="<?php if(!empty($alcohol_awareness["image"])) { echo S3_PATH . 'alcohol_awareness/' . $alcohol_awareness["image"]; } else { echo ""; } ?>" class="img-fluid" alt="">
                </div>
            </div>            
        </div>
    </div>
</section>

<section class="page_section prod_usr_ratings">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="main_title no_devide text-left"><?php echo $alcohol_awareness["title"]; ?></h2>
            </div>
            
            <div class="col-12">
                <?php echo $alcohol_awareness["description"]; ?>
            </div>
        </div>
    </div>
</section>
