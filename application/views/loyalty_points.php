<?php //echo "<pre>"; print_r($points); exit; ?>
<section class="page_section wallet mt-66 h-100vh">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-3">
                <h4 class="main_title mb-0">Loyalty Program</h4>
                <p class="c-pink mb-0 mt-4">You need <?php echo number_format($points["point_left"], 2); ?> points to become VIP member</p>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-3 mb-3">
                <div class="card p-3 wallet_main_card loyalty_point">
                    <p class="title">
                        <span class="spn-1">Loyalty</span><br>
                        <span class="spn-2">points</span>
                    </p>
                    <p class="total_amount mb-0 c-pink"><?php echo number_format($points["loyalty_point"], 2); ?></p>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-3 mb-3">
                <a href="<?php echo base_url() . 'loyalty_points/loyality_club?offset=0'; ?>">
                    <div class="card p-3 wallet_main_card loyalty_program">
                        <p class="title">
                            <span class="spn-1">Loyalty</span><br>
                            <span class="spn-2">Program</span>
                        </p>
                        <p class="total_amount mb-0 c-pink">Product List</p>
                    </div>
                </a>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 mt-3 mb-3">
                <a href="<?php echo base_url() . 'loyalty_points/vip_club?offset=0'; ?>">
                    <div class="card p-3 wallet_main_card vip_club">
                        <p class="title">
                            <span class="spn-1">VIP</span><br>
                            <span class="spn-2">Club</span>
                        </p>
                        <p class="total_amount mb-0 c-pink">Product List</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>