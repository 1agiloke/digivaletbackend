@extends('layouts.admin')

@section('header')
	<section class="content-header">
		<h1>
		Parking Location
		<small>List</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li><a href="#">Parking Location</a></li>
			<li class="active"><a href="#">List</a></li>
		</ol>
	</section>
@endsection

@section('content')
	<div class="box box-danger">
        <div class="box-body">
            <div class="table-responsive">
                <table id="data_table" class="table table-striped table-bordered table-hover nowrap dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Device Name</th>
                            <th>Owner</th>
                            <th>Capacity</th>
                            <th>Exist</th>
                            <th>Date created</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
	<script>
        jQuery(document).ready(function($){
            var table = $('#data_table').DataTable({
                "bFilter": true,
                "processing": true,
                "serverSide": true,
                "lengthChange": true,
                "ajax": {
                    "url": "{{ route('admin.parking-location.index') }}",
                    "type": "POST",
                    "data" : {}
                },
                "language": {
                    "emptyTable": "Tidak Ada Data Tersedia",
                },
                "columns": [
                    {
                       data: null,
                       render: function (data, type, row, meta) {
                           return meta.row + meta.settings._iDisplayStart + 1;
                       },
                       "width": "20px",
                       "orderable": false,
                    },
                    {
                        "data": "device_name",
                        "orderable": true,
                    },
                    {
                        "data": "name",
                        "orderable": true,
                    },
                    {
                        "data": "capacity",
                        "orderable": true,
                    },
                    {
                        "data": "exist",
                        "orderable": true,
                    },
                    {
                        "data": "created_at",
                        "orderable": true,
                    }
                ],
                "order": [ 5, 'asc' ],
                "fnCreatedRow" : function(nRow, aData, iDataIndex) {
                    $(nRow).attr('data', JSON.stringify(aData));
                }
            });
        });
    </script>
@endsection
