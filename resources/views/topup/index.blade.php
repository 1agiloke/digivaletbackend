@extends('layouts.app')

@section('header')
	<section class="content-header">
		<h1>History Balance</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li>Balance</li>
			<li class="active">History</li>
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
                            <th>Nominal</th>
                            <th>Bank Transfer</th>
                            <th>Status</th>
                            <th>Top Up Date</th>
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
                    "url": "{{ route('top-up.index') }}",
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
                        "data": "nominal",
                        "orderable": true,
                    },
                    {
                        "data": "bank.name",
                        "orderable": true,
                    },
                    {
                        "data": "status",
                        "orderable": true,
                    },
                    {
                        "data": "created_at",
                        "orderable": true,
                    }
                ],
                "order": [ 3, 'desc' ],
                "fnCreatedRow" : function(nRow, aData, iDataIndex) {
                    $(nRow).attr('data', JSON.stringify(aData));
                }
            });
        });
    </script>
@endsection
