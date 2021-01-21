<div class="modal fade" id="mdl_repeat_delivery" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-10 col-sm-10 col-md-9 col-lg-9 col-xl-9">
                        <h4 class="mb-0">Repeat Delivery</h4>
                    </div>
                    <div class="col-2 col-sm-2 col-md-3 col-lg-3 col-xl-3">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
			<?php if(!empty($schedule_list)) { foreach($schedule_list as $list) { ?>
			    <div class="form-group rep_del_opt_box">
				<input class="form-control d-none" id="repeat_order_on_<?php echo $list["schedule_order_list_id"]; ?>" value="<?php echo $list["schedule_order_list_id"]; ?>" name="repeat_order_on" type="radio">
				<label class="select_radio repeat_order_on_<?php echo $list["schedule_order_list_id"]; ?>" for="rep_del_opt_<?php echo $list["schedule_order_list_id"]; ?>"><?php echo $list["schedule_on_title"]; ?></label>
			    </div>
                        <?php } } else { ?>
			    <div class="col-12 text-center mt-66">
				<label class="display_label">No Schedule List</label>
			    </div>
			<?php } ?>
                    </div>
                    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12 mt-4">
                        <button type="button" class="btn btn-pink_squre" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>