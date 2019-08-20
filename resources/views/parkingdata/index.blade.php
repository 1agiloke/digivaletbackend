@extends('layouts.app')

@section('header')
	<section class="content-header">
		<h1>Parking Data</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="active">Parking Data</li>
		</ol>
	</section>
@endsection

@section('content')
	<div class="box box-warning">
        <div class="box-header">
            <form>
                <div class="row">
                    <div class="form-group col-md-6">
                        <span class="form-group-addon"><b>Status</b></span>
                        <select class="form-control" id="status" name="status" style="width: 100%;">
                            <option value="">Semua</option>
                            <option value="process">Process</option>
                            <option value="done">Done</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6 align-bottom">
                        <span class="form-group-addon"><b>&nbsp;</b></span>
                        <button id="btnFilter" class="form-control btn btn-md btn-primary"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
           </form>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table id="data_table" class="table table-striped table-bordered table-hover nowrap dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Police Number</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Price</th>
                            <th>Status</th>
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
                    "url": "{{ route('parking-data.index') }}",
                    "type": "POST",
                    "data" : function(d){
                        return $.extend({},d,{
                            'status' : $('#status').val(),
                        });
                    }
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
                        "data": "police_number",
                        "orderable": true,
                    },
                    {
                        "data": "date",
                        "orderable": true,
                    },
                    {
                        "data": "day",
                        render: function (data, type, row){
                            if ( data == "1" ) {
                                return "Monday"
                            }
                            else if( data == "2" ) {
                                return "Tuesday"
                            }
                            else if( data == "3" ) {
                                return "Wednesday"
                            }
                            else if( data == "4" ) {
                                return "Thursday"
                            }
                            else if( data == "5" ) {
                                return "Friday"
                            }
                            else if( data == "6" ) {
                                return "Saturday"
                            }
                            else if( data == "7" ) {
                                return "Sunday"
                            }
                        },
                        "orderable": true,
                    },
                    {
                        "data": "time_in",
                        "orderable": true,
                    },
                    {
                        "data": "time_out",
                        "orderable": true,
                    },
                    {
                        "data": "price",
                        "orderable": true,
                    },
                    {
                        "data": "status",
                        render: function (data, type, row){
                            if (data == "process") {
                                return "<span class='badge bg-orange'>Process</span>";
                            }
                            else if(data == "done"){
                                return "<span class='badge bg-green'>Done</span>";
                            }
                            else {
                                return "<span class='badge bg-red'>Failed</span>";
                            }
                        },
                        "orderable": true,
                    },
                ],
                "order": [ 1, 'asc' ],
                "fnCreatedRow" : function(nRow, aData, iDataIndex) {
                    $(nRow).attr('data', JSON.stringify(aData));
                }
            });

            $('#btnFilter').click(function (e) {
               e.preventDefault();
               table.draw();
            });
        });
    </script>
@endsection
