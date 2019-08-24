@extends('layouts.admin')

@section('header')
	<section class="content-header">
		<h1>
		Customer
		<small>List</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="active"><a href="#">Customer</a></li>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Number Phone</th>
                            <th>Saldo</th>
                            <th>Status</th>
                            <th>Join Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Change status -->
    <div class="modal fade" tabindex="-1" role="dialog" id="modalChangeStatus">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post" id="formChangeStatus" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"></h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-horizontal">
                            <input type="hidden" id="id" name="id">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Status</label>

                                <div class="col-sm-10">
                                    <select name="status" id="status" class="form-control">
                                    	<option value="">-- Select One --</option>
                                        <option value="active">Active</option>
                                        <option value="non-active">Non Active</option>
                                    </select>
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
                    "url": "{{ route('admin.customer.index') }}",
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
                        "data": "name",
                        "orderable": true,
                    },
                    {
                        "data": "email",
                        "orderable": true,
                    },
                    {
                        "data": "phone",
                        render : function(data, type, row){
                            if (data == null) {
                                return "-";
                            } else {
                                return data;
                            }
                        },
                        "orderable": true,
                    },
                    {
                        "data": "saldo",
                        "orderable": true,
                    },
                    {
                        "data": "status",
                        "orderable": true,
                    },
                    {
                        "data": "created_at",
                        "orderable": true,
                    },
                    {
                        render : function(data, type, row){
                            return	'<a href="#" class="btn btn-xs btn-warning change-status"><i class="fa fa-pencil"></i> Change Status</a>';
                        },
                        "width": "10%",
                        "orderable": false,
                    }
                ],
                "order": [ 1, 'desc' ],
                "fnCreatedRow" : function(nRow, aData, iDataIndex) {
                    $(nRow).attr('data', JSON.stringify(aData));
                }
            });

            // change-status
            $('#data_table').on('click', '.change-status', function(e){
                $('#formChangeStatus div.form-group').removeClass('has-error');
                $('#formChangeStatus .help-block').empty();
                $('#formChangeStatus .modal-title').text("Change Status");
                $('#formChangeStatus')[0].reset();
                var aData = JSON.parse($(this).parent().parent().attr('data'));
                $('#formChangeStatus button[type=submit]').button('reset');

                url = '{{ route("admin.customer.change-status") }}';

                $('#id').val(aData.id);
                $('#status').val(aData.status);

                $('#modalChangeStatus').modal('show');
            });

            $('#formChangeStatus').submit(function (event) {
                event.preventDefault();
                $('#formChangeStatus div.form-group').removeClass('has-error');
                $('#formChangeStatus .help-block').empty();
                $('#formChangeStatus button[type=submit]').button('loading');

                var formData = new FormData($("#formChangeStatus")[0]);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData : false,
                    contentType : false,
                    cache: false,

                    success: function (response) {
                        if (response.success) {
                            table.draw();
                            $.toast({
                                heading: 'Success',
                                text : response.message,
                                position : 'top-right',
                                allowToastClose : true,
                                showHideTransition : 'fade',
                                icon : 'success',
                                loader : false
                            });

                            $('#modalChangeStatus').modal('hide');
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

                        $('#formChangeStatus button[type=submit]').button('reset');
                    },

                    error: function(response){
                        if (response.status === 422) {
                            // form validation errors fired up
                            var error = response.responseJSON.errors;
                            var data = $('#formChangeStatus').serializeArray();
                            $.each(data, function(key, value){
                                if( error[data[key].name] != undefined ){
                                    console.log(data[key].name);
                                    var elem;
                                    if( $("#formChangeStatus input[name='" + data[key].name + "']").length )
                                        elem = $("#formChangeStatus input[name='" + data[key].name + "']");
                                    else if( $("#formChangeStatus select[name='" + data[key].name + "']").length )
                                        elem = $("#formChangeStatus select[name='" + data[key].name + "']");
                                    else
                                        elem = $("#formChangeStatus textarea[name='" + data[key].name + "']");

                                    elem.parent().find('.help-block').text(error[data[key].name]);
                                    elem.parent().find('.help-block').show();
                                    elem.parent().parent().addClass('has-error');
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
                        $('#formChangeStatus button[type=submit]').button('reset');
                    }
                });
            });
        });
    </script>
@endsection
