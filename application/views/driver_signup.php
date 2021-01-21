<style>
    /* Dropzone */
    .dropzone { min-height: 140px; border: 1px dashed #1ab394; background: white; padding: 20px 20px; width:100%;}
    .dropzone .dz-message{font-size: 16px;}
    input.upload_img { display: inline-block; width: 100%; padding: 139px 0 0 0; height: 100px; overflow: hidden; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; background: url('https://cdn1.iconfinder.com/data/icons/hawcons/32/698394-icon-130-cloud-upload-512.png') center center no-repeat #e4e4e4; border-radius: 20px; background-size: 60px 60px; }
    .main_header{background-color: #fff !important;box-shadow: 0 0 5px rgba(0, 0, 0, 0.15) !important;}
    .main_header .media h5 {color: #454545 !important;}
    .main_header #menu_icon, .main_header #shopping_bag_icon{fill: #454545 !important;}
    .h-100vh{min-height: calc(100vh - 66px)}
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

<section class="become_driver_signup bg-light-gray mt-66 page_section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10">
                <div class="card p-3 p-sm-3 p-md-4 p-lg-5 p-xl-5">
		    <div class="row">
			<div class="col-9 col-sm-9 col-lg-9 col-md-9 col-xl-9">
			    <h4 class="title">Sign Up For Drivers</h4>
			    <p class="desc">Welcome to our two-minute sign-up process! Input your details below and we’ll do the rest.</p>
			</div>
			<div class="col-3 col-sm-3 col-lg-3 col-md-3 col-xl-3">
			    <img class="app_icon mb-4" src="<?php echo base_url(). 'assets/website/logo.jpg'; ?>" alt="" width="100px" style="float:right;"/>
			</div>
		    </div>                    
		    
                    <form action="#" method="post" name="frm_driver_save" class="frm_driver_save form-control" enctype="multipart/form-data">
			<h5> #1 Driver Personal Information</h5><hr>			
                        <div class="row mt-4">
                            <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" id="name" name="name" type="text" placeholder="Full Name(*)"/>
                                </div>
                            </div>
			    <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" id="profile_image" name="profile_image" type="file"/>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" name="email" type="text" placeholder="E-Mail(*)"/>
                                </div>
                            </div>
			    
			    <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control date_picker_driver" autocomplete="off" name="birthdate" type="text" placeholder="Date of birth(*)"/>
                                </div>
                            </div>
			    
                            <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
				    <select class="form-control" name="country_code" id="country_code">
					<option value="">--Any country code--</option>
					<?php if(!empty($country_list)) { foreach($country_list as $c) { ?>
					    <option value="<?php echo $c["code"]; ?>"><?php echo $c["name"]; ?></option>
					<?php } } ?>
				    </select>
                                </div>
                            </div>
			    
			    <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control numeric" maxlength="15" name="mobileno" type="text" placeholder="Mobile(*)"/>
                                </div>
                            </div>
			    			    
			    
				<div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
				    <div class="form-group">
					<input type="text" class="form-control address" onFocus="geolocate()" name="address" value="" id="address" placeholder="Address (*)">                            
				    </div>
				</div>

				<div class="form-group col-sm-3 latitude">
				    <input type="text" class="form-control latitude" name="latitude" value="" id="latitude" placeholder="Latitude (*)">                            
				</div>

				<div class="form-group col-sm-3 longitude">
				    <input type="text" class="form-control longitude"  name="longitude" value="" id="longitude" placeholder="Longitude (*)">                            
				</div>

				<div class="form-group col-sm-6 longitude">                                                        
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
			    
			    
			    <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" maxlength="150" name="city" type="text" placeholder="City(*)"/>
                                </div>
                            </div>
			    
			    <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" maxlength="150" name="postalcode" type="text" placeholder="Postal Code(*)"/>
                                </div>
                            </div>
			</div>
			<br>
			
			<h5>#2 Driver Vehicle Information</h5><hr>			
			<div class="row mt-4">
                            <div class="col-4 col-sm-4 col-lg-4 col-md-4 col-xl-4">
                                <div class="form-group">
                                    <input class="form-control" id="maker" name="maker" type="text" placeholder="Vehicle Make(*)"/>
                                </div>
                            </div>
                             <div class="col-4 col-sm-4 col-lg-4 col-md-4 col-xl-4">
                                <div class="form-group">
                                    <input class="form-control" id="model" name="model" type="text" placeholder="Vehicle Model(*)"/>
                                </div>
                            </div>
                             <div class="col-4 col-sm-4 col-lg-4 col-md-4 col-xl-4">
                                <div class="form-group">
                                    <input class="form-control" id="registration_number" name="registration_number" type="text" placeholder="Vehicle Registration Number(*)"/>
                                </div>
                            </div>
			    
			    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                                <div class="form-group">
                                    <textarea class="form-control" id="vehicle_info" name="vehicle_info" type="text" rows="4" placeholder="Additional information about the vehicle"></textarea>
                                </div>
                            </div>
			    
                            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                                <div class="form-group vehicle_imgs">
                                    <label>Upload Vehicle Image (Maximum 2 images)</label><br>
                                    <input id="vehicle_imgs" name="vehicle_imgs[]" class="upload_img dropzone" type="file" multiple="multiple">
                                    <div id="files" class="files dz-message"></div>
                                </div>
                            </div>
                            
                            <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                                <div class="form-group" id="docs">
                                    <label>Upload Personal Documents (UK driving license/car insurance, document that proves your right to work in the UK)(*) <b><span title="(Maximum 6 images)">(?)</span></b></span></label>
                                    <input id="docs" name="docs[]" class="upload_img dropzone" type="file" multiple="multiple">
                                    <div id="docs_files" class="docs_files dz-message"></div>
                                </div>
                            </div>                            
                        </div>
			<br>
			
			<h5>#3 Driver Bank Details</h5><hr>
                        <div class="row mt-4">
                            <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" id="account_number" name="account_number" type="text" placeholder="Account Number(*)"/>
                                </div>
                            </div>
			    <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" id="bank_name" name="bank_name" type="text" placeholder="Bank Name(*)"/>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" name="routing_no" id="routing_no" type="text" placeholder="Sort Code(*)"/>
                                </div>
                            </div>
			    <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" id="name_of_card" name="name_of_card" type="text" placeholder="Name Of Card(*)"/>
                                </div>
                            </div>
			</div>
			<br>
			
			<hr><h5> Referral Code</h5>
                        <div class="row mt-4">
                            <div class="col-6 col-sm-6 col-lg-6 col-md-6 col-xl-6">
                                <div class="form-group">
                                    <input class="form-control" id="refrence_code" name="refrence_code" type="text" placeholder="Referral Code"/>
                                </div>
                            </div>
			</div>
			
			<div class="row mt-4">
			    <div class="col-12 col-sm-12 col-lg-12 col-md-12 col-xl-12">
                                <button type="button" class="btn btn-pink_squre submit_btn">Submit</button>
                                <span class="success_msg2" style="color:green"> </span>
                                <span class="error_msg2" style="color:red"> </span>
                            </div>
			</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(".latitude").hide();
    $(".longitude").hide();
    $(document).ready(function () {
	
	//datepicker
        if ($(".date_picker_driver").length) {
	    $('.date_picker_driver').attr('type', 'text');
	    $('.date_picker_driver').datepicker({
		startView: 3,
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format: "dd/mm/yyyy"
	    });
        }
	
        $(".numeric").keypress(function (e) {
            //alert(e.which);
            //if the letter is not digit then display error and don't type anything
            if (e.which != 32 && e.which != 45 && e.which != 43 && e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
                //display error message
                return false;
            }
        });

        //to upload vehicle images
        var max = 2;
        var replaceMe = function () {
	    $("#modal_submit_reviews").find(".alert_msg").html("");
	    
            var obj = $(this);
            if ($(".vehicle_imgs input[type='file']").length > max)
            {
		$("a.modal_submit_reviewss").trigger("click");
		$("#modal_submit_reviews").find(".alert_msg").html("You can add maxmium 2 images.");
                obj.val("");
                return false;
            } else {
                $(obj).css({'position': 'absolute', 'left': '-9999px', 'display': 'none'});
                $(obj).after('<input class="upload_img dropzone" type="file" name="' + obj.attr('name') + '"/>');
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#files').append('<img src=' + e.target.result + ' width=100 height=100 />');
                    }
                    reader.readAsDataURL(this.files[0]);
                }
                //if($(".vehicle_imgs input[type='file']").length < max) {
                $(".vehicle_imgs input[type='file']").change(replaceMe);
                //}
            }
        }
        $(".vehicle_imgs input[type='file']").change(replaceMe);

        //to upload documents
        var max1 = 6;
        var replaceMe1 = function () {
	    $("#modal_submit_reviews").find(".alert_msg").html("");
	    
            var obj = $(this);
            if ($("#docs input[type='file']").length > max1)
            {
                $("a.modal_submit_reviewss").trigger("click");
		$("#modal_submit_reviews").find(".alert_msg").html("You can add maxmium 6 images.");
                obj.val("");
                return false;
            }
            $(obj).css({'position': 'absolute', 'left': '-9999px', 'display': 'none'});
            $(obj).after('<input class="upload_img dropzone" type="file" name="' + obj.attr('name') + '"/>');
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#docs_files').append('<img src=' + e.target.result + ' width=100 height=100 />');
                }
                reader.readAsDataURL(this.files[0]);
            }
            $("#docs input[type='file']").change(replaceMe1);
        }
        $("#docs input[type='file']").change(replaceMe1);

        $('.submit_btn').click(function (e) {
            $('.success').html('');
            $('.error').remove();
            $('.form-control').removeClass('input_error');
            var valid = true;
            var frm = $('form[name = "frm_driver_save"]');

            var name = frm.find('[name="name"]').val();
            if (!name || !name.trim()) {
                frm.find('[name="name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your name'));
                valid = false;
            }

            var email = frm.find('[name="email"]').val();
            if (!email || !email.trim()) {
                frm.find('[name="email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your email'));
                valid = false;
            } else {
                var pattern = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
                if (pattern.test(email)) {
                } else {
                    frm.find('[name="email"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter valid email address'));
                    valid = false;
                }
            }

            var mobileno = frm.find('[name="mobileno"]').val();
            if (!mobileno || !mobileno.trim()) {
                frm.find('[name="mobileno"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your mobile number'));
                valid = false;
            }
	    
	    var country_code = frm.find('[name="country_code"]').val();
            if (!country_code || !country_code.trim()) {
                frm.find('[name="country_code"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your country code'));
                valid = false;
            }
	    
	    var birthdate = frm.find('[name="birthdate"]').val();
            if (!birthdate || !birthdate.trim()) {
                frm.find('[name="birthdate"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your date of birth'));
                valid = false;
            }
	    
	    var city = frm.find('[name="city"]').val();
            if (!city || !city.trim()) {
                frm.find('[name="city"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your city'));
                valid = false;
            }
	    
	    var postalcode = frm.find('[name="postalcode"]').val();
            if (!postalcode || !postalcode.trim()) {
                frm.find('[name="postalcode"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your postal code'));
                valid = false;
            }

            var maker = frm.find('[name="maker"]').val();
            if (!maker || !maker.trim()) {
                frm.find('[name="maker"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your vehicle make'));
                valid = false;
            }

            var model = frm.find('[name="model"]').val();
            if (!model || !model.trim()) {
                frm.find('[name="model"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your vehicle model'));
                valid = false;
            }

            var registration_number = frm.find('[name="registration_number"]').val();
            if (!registration_number || !registration_number.trim()) {
                frm.find('[name="registration_number"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your vehicle registration number'));
                valid = false;
            }
	    
	    var account_number = frm.find('[name="account_number"]').val();
            if (!account_number || !account_number.trim()) {
                frm.find('[name="account_number"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your account number'));
                valid = false;
            }
	    
	    var address = frm.find('[name="address"]').val();
            if (!address || !address.trim()) {
                frm.find('[name="address"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your address'));
                valid = false;
            }
	    
	    var bank_name = frm.find('[name="bank_name"]').val();
            if (!bank_name || !bank_name.trim()) {
                frm.find('[name="bank_name"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your bank name'));
                valid = false;
            }	    
	    
	    var routing_no = frm.find('[name="routing_no"]').val();
            if (!routing_no || !routing_no.trim()) {
                frm.find('[name="routing_no"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your sort code'));
                valid = false;
            }
	    
	    var name_of_card = frm.find('[name="name_of_card"]').val();
            if (!name_of_card || !name_of_card.trim()) {
                frm.find('[name="name_of_card"]').addClass('input_error').parents('.form-group').append(error_msg('Please enter your card name'));
                valid = false;
            }

            var docs = $("#docs input[type='file']").length;
            //console.log(docs);
            if (docs == 1) {
                $("#docs").addClass('input_error').append(error_msg('Please add your verification documents'));
                valid = false;
            }

            if (valid) {
                $(this).attr("disabled", "disabled");
                $(this).html('Processing...');
                var data = new FormData(frm[0]);

                $.ajax({
                    url: "<?php echo base_url() . 'become_a_driver/add_driver'; ?>",
                    type: "post",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (data) {
                        //console.log(data);
                        $(".submit_btn").removeAttr('disabled');
                        $('.submit_btn').html('Submit');
                        if (data == 'success') {
                            $('form[name = "frm_driver_save"]').trigger("reset");
                            $("#docs_files").html("");
                            $("#files").html("");
                            $('.success_msg2').html('Thank you for signing up! You can expect to hear from us in at most 14 days, when we will schedule a meeting with you to verify your documents and discuss Janet-Collection work principles. (let you know what delivering for off-licences partnered with Janet-Collection entails)');
                        } else if (data == 'exist') {
                            frm.find('[name="email"]').addClass('input_error').parents('.form-group').append(error_msg('Email already exists'));
                        }
                    }
                });
            }
        });
    });
</script>
