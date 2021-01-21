<?php // echo "<pre>"; print_r($order_return); exit; ?>
<section class="page_section wallet mt-66 h-100vh">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                <h4 class="main_title mb-0">Wallet</h4>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-3 mb-3">
                <div class="card p-3 wallet_main_card">
                    <p class="title">
                        <span class="spn-1">available</span><br>
                        <span class="spn-2">balance</span>
                    </p>
                    <p class="total_amount mb-0 c-pink"><?php echo CURRENCY_CODE.number_format((float)$wallet_balance, 2); ?></p>
                </div>
            </div>
            
            <?php if(!empty($order_return)) { foreach($order_return as $key => $value) { ?>
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-3 mb-3">
                <div class="card p-3 wallet_status_card">
                    <div class="row">
                        <div class="col-6">
                            <p class="sts c-pink"><strong>Order Return</strong><br><label class="desc mb-0"><?php echo date("d M, Y", strtotime($value["update_date"])); ?></label></p>
                        </div>
                        <div class="col-6 text-right align-self-center">
                            <p class="total_amount"><strong><?php echo CURRENCY_CODE.number_format($value["amount_refunded"], 2); ?></strong></p>
                        </div>
                        <div class="col-12">
                            <p class="text-uppercase mb-0">Order: <?php echo $value["order_no"]; ?></p>
                        </div>
                        <div class="col-12">
                            <p class="desc mb-0 text-capitalize"><?php echo $value["product_name"]; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php }} ?>
        </div>
    </div>
</section>