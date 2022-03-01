<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="stylesheet" href="/template/css/bootstrap.min.css">
        <link rel="stylesheet" href="/template/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="/template/css/bootstrap-select.min.css">
        <link rel="stylesheet" href="/template/css/datepicker.css">
        <link rel="stylesheet" href="/template/css/datepicker3.css">
        <link rel="stylesheet" href="/template/css/select2.min.css">
        <link rel="stylesheet" href="/template/css/pace.css">
        <link rel="stylesheet" href="/template/css/my.css?v=1.0.3">
        <!-- Libs -->
        <script src="/template/js/polyfill.js"></script>
        <script src="/template/js/jquery-2.1.1.min.js"></script>
        <script src="/template/js/bootstrap.min.js"></script>
        <script src="/template/js/bootstrap-datepicker.js"></script>
        <script src="/template/js/bootstrap-select.min.js"></script>
        <script src="/template/js/jsapi.js"></script>
        <script src="/template/js/jquery.blockUI.js"></script>
        <script src="/template/js/select2.min.js"></script>
        <script src="/template/js/angular.min.js"></script>
        <script src="/template/js/pace.min.js" data-pace-options='{"ajax":{"trackMethods":["GET","POST"]},"startOnPageLoad":false}'></script>
    </head>
    <body ng-app>
        @yield('navbar')
        <div class="container-fluid">
            <div class="row-fluid">
                @yield('content')
            </div>
        </div>
    </body>
</html>