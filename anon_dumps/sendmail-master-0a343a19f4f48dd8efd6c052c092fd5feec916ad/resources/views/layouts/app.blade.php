<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/bootstrap-theme.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/angular-block-ui.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/bootstrap-select.min.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/font-awesome.css') }}" rel="stylesheet">
        <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
        <script src="{{ asset('/js/jquery.min.js') }}"></script>
        <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('/js/bootstrap-select.min.js') }}"></script>
        <script src="{{ asset('/js/angular.min.js') }}"></script>
        <script src="{{ asset('/js/angular-animate.min.js') }}"></script>
        <script src="{{ asset('/js/ui-bootstrap-tpls.min.js') }}"></script>
        <script src="{{ asset('/js/angular-file-upload.min.js') }}"></script>
        <script src="{{ asset('/js/angular-block-ui.min.js') }}"></script>
        <script type="text/javascript">var app = angular.module('app', ['ui.bootstrap', 'ngAnimate', 'angularFileUpload', 'blockUI']);</script>
    </head>
    <body ng-app="app">
        @include('navbar')
        @yield('content')
    </body>
</html>
