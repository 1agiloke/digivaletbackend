@extends('layouts.app')

@section('header')
	<section class="content-header">
		<h1>Transfer Balance</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
			<li>Balance</li>
			<li class="active">Transfer</li>
		</ol>
	</section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">

        </div>
        <div class="col-md-4">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <center>
                        <h3 class="box-title">
                            Transfer Information
                        </h3>
                    </center>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <h3><b>Name Bank</b></h3>
                                <p>
                                    {{ $data->bank->name }}
                                </p>
                            </center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <h3><b>Owner Bank</b></h3>
                                <p>
                                    {{ $data->bank->owner }}
                                </p>
                            </center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <h3><b>Rekening Number</b></h3>
                                <p>
                                    {{ $data->bank->number }}
                                </p>
                            </center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <h3><b>Nominal</b></h3>
                                <h4 style="color:red;">
                                    Rp. {{ intval($data->nominal) + intval($data->unique_code) }}
                                </h4>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">

        </div>
    </div>
@endsection
