<section class="page_section prod_usr_ratings mt-66 h-100vh">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                <h2 class="main_title">Licensed Retailers</h2>
            </div>
            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12 prod_ratings mt-4">
                <div class="row" id="notify_list">
                    <?php $i=1; if(!empty($content)) {  foreach($content as $seller) { ?>
                   
                        <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                            <div class="card mt-2">
                                <div class="media">
                                    <div class="review_left_box">
                                    </div>
                                    <div class="media-body">
                                        <p class="desc mt-0 mb-0"><strong>Seller Name: </strong><?php echo $seller["seller_name"]; ?></p>
					 <p class="desc mt-0 mb-0"><strong>Seller Company Name: </strong><?php echo $seller["company_name"]; ?></p>
                                    </div>
                                </div>                            
                            </div>
                        </div>
                    <?php $i++; }} ?>                    
                </div>
            </div>
        </div>
    </div>
</section>