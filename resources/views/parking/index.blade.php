@extends('layouts.app')

@section('header')
	<section class="content-header">
		<h1>Parking</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="active">Parking</li>
		</ol>
	</section>
@endsection

@section('content')
	<div class="box box-warning">
        <div class="box-body">
            <div class="table-responsive">
                <table id="data_table" class="table table-striped table-bordered table-hover nowrap dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Device Name</th>
                            <th>Capacity</th>
                            <th>Exist</th>
                            <th>Date created</th>
                            <th>Action</th>
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
                    "url": "{{ route('parking.index') }}",
                    "type": "POST",
                    "data" : {}
                },
                "language": {
                    "emptyTable": "No Data Available",
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
                    },
                    {
                        render : function(data, type, row){
                            return	'<a href="{{ route('parking.index') }}/'+ row.id +'" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> Detail</a>';
                        },
                        "width": "10%",
                        "orderable": false,
                    }
                ],
                "order": [ 4, 'asc' ],
                "fnCreatedRow" : function(nRow, aData, iDataIndex) {
                    $(nRow).attr('data', JSON.stringify(aData));
                }
            });
        });
    </script>
@endsection
