<?php //echo "<pre>"; print_r($top_picks); exit; ?>
<section class="page_section bg-light-gray top_picks border-top">
    <div class="container">
	<div class="row mb-4">
	    <div class="col-12">
		<h2 class="main_title text-center">Top Picks <small>For you </small></h2>
	    </div>
	</div>
	<div class="row">
	    <div class="col-12">
		<div class="owl-carousel similar_drinks_carousel">
		    <?php foreach ($top_picks as $tpicks) { if($tpicks["isvolume"] == 1) { ?>                    
			<div class="pt-2 pb-2">
			    <div class="card product_card">
				<a title="<?php echo $tpicks["product_name"]; ?>" href="<?php echo base_url() . 'ppd?pid=' . urlencode(base64_encode($tpicks["product_id"])); ?>">
				    <div class="product_img">
					<img src="<?php echo $tpicks["feature_img_thumb"]; ?>" class="img-fluid" alt="">
				    </div>
				    <div class="product_body text-center">
					<p class="price"><?php echo CURRENCY_CODE . $tpicks["price"]; ?> <small class="discount_price"><strike><?php echo CURRENCY_CODE . $tpicks["volume"][0]["actual_price"]; ?></strike></small></p>
					<p class="title d-block text-truncate"><?php echo $tpicks["product_name"]; ?></p>
					<p class="sub_title"><?php echo $tpicks["seller"]["seller_name"]; ?></p>
				    </div>                                        
				</a>
			    </div>
			</div>
		    <?php  }} ?>
		</div>
	    </div>
	</div>
    </div>
</section>