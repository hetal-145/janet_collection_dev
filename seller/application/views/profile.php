<?php //echo "<pre>"; print_r($profile_details); exit; ?>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places&key=AIzaSyDgn9Lquao1_1j91kZptSRRb59A37bgtZI"></script>
<script type="text/javascript">
    var geocoder = new google.maps.Geocoder();
    var lat = '21.285407';
    var lng = '39.237551';
    var placeSearch, autocomplete;
    var map, marker;
    var evnt;
    function geocodePosition(pos) {
        //var latLng = new google.maps.LatLng(23.2156, 72.6369);
        geocoder.geocode({
            latLng: pos
        }, function (responses) {
            if (responses && responses.length > 0) {
                updateMarkerAddress(responses[0].formatted_address);
            } else {
                //updateMarkerAddress('Cannot determine address at this location.');
                updateMarkerAddress('');
            }
        });
    }

    function updateMarkerStatus(str) {
        document.getElementById('markerStatus').innerHTML = str;
    }

    var second = 1;
    function updateMarkerPosition(latLng) {
        if (!second) {
            $('.latitude').val(latLng.lat());
            $('.longitude').val(latLng.lng());
        }
        second=0;
    }
    
    var first = 1;
    function updateMarkerAddress(str) {

        //document.getElementById('address').innerHTML = str;
        if (!first) {
            $('.address').val(str);
        }
        <?php if (!isset($profile_details['address'])) { ?>
                    $('.address').val(str);
        <?php } ?>
        first = 0;
    }

    function initialize() {


        var latLng = new google.maps.LatLng(lat, lng);
        map = new google.maps.Map(document.getElementById('mapCanvas'), {
            zoom: 12,
            center: latLng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoomControl: true
        });
        marker = new google.maps.Marker({
            position: latLng,
            title: 'Point A',
            map: map,
            draggable: true
        });

        //Update current position info.
        updateMarkerPosition(latLng);
        geocodePosition(latLng);

        // Add dragging event listeners.
        google.maps.event.addListener(marker, 'dragstart', function () {
            //updateMarkerAddress('Dragging...');
            updateMarkerAddress('');
        });

        google.maps.event.addListener(marker, 'drag', function () {
            //updateMarkerStatus('Dragging...');
            updateMarkerAddress('');
            updateMarkerPosition(marker.getPosition());
        });

        google.maps.event.addListener(marker, 'dragend', function () {
            //updateMarkerStatus('Drag ended');
            updateMarkerAddress('');
            geocodePosition(marker.getPosition());
        });

        autocomplete = new google.maps.places.Autocomplete(
                (document.getElementById('address')));
        // When the user selects an address from the dropdown,
        // populate the address fields in the form.
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            fillInAddress();
        });
    }

    function fillInAddress() {
        var place = autocomplete.getPlace().geometry.location.toString();

        place = place.replace("(", "");
        place = place.replace(")", "");
        place = place.split(',');
        var lat = place[0].trim();
        var lng = place[1].trim();

        $('.latitude').val(lat);
        $('.longitude').val(lng);
        var myCenter = new google.maps.LatLng(lat, lng);

        marker.setPosition(myCenter);
        marker.setMap(map);
        map.setCenter(myCenter);
    }



    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = new google.maps.LatLng(
                        position.coords.latitude, position.coords.longitude);
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }

// Onload handler to fire off the app.
    google.maps.event.addDomListener(window, 'load', initialize);
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Profile</h2>
        <ol class="breadcrumb">
            <li>
                <a href="home">Home</a>
            </li>
            <li class="active">
                <strong>Profile</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight col-sm-12">    
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>Profile</h5>

            <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <form name="frm_profile" class="frm_profile form-horizontal" action="#" method="post">
		<h4> Professional Details </h4><hr>
                <div class="panel-body">		    
		    <div class="row">    
			<div class="col-sm-1">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Seller Code </b></span>
                                <input type="text" name="code" class="form-control" value="<?php echo $profile_details["code"]; ?>" disabled="disabled">
                            </div>
                        </div>		    

                        <div class="col-sm-5">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Name </b></span>
                                <input type="text" name="seller_name" class="form-control seller_name" placeholder="Full Name" value="<?php echo $profile_details["seller_name"]; ?>">
                            </div>
                        </div>
			
			<div class="col-sm-6">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Company </b></span>
                                <input type="text" name="company_name" class="form-control company_name" placeholder="Company" value="<?php echo $profile_details["company_name"]; ?>">
                            </div>
                        </div> 
		    </div>
		    		
		    <div class="row">
			<div class="form-group col-sm-12">
			    <span class="help-block m-b-none"><b>Address </b></span>
			    <input type="text" class="form-control address" onFocus="geolocate()" name="address" value="<?php echo $profile_details["address"]; ?>" id="address">                            
			</div>

			<div class="form-group col-sm-3 latitude">
			    <span class="help-block m-b-none"><b>Latitude </b></span>
			    <input type="text" class="form-control latitude" name="latitude" value="<?php echo $profile_details["latitude"]; ?>" id="latitude">                            
			</div>

			<div class="form-group col-sm-3 longitude">
			    <span class="help-block m-b-none"><b>Longitude </b></span>
			    <input type="text" class="form-control longitude"  name="longitude" value="<?php echo $profile_details["longitude"]; ?>" id="longitude">                            
			</div>


			<div class="form-group col-sm-6 longitude" style="float: left; margin-top:-209px;">                                                        
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
		    
		    <br><h4> Personal Details </h4><hr>
		    
		    <div class="row">    
			<div class="col-sm-4">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>City </b></span>
                                <input type="text" name="city" class="form-control city" placeholder="City" value="<?php echo $profile_details["city"]; ?>">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Postal Code </b></span>
                                <input type="text" name="postalcode" class="form-control postalcode" placeholder="Postal Code" value="<?php echo $profile_details["postalcode"]; ?>">
                            </div>
                        </div>
			
			<div class="col-sm-4">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Date of Birth </b></span>
                                <input type="text" name="dob" class="form-control dob date_picker_seller" placeholder="d/m/y" value="<?php echo date("d/m/Y", strtotime($profile_details["dob"])); ?>">
                            </div>
                        </div>
                    </div>
		
		    <div class="row">   		    
                        <div class="col-sm-4">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Email </b></span>
                                <input type="email" name="email" class="form-control email" placeholder="Email" value="<?php echo $profile_details["email"]; ?>">
                            </div>
                        </div> 
			
			<div class="col-sm-4">
			    <div class="form-group">
				<span class="help-block m-b-none"><b>Time Zone </b></span>
				<select name="timezone" class="form-control" id="timezone">
				    <option value="">--Select Time Zone--</option>
				    <?php foreach($tz_list as $t) { ?>
					<option value="<?php echo $t['timestamp'] . ' - ' . $t['zone']; ?>" <?php if($t['zone'] == trim($profile_details["timezone"])) { ?> selected="selected" <?php } ?>>
					    <?php echo $t['diff_from_GMT'] . ' - ' . $t['zone']; ?>
					</option>
				    <?php } ?>
				</select>
			    </div>
			</div>
			
			<div class="col-sm-4">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Gender </b></span>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="1" <?php if($profile_details["gender"] == 1) { ?> selected="selected" <?php } ?>>Male</option>
                                    <option value="2" <?php if($profile_details["gender"] == 2) { ?> selected="selected" <?php } ?>>Female</option>
                                </select>
                            </div>
                        </div>
                    </div>
		    
		    <div class="row">                        
			<div class="col-sm-2">
			    <div class="form-group">
				<span class="help-block m-b-none"><b>Country Code </b></span>
				<select name="country_code" id="country_code" class="form-control">
				    <option value="">--Select--</option>
				    <?php foreach ($code as $c){ ?>
				    <option value="<?php echo $c["code"]; ?>" <?php if($c["code"] == $profile_details["country_code"]) { ?> selected="selected" <?php } ?>><?php echo $c["code"]; ?></option>
				    <?php } ?>
				</select>
			    </div>
			</div>

			<div class="col-sm-3">
			    <div class="form-group">
				<span class="help-block m-b-none"><b>Mobile No </b></span>
				<input type="text" name="contact_no" class="form-control numeric contact_no" placeholder="Mobile No" value="<?php echo $profile_details["contact_no"]; ?>">
			    </div>
			</div> 
			
			<div class="col-sm-3">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Online / Offline Status </b></span>
                                <select name="is_online" id="is_online" class="form-control">
                                    <option value="0" <?php if($profile_details["is_online"] == 0) { ?> selected="selected" <?php } ?>>OFFLINE</option>
                                    <option value="1" <?php if($profile_details["is_online"] == 1) { ?> selected="selected" <?php } ?>>ONLINE</option>
                                </select>
                            </div>
                        </div>
			
			<div class="col-sm-3">
                            <div class="form-group">
                                <span class="help-block m-b-none"><b>Notification Status </b></span>
                                <select name="notification_status" id="notification_status" class="form-control">
                                    <option value="0" <?php if($profile_details["notification_status"] == 0) { ?> selected="selected" <?php } ?>>No</option>
                                    <option value="1" <?php if($profile_details["notification_status"] == 1) { ?> selected="selected" <?php } ?>>Yes</option>
                                </select>
                            </div>
                        </div>
			
			<div class="col-sm-6">
			    <div class="form-group">
				<span class="help-block m-b-none pull-left"><b>Delivery Zone </b></span>
				<select name="dzone_id" class="form-control dzone_id">
				    <option value="">--Select--</option>
				    <?php foreach($delivery_zone as $dzone) { ?>
				    <option value="<?php echo $dzone["dzone_id"]; ?>" <?php if($dzone["dzone_id"] == $profile_details["dzone_id"]){ ?> selected="selected" <?php } ?>><?php echo $dzone["city"]; ?></option>
				    <?php } ?>

				</select>
			    </div>
			</div>
			
			<div class="col-sm-6">
			    <div class="form-group">
				<span class="help-block m-b-none"><b>Whom you want to manage delivery drivers? </b></span>
				<select name="delivery_by" class="form-control delivery_by">
				    <option value="">--Select--</option>
				    <option value="1" <?php if(1 == $profile_details["delivery_by"]){ ?> selected="selected" <?php } ?>>By DeliverIn</option>
				    <option value="2" <?php if(2 == $profile_details["delivery_by"]){ ?> selected="selected" <?php } ?>>By Stuart</option>
				</select>
			    </div>
			</div>
                    </div>
                    
		    <br><h4> Bank Details </h4><hr>
		
		    <div class="row">			
			<div class="col-sm-4">
			    <div class="form-group">
				<span class="help-block m-b-none"><b>Bank Name </b></span>
				<input type="text" name="bank_name" class="form-control bank_name" placeholder="Bank Name" value="<?php echo $profile_details["bank_name"]; ?>">
			    </div>
			</div>
			<div class="col-sm-4">
			    <div class="form-group">
				<span class="help-block m-b-none"><b>Account Number </b></span>
				<input type="text" name="account_number" class="form-control account_number" placeholder="Account Number" value="<?php echo $profile_details["account_number"]; ?>">
			    </div>
			</div>
			<div class="col-sm-4">
			    <div class="form-group">
				<span class="help-block m-b-none"><b>Sort Code </b></span>
				<input type="text" name="routing_no" class="form-control routing_no" placeholder="Sort Code" value="<?php echo $profile_details["routing_no"]; ?>">
			    </div>
			</div>
		    </div>                    
		</div>
		
		<div class="panel-body">
                    <a class="btn submit_btn btn-primary pull-right">Save Profile</a>
                    <span class="success_msg pull-right" style="color:green; padding: 7px;"></span>
                </div>
            </form>
        </div>            
    </div>        
</div>

<script>
    $(".latitude").hide();
    $(".longitude").hide();
    
    $(document).ready(function () {       
            
        $('.submit_btn').click(function (e) {

            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_profile"]');

            var seller_name = frm.find('[name="seller_name"]').val();
            if (!seller_name || !seller_name.trim()) {
                frm.find('[name="seller_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your full name!'));
                valid = false;
            }
            
            var address = frm.find('[name="address"]').val();
            if (!address || !address.trim()) {
                frm.find('[name="address"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your address!'));
                valid = false;
            }
	    
	    var city = frm.find('[name="city"]').val();
            if (!city || !city.trim()) {
                frm.find('[name="city"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your city!'));
                valid = false;
            }
	    
	    var postalcode = frm.find('[name="postalcode"]').val();
            if (!postalcode || !postalcode.trim()) {
                frm.find('[name="postalcode"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your postal code!'));
                valid = false;
            }
	    
	    var dob = frm.find('[name="dob"]').val();
            if (!dob || !dob.trim()) {
                frm.find('[name="dob"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your date of birth!'));
                valid = false;
            }
	    
	    var timezone = frm.find('[name = "timezone"]').val();
            if (!timezone || !timezone.trim()) {
                frm.find('[name = "timezone"]').addClass('input_error').parents('.form-group').append(error_msg('Please select your timezone.'));
                valid = false;
            }
            
            var email = frm.find('[name="email"]').val();
            if (!email || !email.trim()) {
                frm.find('[name="email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter email!'));
                valid = false;
            }
            
            var country_code = frm.find('[name="country_code"]').val();
            if (!country_code || !country_code.trim()) {
                frm.find('[name="country_code"]').addClass('input_error').parents('.form-group').append(error_msg('Please select country code!'));
                valid = false;
            }
            
            var contact_no = frm.find('[name="contact_no"]').val();
            if (!contact_no || !contact_no.trim()) {
                frm.find('[name="contact_no"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter mobile no!'));
                valid = false;
            }

            if (valid) {
                $(this).html('Processing...');
                var datastring = $("form[name=\"frm_profile\"]").serialize();
                $.ajax({
                    url: 'profile/update_profile',
                    data: datastring,
                    type: 'post',
                    success: function (data) {
                        //console.log(data);
                        $('.submit_btn').html('Save Profile');
                        if (data == 'success') {
                            $('.success_msg').html('Your profile is successfully saved');
                        } 
                    }
                });
            }
        });

    });
</script>

<script>
$(document).ready(function () {
    if ($(".date_picker_seller").length) {	
	$('.date_picker_seller').attr('type', 'text');
	$('.date_picker_seller').datepicker({
	    startView: 3,
	    todayBtn: "linked",
	    keyboardNavigation: false,
	    forceParse: false,
	    autoclose: true,
	    format: "dd/mm/yyyy"
	});	
    }
});
</script>
