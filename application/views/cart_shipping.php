<div class="modal fade" id="mdl_apply_now" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content ">
            <div class="modal-body">
                <div class="row mb-8">
                    <div class="col-10 col-sm-10 col-md-9 col-lg-9 col-xl-9">
                        <h4 class="mb-0">Select Shipping Address</h4>
                    </div>
                    <div class="col-2 col-sm-2 col-md-3 col-lg-3 col-xl-3">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                        <p class="desc mb-3 mt-2"></p>
                    </div>
                </div>
                <div class="row address_card_select">
                    <?php
                    if (!empty($shipping_details)) {
                        foreach ($shipping_details as $detail) {
                            ?>
                            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12 mb-3">
                                <div class="card address_card p-3 remove_add_<?php echo $detail["shipping_id"]; ?>">
                                    <div class="row mb-2">
                                        <div class="col-8 col-sm-8 col-lg-8 col-md-8 col-xl-8 align-self-center">
                                            <h5 class="c-pink mb-0"><?php echo $detail["name"]; ?></h5>
                                        </div>
                                        <div class="col-4 col-sm-4 col-lg-4 col-md-4 col-xl-4 text-right">
                                            <div class="pretty p-icon p-round p-smooth mt-0">
                                                <input type="radio" value="<?php echo $detail["shipping_id"]; ?>" class="update_current_address" name="select_add" <?php if ($detail["isaddress"] == 1) { ?> checked <?php } ?>/>
                                                <div class="state p-success">
                                                    <i class="icon mdi mdi-check"></i>
                                                    <label> </label>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary delete_btn delete_shipping_btn ml-2" data-shipping_id="<?php echo $detail["shipping_id"]; ?>"><i class="mdi mdi-delete"></i></button>
                                        </div>
                                    </div>
                                    <p class="address desc"><?php echo $detail["address"] . ', ' . $detail["zipcode"]; ?></p>
                                    <p class="mobile_num desc mb-0"><?php echo $detail["contactno"]; ?></p>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <div class="col-12">
			<button type="button" class="btn btn-pink_squre Add_new_btn">Add New</button>
		    </div>
                </div>
		<form name="add_new_address" action="#" method="post" class="add_new_address" id="add_new_address">
		    <div class="Add_new_address">
			<div class="row">
			    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
				<div class="form-group">
				    <input class="form-control" name="name" type="text" placeholder="Name">
				</div>
			    </div>
			    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
				<div class="form-group">
				    <input class="form-control numeric" name="contactno" type="text" placeholder="Mobile Number">
				</div>
			    </div>
			    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
				<div class="row">
				    <div class="form-group col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
					<span class="help-block m-b-none"><b>Address</b>&nbsp;
					<input type="text" class="form-control address" onFocus="geolocate()" name="address" value="" id="address">                            
				    </div>
<!--				    onFocus="geolocate()" -->

				    <div class="form-group col-12 col-sm-12 col-lg-3 col-md-3 col-xl-3 latitude">
					<span class="help-block m-b-none"><b>Latitude </b></span>
					<input type="text" class="form-control latitude" name="latitude" value="" id="latitude">                            
				    </div>

				    <div class="form-group col-12 col-sm-12 col-lg-3 col-md-3 col-xl-3 longitude">
					<span class="help-block m-b-none"><b>Longitude </b></span>
					<input type="text" class="form-control longitude"  name="longitude" value="" id="longitude">                            
				    </div>


				    <div class="form-group col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12 longitude">                                                        
					<div id="mapCanvas"></div>
					<div id="infoPanel" style="display: none;">
					    <b>Marker status:</b>
					    <div id="markerStatus"><i>Click and drag the marker.</i></div>
					    <b>Current position:</b>
					    <div id="info"></div>
					    <b>Closest matching address:</b>
					    <div id="address"></div>
					</div>
				    </div> 
				</div>     
			    </div>
			    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
				<div class="form-group">
				    <input class="form-control" name="zipcode" type="text" placeholder="Post Code">
				</div>
			    </div>
			    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
				<button type="button" class="btn btn-pink_squre save_address_btn">Save</button>
				<div class="error_shipping" style="color:red;"></div>
				<div class="success_shipping" style="color:green;"></div>
			    </div>
			</div>
		    </div>
		</form>
            </div>
        </div>
    </div>
</div>