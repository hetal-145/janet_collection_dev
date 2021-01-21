<div class="modal mdl_gift_card fade" id="mdl_gift_card" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <div class="row mb-8">
                    <div class="col-10 col-sm-10 col-md-9 col-lg-9 col-xl-9">
                        <h4 class="mb-0">Select Gift Card</h4>
                    </div>
                    <div class="col-2 col-sm-2 col-md-3 col-lg-3 col-xl-3">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="col-12">
                        <p class="desc mb-3 mt-2"></p>
                    </div>
                </div>
                <div class="row address_card_select">
                    <?php
                    if (!empty($get_gift_card)) {
			//print_r($get_gift_card); exit;
                        foreach ($get_gift_card as $detail) {
                            ?>
                            <div class="col-12 mb-3">
                                <div class="card address_card p-3 remove_add_<?php echo $detail["card_id"]; ?>">
                                    <div class="row mb-2">
                                        <div class="col-8 align-self-center">
                                            <h5 class="c-pink mb-0"><?php echo $detail["code"]; ?></h5>
                                        </div>
                                        <div class="col-4 text-right">
                                            <div class="pretty p-icon p-round p-smooth mt-0">
                                                <input type="radio" value="<?php echo $detail["card_id"]; ?>" class="update_gift_card" name="select_add_gc" />
                                                <div class="state p-success">
                                                    <i class="icon mdi mdi-check"></i>
                                                    <label> </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="desc">Sender Name: <?php echo $detail["sender_name"]; ?></p>
                                    <p class="desc mb-0">Remaining Amount: <?php echo $detail["currency"].$detail["remaining_amount"]; ?></p>
				    <p class="desc mb-0">Expiry Date: <?php echo date('d F, Y', strtotime($detail["expiry_date"])); ?></p>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>