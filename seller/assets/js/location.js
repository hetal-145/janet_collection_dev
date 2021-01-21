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