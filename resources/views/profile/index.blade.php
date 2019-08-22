@extends('layouts.app')

@section('header')
	<section class="content-header">
		<h1>
		Profile
		<small>Merchant</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li class="active"><a href="#"> Profile</a></li>
		</ol>
	</section>
@endsection

@section('content')
	<div class="row">
		<div class="col-md-4">
			<div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">Data Profile</h3>
                </div>
				<div class="box-body box-profile">
                    <img class="profile-user-img img-responsive img-circle" src="{{ asset('images/avatar_default.png') }}">

                    <h3 class="profile-username text-center">{{ $data->name }}</h3>

                    <ul class="list-group list-group-unbordered">
						<li class="list-group-item">
							<b>Email</b> <p class="pull-right">{{ $data->email }}</p>
						</li>
						<li class="list-group-item">
							<b>Nomor Handphone</b>
							<p class="pull-right">
								@if( $data->phone != null )
									{{ $data->phone }}
								@else
									-
								@endif
							</p>
						</li>
					</ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#settings" data-toggle="tab">Setting</a></li>
					<li><a href="#password" data-toggle="tab">Change Password</a></li>
                </ul>

                <div class="tab-content">
					<!-- setting -->
					<div class="active tab-pane" id="settings">
						<form class="form-horizontal" method="POST" id="formSetting">
							<div class="form-group">
								<label class="col-sm-3 control-label">Name</label>

								<div class="col-sm-9">
									<input type="text" class="form-control" id="name" name="name" placeholder="Enter Name">

									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Phone Number</label>

								<div class="col-sm-9">
									<input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone Number">

									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
			                    <div class="col-sm-offset-3 col-sm-9">
			                      	<button type="submit" class="btn btn-danger"><i class="fa fa-save"></i> Save</button>
			                    </div>
		                  	</div>
						</form>
					</div>
                    <!-- end setting -->

                    <!-- password -->
					<div class="tab-pane" id="password">
						<form class="form-horizontal" method="POST" id="formPassword">
							<div class="form-group">
								<label class="col-sm-4 control-label">Current Password</label>
								<div class="col-sm-8">
									<input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter Current Password">

									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">New Password</label>

								<div class="col-sm-8">
									<input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter New Password">

									<span class="help-block"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Confirm New Password</label>

								<div class="col-sm-8">
									<input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" placeholder="Confirm New Password">

									<span class="help-block"></span>
								</div>
							</div>

							<div class="form-group">
			                    <div class="col-sm-offset-4 col-sm-8">
			                      	<button type="submit" class="btn btn-danger"><i class="fa fa-save"></i> Save</button>
			                    </div>
		                  	</div>
						</form>
					</div>
                    <!-- password -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#formSetting #name').val("{{ $data->name }}");
            $('#formSetting #phone').val("{{ $data->phone }}");
            $('#formSetting').submit(function (event) {
                event.preventDefault();
    		 	$('#formSetting button[type=submit]').button('loading');
    		 	$('#formSetting div.form-group').removeClass('has-error');
    	        $('#formSetting .help-block').empty();

    		 	var _data = $("#formSetting").serialize();

    		 	$.ajax({
                    url: "{{ route('profile.change-setting', ['id' => $data->id]) }}",
                    method: 'POST',
                    data: _data,
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
                                location.reload();
                            }, 2000);
                        } else {
                            $.toast({
                                heading: 'Error',
                                text : response.message,
                                position : 'top-right',
                                allowToastClose : true,
                                showHideTransition : 'fade',
                                icon : 'error',
                                loader : false,
                                hideAfter: 5000
                            });
                        }

                        $('#formSetting')[0].reset();
                        $('#formSetting button[type=submit]').button('reset');
                    },
                    error: function(response){
                    	if (response.status === 422) {
                            // form validation errors fired up
                            var error = response.responseJSON.errors;
                            var data = $('#formSetting').serializeArray();
                            $.each(data, function(key, value){
                                if( error[data[key].name] != undefined ){
                                    var elem;
                                    if( $("#formSetting input[name='" + data[key].name + "']").length )
                                        elem = $("#formSetting input[name='" + data[key].name + "']");
                                    else if( $("#formSetting textarea[name='" + data[key].name + "']").length )
                                        elem = $("#formSetting textarea[name='" + data[key].name + "']");
                                    else
                                        elem = $("#formSetting select[name='" + data[key].name + "']");
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
                        $('#formSetting button[type=submit]').button('reset');
                    }
                });
    		});

            $('#formPassword').submit(function (event) {
    			event.preventDefault();
    		 	$('#formPassword button[type=submit]').button('loading');
    			$('#formPassword div.form-group').removeClass('has-error');
    	        $('#formPassword .help-block').empty();

    		 	var _data = $("#formPassword").serialize();

    		 	$.ajax({
                    url: "{{ route('profile.change-password', ['id' => $data->id]) }}",
                    method: 'POST',
                    data: _data,
                    cache: false,

                    success: function (response) {
                        if ( response.success ) {
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
    	                        location.reload();
    	                    }, 2000);
                        }
                        else{
                        	$.toast({
                                heading: 'Error',
                                text : response.message,
                                position : 'top-right',
                                allowToastClose : true,
                                showHideTransition : 'fade',
                                icon : 'error',
                                loader : false,
                                hideAfter: 5000
                            });
                        }

                        $('#formPassword button[type=submit]').button('reset');
                        $('#formPassword')[0].reset();
                    },

    					error: function(response){
                    	if (response.status === 422) {
                            // form validation errors fired up
                            var error = response.responseJSON.errors;
                            var data = $('#formPassword').serializeArray();
                            $.each(data, function(key, value){
                                if( error[data[key].name] != undefined ){
                                    var elem = $("#formPassword input[name='" + data[key].name + "']").length ? $("#formPassword input[name='" + data[key].name + "']") : $("#formPassword select[name='" + data[key].name + "']");
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
                        $('#formPassword button[type=submit]').button('reset');
                	}
                });
    		});
        });
    </script>
@endsection
