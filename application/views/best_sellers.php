<?php //echo "<pre>"; print_r($best_selling_product); exit; ?>
<section class="page_section bg_right_img">
    <div class="container">
	<div class="row mb-4">
	    <div class="col-12 text-center" style="z-index: 1;">
		<h2 class="main_title">Best Selling</h2>
	    </div>
	</div>
	<div class="row">
	    <div class="col-12">
		<div class="owl-carousel top_picks_carousel">
		    <?php foreach ($best_selling_product as $bestsell) { if($bestsell["isvolume"] == 1) { ?>
			<div class="pt-2 pb-2">
			    <div class="card product_card">
				<a title="<?php echo $bestsell["product_name"]; ?>" href="<?php echo base_url() . 'ppd?pid=' . urlencode(base64_encode($bestsell["product_id"])); ?>">
				    <div class="product_img">
					<img src="<?php echo $bestsell["feature_img"]; ?>" class="img-fluid" alt="">
				    </div>
				    <div class="product_body text-center">
					<p class="price"><?php echo CURRENCY_CODE . $bestsell["price"]; ?> <small class="discount_price"><strike><?php echo CURRENCY_CODE . $bestsell["volume"][0]["actual_price"]; ?></strike></small></p>
					<p class="title d-block text-truncate"><?php echo $bestsell["product_name"]; ?></p>
					<p class="sub_title"><?php echo $bestsell["seller"]["seller_name"]; ?></p>
				    </div>
				</a>
				<div class="wish_list">
				    <?php if ($this->session->userdata('loged_in')) { ?>    
				    <input class="d-none like_unlike" type="checkbox" id="heart_1_<?php echo $bestsell["product_id"]; ?>" name="heart_1_<?php echo $bestsell["product_id"]; ?>" data-prid="<?php echo $bestsell["product_id"]; ?>" <?php if($bestsell["is_favourite"] == 1){ ?> checked="checked" <?php } ?>>
				    <label class="like_unlike_click" for="heart_1_<?php echo $bestsell["product_id"]; ?>"><i class="icon mdi mdi-heart"></i></label>
				    <?php } ?>
				</div>
			    </div>
			</div>
		    <?php }} ?>
		</div>
	    </div>
	</div>
    </div>
</section>