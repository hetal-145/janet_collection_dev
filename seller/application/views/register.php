<!DOCTYPE html>
<html>
    <head>
        <base href="<?= site_url() ?>" />
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Seller Login</title>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo.png"/>
        <script src="assets/js/jquery-2.1.1.js"></script>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" src="assets/css/plugins/datapicker/datepicker3.css">
        <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="assets/css/animate.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
        <link href="assets/css/style.css" rel="stylesheet">
	<script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/plugins/datapicker/bootstrap-datepicker.js"></script>
	<script src="assets/js/jquery-ui-1.10.4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
        <style>
            .gray-bg {
                background-color: #01aeec;
            }
            .btn-primary {
                background-color: #f44336;
                border-color: #1ab394;
                color: #FFFFFF;
            }
            .error {
                color:#f00;
                float: left;
            }
            
            .large-box {
                max-width: 800px;
                z-index: 100;
                margin: 0 auto;
                padding-top: 40px;
            }            
            .radio_label{
                margin-right: 20px;
                font-size: 20px;
                padding-left: 20px;
            }
            
            .radio_span{
                padding-left: 20px;
            }
        </style>   
	
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
    </head>

    <body class="gray-bg" style="background-color: #f3f3f4;"> 
        <div class="text-center animated fadeInDown">
            <img alt="image" class="img-circle" src="../assets/logo.png"  height="100px"width="100px" />
            <h2><b style="">Janet-Collection - Seller Registration Form</b></h2>
            <form class="m-t large-box " name="seller_register" role="form" method="post" action="#">
                <span class="text-left"><strong><h4>Professional Details</h4></strong></span><hr>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Seller Full Name (*)</b></span>
                            <input type="text" name="seller_name" class="form-control" placeholder="Seller Name">
                        </div>
                    </div>
                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Registered Company Name</b></span>
                            <input type="text" name="company_name" class="form-control" placeholder="Registered Company Name">
                        </div>
                    </div>
		</div>
		
		<div class="row">
		    <div class="form-group col-sm-12">
			<span class="help-block m-b-none" style="float:left;"><b>Address (*)</b>&nbsp;
                                <b><span title="Companies Registered Address">(?)</span></b></span>
			<input type="text" class="form-control address" onFocus="geolocate()" name="address" value="" id="address">                            
		    </div>

		    <div class="form-group col-sm-3 latitude">
			<span class="help-block m-b-none"><b>Latitude (*)</b></span>
			<input type="text" class="form-control latitude" name="latitude" value="" id="latitude">                            
		    </div>

		    <div class="form-group col-sm-3 longitude">
			<span class="help-block m-b-none"><b>Longitude (*)</b></span>
			<input type="text" class="form-control longitude"  name="longitude" value="" id="longitude">                            
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
                
                <span class="text-left"><strong><h4>Personal Details</h4></strong></span><hr>
                <div class="row">
		    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>City (*)</b></span>
                            <input type="text" maxlength="150" name="city" class="form-control" placeholder="City">
                        </div>
                    </div>
		    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Postal Code (*)</b></span>
                            <input type="text" maxlength="10" name="postalcode" class="form-control" placeholder="Postal Code">
                        </div>
                    </div>
		    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Date Of Birth (*)</b></span>
                            <input type="text" name="dob" autocomplete="off" class="form-control date_picker" placeholder="Date Of Birth">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Email (*)</b></span>
                            <input type="email" name="email" class="form-control" placeholder="Email">
                        </div>
                    </div>
		    <div class="col-sm-6">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left" style="width: 100%; text-align: left;"><b>Time Zone (*)</b></span>
			    <select name="timezone" class="form-control" id="timezone">
				    <option value="">--Select Time Zone--</option>
				<?php foreach($tz_list as $t) { ?>
				    <option value="<?php echo $t['timestamp'] . ' - ' . $t['zone']; ?>">
					<?php echo $t['diff_from_GMT'] . ' - ' . $t['zone']; ?>
				    </option>
				<?php } ?>
			    </select>
                        </div>
                    </div>
<!--                    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Gender</b></span>
                            <label class="radio_label"><input value="1" type="radio" name="gender" style="margin-left: -64px;"><span class="radio_span">Male</span></label>
                            <br><label class="radio_label"><input value="2" type="radio" name="gender"><span class="radio_span">Female</span></label>
                        </div>
                    </div>-->
                    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Password (*)</b>&nbsp;
                                <b><span title="You can change this later">(?)</span></b></span>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Confirm Password (*)</b></span>
                            <input type="password" name="cnf_password" class="form-control" placeholder="Confirm Password">
                        </div>
                    </div>
		    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Delivery Zone (*)</b></span>
                            <select name="dzone_id" class="form-control dzone_id">
                                <option value="">--Select--</option>
                                <?php foreach($delivery_zone as $dzone) { ?>
                                <option value="<?php echo $dzone["dzone_id"]; ?>"><?php echo $dzone["city"]; ?></option>
                                <?php } ?>
                                
                            </select>
                        </div>
                    </div>
                </div>
                
                <span class="text-left"><strong><h4>Verify Mobile No</h4></strong></span><hr>
                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Country Code (*)</b></span>
                            <select name="country_code" class="form-control country_code">
                                <option value="">--Select--</option>
                                <?php foreach($country_code as $code) { ?>
                                <option value="<?php echo $code["code"]; ?>"><?php echo $code["code"]; ?></option>
                                <?php } ?>
                                
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left" style="width: 100%; text-align: left;"><b>Contact No (*)</b></span>
                            <input type="text" name="contact_no" class="form-control contact_no numeric" placeholder="Contact No" style="/*width: 69% !important;display: inline-block !important;*/">
<!--                            <button type="button" class="send_otp btn btn-primary m-b" style="margin:0;">Send OTP</button>-->
                        </div>
                    </div>
		    <div class="col-sm-5">
			<div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Whom you want to manage delivery drivers? (*)</b></span>
                            <select name="delivery_by" class="form-control delivery_by">
                                <option value="">--Select--</option>
                                <option value="1">By DeliverIn</option>
				<option value="2">By Stuart</option>
                            </select>
                        </div>
		    </div>
<!--                    <div class="col-sm-5">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left" style="width: 100%; text-align: left;"><b>OTP Number (*)</b></span>
                            <input type="text" disabled="disabled" name="otp" class="form-control otp_no numeric" placeholder="OTP Number" maxlength="4" style="width: 69% !important;display: inline-block !important;">
                            <input type="hidden" name="delivery_receipt_id" id="delivery_receipt_id" class="form-control numeric" value="">
                            <button type="button" disabled="disabled" class="verify_otp btn btn-primary m-b" style="margin:0;">Verify OTP</button>
                        </div>
                    </div>-->		    
                </div>
		
		<!--<span class="text-left"><strong><h4></h4></strong></span><hr>-->
		
		<span class="text-left"><strong><h4>Working Hours</h4></strong></span><hr>
                <div class="row">
                    <div class="col-sm-12">
			<table class="table">
			    <thead>
				<tr>
				    <th>Sr.No.</th>
				    <th>Weekday</th>
				    <th>Start Time</th>
				    <th>End Time</th>
				</tr>
			    </thead>
			    <tbody>
				<?php for($i=1; $i<=7; $i++) { ?>
				<tr>
				    <td><?php echo $i; ?></td>
				    <td><input type="hidden" name="weekday[]" value="<?php echo $i; ?>">
					<?php echo $weekday[$i-0]; ?></td>
				    <td><input type="text" id="start_time_<?php echo $i; ?>" class="form-control stime_picker" placeholder="Start Time" name="start_time[]"></td>
				    <td><input type="text" id="end_time_<?php echo $i; ?>" class="form-control etime_picker" placeholder="End Time" name="end_time[]"></td>
				</tr>
				<?php } ?>
			    </tbody>
			</table>
                    </div>
                </div>
		
		<span class="text-left"><strong><h4>Bank Information</h4></strong></span><hr>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Bank Name (*)</b></span>
                            <input type="text" name="bank_name" class="form-control" placeholder="Bank Name">
                        </div>
                    </div>
                    
		    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Account Number (*)</b></span>
                            <input type="text" name="account_number" class="form-control numeric" placeholder="Account Number">
                        </div>
                    </div>
		    
		    <div class="col-sm-4">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left"><b>Routing Number / Sort Code (*)</b></span>
                            <input type="text" name="routing_no" class="form-control numeric" placeholder="Routing Number / Sort Code">
                        </div>
                    </div>
                </div>
                
                <span class="text-left"><strong><h4>Verification Documents Details</h4></strong></span><hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <span class="help-block m-b-none pull-left">
                                <b>Upload Verification Document (multiple) (*)</b>&nbsp;
                                <b><span title="For Verification Document, please upload your company’s premises license in full. You can add maximum 4 documents">(?)</span></b>
                            </span>
                            <input maxlength="4" type="file" id="verify_doc" name="verify_doc[]" class="form-control verify_doc" multiple="multiple">
                        </div>
                    </div>                    
                </div>
                <div class="m-t large-box row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <button type="button" class="submit_btn btn btn-primary block full-width m-b">Register</button>
<!--			    <button type="button" disabled="disabled" class="submit_btn btn btn-primary block full-width m-b">Register</button>-->
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <a href="login" class="btn btn-success block full-width m-b"><strong>Signin</strong></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    </body>

<script>
    function error_msg(msg) {
        return '<div class="error">' + msg + '</div>';
    }
    function hide_msg() {
        setTimeout(function () {
            $('.success').html('');
        }, 3000);
    }
    
    $(".latitude").hide();
    $(".longitude").hide();
    
    $(document).ready(function ()
    {    
	$(".verify_doc").change(function(){  
	    var numFiles = $(this)[0].files.length;
	    
	    if(numFiles > 4) {
		alert("You are allowed to upload only 4 documents.");
		return false;
	    }
	});

        $('.submit_btn').click(function () {
            
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "seller_register"]');
	    
	    var numFiles = $(".verify_doc")[0].files.length;
	    
	    if(numFiles > 4) {
		alert("You are allowed to upload only 4 documents.");
		return false;
	    }
	    
	    //Seller name
            var seller_name = frm.find('[name = "seller_name"]').val();
            if (!seller_name || !seller_name.trim()) {
                frm.find('[name = "seller_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your full name'));
                valid = false;
            } 
            	    
	    //Address
            var address = frm.find('[name = "address"]').val();
            if (!address || !address.trim()) {
                frm.find('[name = "address"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your full address'));
                valid = false;
            }
	    
	    //Email
            var email = frm.find('[name = "email"]').val();
            if (!email || !email.trim()) {
                frm.find('[name = "email"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your email address'));
                valid = false;
            }
	    
	    //Password
            var password = frm.find('[name = "password"]').val();
            if (!password || !password.trim()) {
                frm.find('[name = "password"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your password'));
                valid = false;
            }
	    else {
                var pattern = new RegExp(/^[a-zA-Z0-9_-]{5,15}$/);
                if (pattern.test(password)) {
                } else {
                    frm.find('[name="password"]').addClass('input_error').parents('.form-group').append(error_msg('Password must be 5-15 charaters'));
                       valid = false;
                }
            }
	    
	    //confirm password
	    var cnf_password = frm.find('[name="cnf_password"]').val();
            if (!cnf_password || !cnf_password.trim()) {
                frm.find('[name="cnf_password"]').addClass('input_error').parents('.form-group').append(error_msg('Please re-type password'));
                valid = false;
            } else if (password != cnf_password) {
                frm.find('[name="cnf_password"]').addClass('input_error').parents('.form-group').append(error_msg('Password does not match'));
                valid = false;
            }
	    
	    //Delivery Zone
            var dzone_id = frm.find('[name = "dzone_id"]').val();
            if (!dzone_id || !dzone_id.trim()) {
                frm.find('[name = "dzone_id"]').addClass('input_error').parents('.form-group').append(error_msg('Please select your delivery zone.'));
                valid = false;
            }
	    
	    //Delivery By
            var delivery_by = frm.find('[name = "delivery_by"]').val();
            if (!delivery_by || !delivery_by.trim()) {
                frm.find('[name = "delivery_by"]').addClass('input_error').parents('.form-group').append(error_msg('Please select from whom you want to carryout delivery.'));
                valid = false;
            }
	    
	    //TimeZone
            var timezone = frm.find('[name = "timezone"]').val();
            if (!timezone || !timezone.trim()) {
                frm.find('[name = "timezone"]').addClass('input_error').parents('.form-group').append(error_msg('Please select your timezone.'));
                valid = false;
            }
	    
	    //country code
            var country_code = frm.find('[name = "country_code"]').val();
            if (!country_code || !country_code.trim()) {
                frm.find('[name = "country_code"]').addClass('input_error').parents('.form-group').append(error_msg('Please select your country code.'));
                valid = false;
            }
	    
	    //contact number
            var contact_no = frm.find('[name = "contact_no"]').val();
            if (!contact_no || !contact_no.trim()) {
                frm.find('[name = "contact_no"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your contact number.'));
                valid = false;
            }
	    
	    //bank name
            var bank_name = frm.find('[name = "bank_name"]').val();
            if (!bank_name || !bank_name.trim()) {
                frm.find('[name = "bank_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your bank name.'));
                valid = false;
            }
	    
	    //account number
            var account_number = frm.find('[name = "account_number"]').val();
            if (!account_number || !account_number.trim()) {
                frm.find('[name = "account_number"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your account number.'));
                valid = false;
            }
	    
	    //routing number
            var routing_no = frm.find('[name = "routing_no"]').val();
            if (!routing_no || !routing_no.trim()) {
                frm.find('[name = "routing_no"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your account routing number.'));
                valid = false;
            }
	    
	    //dob
            var dob = frm.find('[name = "dob"]').val();
            if (!dob || !dob.trim()) {
                frm.find('[name = "dob"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your date of birth.'));
                valid = false;
            }
	    
	    //city
            var city = frm.find('[name = "city"]').val();
            if (!city || !city.trim()) {
                frm.find('[name = "city"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your city.'));
                valid = false;
            }
	    
	    //postal code
            var postalcode = frm.find('[name = "postalcode"]').val();
            if (!postalcode || !postalcode.trim()) {
                frm.find('[name = "postalcode"]').addClass('input_error').parents('.form-group').append(error_msg('Please add your postal code.'));
                valid = false;
            }	    
	    
	    //Upload document
            var verify_doc = frm.find('#verify_doc').val();
	    if (verify_doc == "") {
		frm.find('#verify_doc').addClass('input_error').parents('.form-group').append(error_msg('Please select your verification documents.'));
                valid = false;
            }
	    
	    //Start time
            var start_time = $("#start_time_1").val();
            if (!start_time || !start_time.trim()) {
                $("#start_time_1").addClass('input_error').parents('.form-group').append(error_msg('Please select your start time.'));
                valid = false;
            }	
	    
	    //End time
            var end_time = $("#end_time_1").val();
            if (!end_time || !end_time.trim()) {
                $("#end_time_1").addClass('input_error').parents('.form-group').append(error_msg('Please select your end time.'));
                valid = false;
            }	
            
            if (valid) {
                $(this).html('Processing...');
                var data = new FormData(frm[0]);
                //console.log(data);
                $.ajax({
                    url: "register/save_register",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function (resp)
                    {
                        //console.log(resp);
                        if (resp === 'exists') {
                            alert('Email Already Exists');
                            frm.find('[name = "email"]').addClass('input_error').parents('.form-group').append(error_msg('Email already exists.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }
			else if (resp === 'error') {
                            alert('Upload atleast one verification document.');
                            frm.find('[name = "verify_doc"]').addClass('input_error').parents('.form-group').append(error_msg('Upload atleast one verification document.'));
                            valid = false;
                            $('.submit_btn').html('Save');
                        }
                        else if (resp === 'success'){
                            alert('Registration Successful. Please wait till the admin verify your docs.');
                            window.location = "login";
                        }
                    }
                });
            }
        });
        
        $(".send_otp").click(function() {
            var contact_no = document.seller_register.contact_no.value;
            var country_code = document.seller_register.country_code.value;

            if (contact_no == "")
            {
                alert("Please enter your mobile no!");
                return false;
            }

            if (country_code == "")
            {
                alert("Please select your country code!");
                return false;
            }
            
            var data = $('form[name="seller_register"]').serialize();            
            $.ajax({
                url: "register/send_otp",
                type: "post",
                data: data,
                success: function (resp)
                {
                    //console.log(resp);
                    if (resp === 'exists') {
                        alert('Mobile No Already Exists');
                        valid = false;
                        $('.submit_btn').html('Save');
                    }
                    else if (resp === 'wrong') {
                        alert('Invalid Mobile No');
                        valid = false;
                        $('.submit_btn').html('Save');
                    }
                    else {
                        var res = $.parseJSON(resp);
                        alert(res.otp);
                        $("#delivery_receipt_id").val(res.delivery_receipt_id);
                        $(".otp_no").removeAttr('disabled');
                        $(".verify_otp").removeAttr('disabled');                        
                        $(".contact_no").attr('readonly', 'readonly');
                        $(".send_otp").attr('disabled', 'disabled');
                    }
                }
            });            
        });
        
        $(".verify_otp").click(function() {
            var otp_no = document.seller_register.otp.value;

            if (otp_no == "")
            {
                alert("Please enter your OTP!");
                return false;
            }
            
            var data = $('form[name="seller_register"]').serialize();            
            $.ajax({
                url: "register/verify_otp",
                type: "post",
                data: data,
                success: function (resp)
                {
                    if(resp == 1){
                        alert('OTP Verified');
                        $(".submit_btn").removeAttr('disabled');
                        $(".otp_no").attr('disabled', 'disabled');
                        $(".verify_otp").attr('disabled', 'disabled');                   
                        $(".contact_no").attr('readonly', 'readonly');
                        $(".send_otp").attr('disabled', 'disabled');
                    }
                    else {
                        alert('Invalid OTP');
                    }
                }
            });            
        });
        
        $(".country_code").click(function(){
            return false;
        }); 
	
	if ($(".date_picker").length) {            
	    $('.date_picker').attr('type', 'text');
	    $('.date_picker').datepicker({
		startView: 3,
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		maxDate: new Date(),
		format: "dd/mm/yyyy"
	    });
        }
	
	if ($(".stime_picker").length) {
	    $('.stime_picker').timepicker({
		autoclose: true,
		timeFormat: "HH:mm:ss"
	    });
        }
	
	if ($(".etime_picker").length) {           
	    $('.etime_picker').timepicker({
		autoclose: true,
		timeFormat: "HH:mm:ss"
	    });
        }
        
        allow_numeric();
    });
    
    function allow_numeric(){
        $(".numeric").keypress(function (e) {
            //if the letter is not digit then display error and don't type anything
            if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
               //display error message
               return false;
           }
        });
    }    
</script>
</html>
