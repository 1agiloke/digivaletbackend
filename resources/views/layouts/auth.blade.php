<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  @yield('title')
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="{{ mix('/css/app.css') }}">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            @yield('header')
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            @yield('content')
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

<!-- JS -->
<script src="{{ mix('/js/app.js') }}"></script>

</body>
</html>
