<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" href="/template/css/bootstrap.min.css">
    <link rel="stylesheet" href="/template/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/template/css/darkstrap.min.css">
    <link rel="stylesheet" href="/template/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="/template/css/datepicker.css">
    <link rel="stylesheet" href="/template/css/datepicker3.css">
    <link rel="stylesheet" href="/template/css/bootstrap-datetimepicker.min.css" />
    <link rel="stylesheet" href="/template/css/select2.min.css">
    <link rel="stylesheet" href="/template/css/pace.css">
    <link rel="stylesheet" href="/template/css/font-awesome.css">
    <link rel="stylesheet" href="/template/css/my.css?v=1.0.3">
    <!-- Libs -->
    <script src="/template/js/polyfill.js"></script>
    <script src="/template/js/jquery-2.1.1.min.js"></script>
    <script src="/template/js/moment.min.js"></script>
    <script src="/template/js/locales.min.js"></script>
    <script src="/template/js/bootstrap.min.js"></script>
    <script src="/template/js/bootstrap-datepicker.js"></script>
    <script src="/template/js/bootstrap-select.min.js"></script>
    <script src="/template/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/template/js/select2.min.js"></script>
    <script src="/template/js/pace.min.js" data-pace-options='{"ajax":{"trackMethods":["GET","POST"]},"startOnPageLoad":false}'></script>
    <script src="/template/js/angular.min.js"></script>
    <script src="/template/js/angular-animate.min.js"></script>
    <script src="/template/js/checklist-model.js"></script>
    <script src="/template/js/ui-bootstrap-tpls.min.js"></script>
    <script src="/template/js/jquery.blockUI.js"></script>
    <script src="/template/js/common-functions.js"></script>
    <script type="text/javascript">var app = angular.module('app', ['ui.bootstrap', 'checklist-model', 'ngAnimate']);</script>
</head>
<body ng-app="app">
    <?=$navbar?>
    <div class="container-fluid">
        <div class="row-fluid">
            <?=$content?>
        </div>
    </div>
</body>
</html>
