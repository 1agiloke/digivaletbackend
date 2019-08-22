@extends('layouts.admin')

@section('header')
	<section class="content-header">
		<h1>
		Parking Location
		<small>Add</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li><a href="#">Parking Location</a></li>
			<li class="active"><a href="#">Add</a></li>
		</ol>
	</section>
@endsection

@section('content')
	<div class="box box-danger">
       <form action="" id="formAddParking" enctype="multipart/form-data" method="POST" autocomplete="off">
	        <div class="box-body">
                <div class="form-group">
                    <label class="control-label">Merchant</label>
                    <select class="form-control" id="merchant" name="merchant">
                        <option value="">-- Select One --</option>
                        @foreach ($merchants as $merchant)
                            <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
                <div class="form-group">
                    <label class="control-label">Capacity</label>

                    <input class="form-control" type="text" id="capacity" name="capacity" placeholder="Enter Capacity">
                    <span class="help-block"></span>
                </div>
                <div class="form-group">
                    <label for="">Set Parking Location</label>
                    <input type="text" id="searchmap" name="title" class="form-control" required placeholder="Enter Your Location">
                    <div id="map-canvas"></div>
                </div>
                <input type="hidden" id="latitude" name="latitude" class="form-control" required>
                <input type="hidden" id="longitude" name="longitude" class="form-control" required>
            </div>
            <div class="box-footer">
	        	<button type="reset" class="btn btn-warning pull-left" data-dismiss="modal">
                    Reset
                </button>
                <button type="submit" class="btn btn-primary pull-right" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
                    Sumbit
                </button>
	        </div>
       </form>
    </div>
@endsection

@section('css')
    <style>
        #map-canvas {
            height: 400px;
            width: 100%;
        }
    </style>
@endsection

@section('js')
    <script>
        $('#formAddParking').submit(function (event) {
            $('#formAddParking div.form-group').removeClass('has-error');
            $('#formAddParking .help-block').empty();
            event.preventDefault();
            $('#formAddParking button[type=submit]').button('loading');

            var formData = new FormData($("#formAddParking")[0]);

            $.ajax({
                url: '{{ route("admin.parking-location.store") }}',
                type: 'POST',
                data: formData,
                processData : false,
                contentType : false,
                cache: false,

                success: function (response) {
                    if (response.success) {
                        $.toast({
                            heading: 'Success',
                            text : response.message,
                            position : 'top-right',
                            allowToastClose : true,
                            showHideTransition : 'fade',
                            icon : 'success',
                            loader : false
                        });

                        setTimeout(function () {
                            location.href = "/admin/parking-location/add";
                        }, 2000);
                    }
                    else {
                        $.toast({
                            heading: 'Error',
                            text : response.message,
                            position : 'top-right',
                            allowToastClose : true,
                            showHideTransition : 'fade',
                            icon : 'error',
                            loader : false
                        });
                    }

                    $('#formAddParking button[type=submit]').button('reset');
                },

                error: function(response){
                    if (response.status === 422) {
                        // form validation errors fired up
                        var error = response.responseJSON.errors;
                        var data = $('#formAddParking').serializeArray();
                        $.each(data, function(key, value){
                            if( error[data[key].name] != undefined ){
                                var elem;
                                if( $("#formAddParking input[name='" + data[key].name + "']").length )
                                    elem = $("#formAddParking input[name='" + data[key].name + "']");
                                else if( $("#formAddParking select[name='" + data[key].name + "']").length )
                                    elem = $("#formAddParking select[name='" + data[key].name + "']");
                                else
                                    elem = $("#formAddParking textarea[name='" + data[key].name + "']");

                                elem.parent().find('.help-block').text(error[data[key].name]);
                                elem.parent().find('.help-block').show();
                                elem.parent().addClass('has-error');
                            }
                        });
                    }
                    else if (response.status === 400) {
                        // Bad Client Request
                        $.toast({
                            heading: 'Error',
                            text : response.responseJSON.message,
                            position : 'top-right',
                            allowToastClose : true,
                            showHideTransition : 'fade',
                            icon : 'error',
                            loader : false,
                            hideAfter: 5000
                        });
                    }
                    else {
                        $.toast({
                            heading: 'Error',
                            text : "Whoops, looks like something went wrong.",
                            position : 'top-right',
                            allowToastClose : true,
                            showHideTransition : 'fade',
                            icon : 'error',
                            loader : false,
                            hideAfter: 5000
                        });
                    }
                    $('#formAddParking button[type=submit]').button('reset');
                }
            });
        });
    </script>
    <script>
        function initMap() {
            var posisi = { lat: 3.5951956, lng: 98.67222270000002 };
            var map = new google.maps.Map(document.getElementById('map-canvas'), {
                zoom: 15,
                center: posisi
            });

            var marker = new google.maps.Marker({
                position: posisi,
                map: map,
                draggable: true
            });

            var searchBox = new google.maps.places.SearchBox(document.getElementById('searchmap'));

            google.maps.event.addListener(marker,'click',function(e){
                info.open(map, marker);
            });

            google.maps.event.addListener(searchBox, 'places_changed', function(){
                var places = searchBox.getPlaces();
                var bounds = new google.maps.LatLngBounds();

                for (var i = 0; places = places[i]; i++) {
                    bounds.extend(places.geometry.location);
                    marker.setPosition(places.geometry.location);

                    map.fitBounds(bounds);
                    map.setZoom(15)
                }
            });

            google.maps.event.addListener(marker, 'position_changed', function(){
                var lat = marker.getPosition().lat();
                var lng = marker.getPosition().lng();

                $('#latitude').val(lat);
                $('#longitude').val(lng);

                latCoder = $('#latitude').val(lat).val();
                lngCoder = $('#longitude').val(lng).val();

                getCoder(latCoder, lngCoder);
            });
        }

        function getCoder(latCoder, lngCoder){
            var geocoder  = new google.maps.Geocoder();
            var location  = new google.maps.LatLng(latCoder, lngCoder);
            geocoder.geocode({'latLng': location}, function (results, status) {
            if(status == google.maps.GeocoderStatus.OK) {
                    var add=results[0].formatted_address;
                    $('#searchmap').val(add);
                }
            });
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3gN0AFseoGdJj7jV-gClr6Hsu9VVYsE0&libraries=places&callback=initMap"></script>
@endsection
