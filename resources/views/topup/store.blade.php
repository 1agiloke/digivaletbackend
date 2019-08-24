@extends('layouts.app')

@section('header')
	<section class="content-header">
		<h1>Top Up Balance</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li>Balance</li>
			<li class="active">Top Up</li>
		</ol>
	</section>
@endsection

@section('content')
	<div class="box box-warning">
       <form action="" id="formTopUp" enctype="multipart/form-data" method="POST" autocomplete="off">
	        <div class="box-body">
                <div class="form-group">
                    <label class="control-label">Bank</label>
                    <select class="form-control" id="bank" name="bank">
                        <option value="">-- Select One --</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}"><b>{{ $bank->name }}</b> ( a/n : {{ $bank->owner }} - rek. number : {{ $bank->number }} )</option>
                        @endforeach
                    </select>
                    <span class="help-block"></span>
                </div>
                <div class="form-group">
                    <label class="control-label">Nominal</label>

                    <input class="form-control" type="text" id="nominal" name="nominal" placeholder="Enter Nominal">
                    <span class="help-block"></span>
                </div>
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

@section('js')
    <script>
        $('#formTopUp').submit(function (event) {
            $('#formTopUp div.form-group').removeClass('has-error');
            $('#formTopUp .help-block').empty();
            event.preventDefault();
            $('#formTopUp button[type=submit]').button('loading');

            var formData = new FormData($("#formTopUp")[0]);

            $.ajax({
                url: '{{ route("top-up.store") }}',
                type: 'POST',
                data: formData,
                processData : false,
                contentType : false,
                cache: false,

                success: function (response) {
                    if (!response.success) {
                        $.toast({
                            heading: 'Error',
                            text : response.message,
                            position : 'top-right',
                            allowToastClose : true,
                            showHideTransition : 'fade',
                            icon : 'error',
                            loader : false
                        });
                    } else {
                        setTimeout(function () {
                            location.href = "/top-up/transfer/" + response.id;
                        }, 2000);
                    }

                    $('#formTopUp button[type=submit]').button('reset');
                },

                error: function(response){
                    if (response.status === 422) {
                        // form validation errors fired up
                        var error = response.responseJSON.errors;
                        var data = $('#formTopUp').serializeArray();
                        $.each(data, function(key, value){
                            if( error[data[key].name] != undefined ){
                                var elem;
                                if( $("#formTopUp input[name='" + data[key].name + "']").length )
                                    elem = $("#formTopUp input[name='" + data[key].name + "']");
                                else if( $("#formTopUp select[name='" + data[key].name + "']").length )
                                    elem = $("#formTopUp select[name='" + data[key].name + "']");
                                else
                                    elem = $("#formTopUp textarea[name='" + data[key].name + "']");

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
                    $('#formTopUp button[type=submit]').button('reset');
                }
            });
        });
    </script>
@endsection
