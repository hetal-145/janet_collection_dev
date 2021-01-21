<?php //echo "<pre>"; print_r($alcohol_awareness); exit; ?>
<section class="page_section product_list mt-66">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="main_title">Alcohol Awareness</h1>
            </div>
        </div>
        <div class="row">
            <?php if(!empty($alcohol_awareness)) { ?>
                <?php foreach($alcohol_awareness as $aa) { ?>
                <div class="col-6 col-sm-6 col-md-4 col-lg-3 col-xl-3 mb-2 mt-2">
                    <div class="card product_card">
                        <a href="<?php echo base_url() . 'home/alcohol_awareness_detail?aid='. base64_encode($aa["aid"]); ?>">
                            <div class="product_img">
				<img src="<?php if(!empty($aa["image"])) { echo S3_PATH . 'alcohol_awareness/' . $aa["image"]; } else { echo ''; } ?>" class="img-fluid" alt="">
                            </div>
                            <div class="product_body text-center">
                                <p class="title"><?php echo $aa["title"]; ?></p>
				
				<?php 
				    $word_count = str_word_count($aa["description"]);
				    $limit = 50;
				    if ($word_count <= $limit) {
					echo $aa["description"];
				    } else {
					$words = str_word_count($aa["description"], 2);
					$pos = array_keys($words);
					echo $aa["description"] = substr($aa["description"], 0, $pos[$limit]) . '...<a href="javascript:void(0);" class="read_more_content">...</a>';
				    }
				?>
                                
                            </div>
                        </a>
                    </div>
                </div>
                <?php } } else { ?>
                    <?php redirect(base_url('home/no_data')); ?>
                <?php } ?>            
            </div>
        </div>
    </div>
</section>