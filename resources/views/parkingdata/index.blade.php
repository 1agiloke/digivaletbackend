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
                            <th>Parking Code</th>
                            <th>Police Number</th>
                            <th>Customer Name</th>
                            <th>Entry Time</th>
                            <th>Exit Time</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modalManualPayment">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="formManualPayment" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"></h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-horizontal">
                            <input type="hidden" id="id_parking" name="id_parking">
                            <input type="hidden" id="id_config_parking" name="id_config_parking">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Duration</label>

                                <div class="col-sm-9">
                                    <input type="text" id="number" name="number" class="form-control" value="2 Hours" readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Price</label>

                                <div class="col-sm-9">
                                    <input type="text" id="owner" name="owner" class="form-control" value="Rp. 6000" readonly>
                                    <span class="help-block"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>">
                            Save
                        </button>
                    </div>
                </form>
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
                        "data": "code",
                        "orderable": true,
                    },
                    {
                        "data": "police_number",
                        "orderable": true,
                    },
                    {
                        "data": "customer.name",
                        "orderable": true,
                    },
                    {
                        "data": "entry_time",
                        "orderable": true,
                    },
                    {
                        "data": "exit_time",
                        render: function (data, type, row){
                            return data == null ? '-' : data;
                        },
                        "orderable": true,
                    },
                    {
                        "data": "price",
                        render: function (data, type, row){
                            return "Rp. " + data;
                        },
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
                    {
                        render : function(data, type, row){
                            if(row.status == 'process' ){
                                return	'<a href="#" class="manual-payment-btn btn btn-xs btn-warning" data-parking="'+ row.parking.id +'" data-config_parking="'+ {{ date("w") }} +'"><i class="fa fa-pencil"> Manual Payment</i></a>';
                            } else {
                                return "&nbsp";
                            }
                        },
                        "width": "10%",
                        "orderable": false,
                    }
                ],
                "order": [ 1, 'asc' ],
                "fnCreatedRow" : function(nRow, aData, iDataIndex) {
                    $(nRow).attr('data', JSON.stringify(aData));
                }
            });

            // Manual Payment
            $('#data_table').on('click', '.manual-payment-btn', function(e){
                $('#formManualPayment div.form-group').removeClass('has-error');
                $('#formManualPayment .help-block').empty();
                $('#formManualPayment .modal-title').text("Manual Payment");
                $('#formManualPayment')[0].reset();
                var aData = JSON.parse($(this).parent().parent().attr('data'));
                $('#formManualPayment button[type=submit]').button('reset');

                $('#formManualPayment .modal-body .form-horizontal').append('<input type="hidden" name="_method" value="PUT">');
                url = '{{ route("parking-data.index") }}' + '/manual-payment/' + aData.id;

                $('#formManualPayment #id_parking').val($(this).data('parking'));
                $('#formManualPayment #id_config_parking').val($(this).data('config_parking'));

                $('#modalManualPayment').modal('show');
            });

            $('#btnFilter').click(function (e) {
               e.preventDefault();
               table.draw();
            });
        });
    </script>
@endsection
