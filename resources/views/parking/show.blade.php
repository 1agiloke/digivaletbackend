@extends('layouts.app')

@section('header')
	<section class="content-header">
		<h1>Parking</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li>Parking</li>
			<li class="active">Detail</li>
		</ol>
	</section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Parking Location
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <b>Address :</b>
                        </div>
                        <div class="col-md-9">
                            {{ $data->location->address }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="map-canvas"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Configuration Parking
                    </h3>
                </div>
                <div class="box-body">
                    <table class="table table-responsive table-bordered table-striped" id="table_configuration">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Open Time</th>
                                <th>Close Time</th>
                                <th>Price <small>/hours</small></th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @foreach ($data->config_parkings as $item)
                            <tr>
                                <th>
                                    @php
                                        $day = "";
                                        if ($item->day == 0) {
                                            $day = "Sunday";
                                        } elseif($item->day == 1) {
                                            $day = "Monday";
                                        } elseif($item->day == 2) {
                                            $day = "Tuesday";
                                        } elseif($item->day == 3) {
                                            $day = "Wednesday";
                                        } elseif($item->day == 4) {
                                            $day = "Thursday";
                                        } elseif($item->day == 5) {
                                            $day = "Friday";
                                        } elseif($item->day == 6) {
                                            $day = "Saturday";
                                        }
                                    @endphp
                                    {{ $day }}
                                </th>
                                <td>{{ $item->open_time == null ? '-' : substr($item->open_time, 0, 5) }}</td>
                                <td>{{ $item->close_time == null ? '-' : substr($item->close_time, 0, 5) }}</td>
                                <td>{{ $item->price == null ? '-' : $item->price }}</td>
                                <td>{{ $item->status }}</td>
                                <td>
                                    <button class="btnEdit btn btn-xs btn-flat" data-id="{{ $item->id }}" data-day="{{ $item->day }}" data-open_time="{{ $item->open_time }}" data-close_time="{{ $item->close_time }}" data-price="{{ $item->price }}" data-status="{{ $item->status }}"> <i class="fa fa-pencil"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal" tabindex="-1" role="dialog" id="modalEdit" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="formEdit" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"></h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-horizontal">
                            <input type="hidden" id="id" name="id" value="">
                            <input type="hidden" id="day" name="day" value="">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">
                                    Time
                                </label>
                                <div class="col-sm-3">
                                    <input class="form-control" type="time" id="open_time" name="open_time" placeholder="Open Time">
                                    <span class="help-block"></span>
                                </div>
                                <div class="col-sm-3">
                                    <input class="form-control" type="time" id="close_time" name="close_time" placeholder="Close Time">
                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Price <small>/hours</small></label>

                                <div class="col-sm-9">
                                    <input type="text" id="price" name="price" class="form-control" placeholder="Enter Price">
                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Status</label>

                                <div class="col-sm-9">
                                    <select name="status" id="status" class="form-control">
                                        <option value="">-- Select One --</option>
                                        <option value="close">Close</option>
                                        <option value="open">Open</option>
                                    </select>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                            <i class="fa fa-close"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary pull-right" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        #map-canvas {
            height: 300px;
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('js')
    <script>
        jQuery(document).ready(function($){
            var url = null;

            // Edit
            $('#table_configuration').on('click', '.btnEdit' , function(e){
                $('#formEdit div.form-group').removeClass('has-error');
                $('#formEdit .help-block').empty();
                $('#formEdit .modal-title').text("Change Configuration Parking");
                $('#formEdit')[0].reset();
                $('#formEdit button[type=submit]').button('reset');

                url = "{{ route('parking.configuration') }}";

                $('#formEdit #id').val($(this).data('id'));
                $('#formEdit #day').val($(this).data('day'));
                $('#formEdit #open_time').val($(this).data('open_time').substring(0, 5));
                $('#formEdit #close_time').val($(this).data('close_time').substring(0, 5));
                $('#formEdit #price').val($(this).data('price'));
                $('#formEdit #status').val($(this).data('status'));

                $('#modalEdit').modal('show');
            });

            // formEdit
            $('#formEdit').submit(function (event) {
                event.preventDefault();
                $('#formEdit button[type=submit]').button('loading');
                $('#formEdit div.form-group').removeClass('has-error');
                $('#formEdit .help-block').empty();

                var _data = $("#formEdit").serialize();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: _data,
                    dataType: 'json',
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

                            $('#modalEdit').modal('hide');
                            location.reload();
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
                        $('#formEdit button[type=submit]').button('reset');
                    },

                    error: function(response){
                        if (response.status === 422) {
                            // form validation errors fired up
                            var error = response.responseJSON.errors;
                            var data = $('#formEdit').serializeArray();
                            $.each(data, function(key, value){
                                if( error[data[key].name] != undefined ){
                                    var elem;
                                    if( $("#formEdit input[name='" + data[key].name + "']").length )
                                        elem = $("#formEdit input[name='" + data[key].name + "']");
                                    else if( $("#formEdit select[name='" + data[key].name + "']").length )
                                        elem = $("#formEdit select[name='" + data[key].name + "']");
                                    else
                                        elem = $("#formEdit textarea[name='" + data[key].name + "']");

                                    elem.parent().find('.help-block').text(error[data[key].name]);
                                    elem.parent().find('.help-block').show();
                                    elem.parent().parent().addClass('has-error');
                                }
                            });
                        }
                        $('#formEdit button[type=submit]').button('reset');
                    }
                });
            });
        });
    </script>

    <script>
		function initMap() {
			var latitude    = {{ $data->location->latitude }};
            var longitude   = {{ $data->location->longitude }};
            var posisi = {lat: latitude, lng: longitude};
            var map = new google.maps.Map(document.getElementById('map-canvas'), {
                zoom: 15,
                center: posisi
            });

            var contentString = '<div><b>{{ $data->name }}</b>';
            var info = new google.maps.InfoWindow({
                content: contentString
            });

        	var image = 'https://png.icons8.com/ios-glyphs/50/e74c3c/find-hospital.png';
            var marker = new google.maps.Marker({
                position: posisi,
                title:'Locations Office',
                map: map,
                icon: image,
            });

            google.maps.event.addListener(marker,'click',function(e){
                info.open(map, marker);
            });
		}
	</script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3gN0AFseoGdJj7jV-gClr6Hsu9VVYsE0&libraries=places&callback=initMap"></script>
@endsection
