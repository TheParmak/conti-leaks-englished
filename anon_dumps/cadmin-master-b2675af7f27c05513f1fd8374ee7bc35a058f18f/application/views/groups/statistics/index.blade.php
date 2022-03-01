<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/template/css/bootstrap.min.css">
    <link rel="stylesheet" href="/template/css/bootstrap-theme.min.css">
    <script src="/template/js/jquery-2.1.1.min.js"></script>
    <script src="/template/js/angular.min.js"></script>
    <script src="/template/js/angular-route.min.js"></script>
    <script src="/template/js/angular-animate.min.js"></script>
    <script src="/template/js/ng-google-chart.min.js"></script>
    <script src="/template/js/ui-bootstrap-tpls.min.js"></script>
    <script type="text/javascript">var app = angular.module('app', ['ngRoute', 'ui.bootstrap', 'ngAnimate', 'googlechart']);</script>
    <style>
        .chart{
            display:block;
            overflow-y: auto;
            max-height: 200px;
        }
        .chart tr {
            display:table;
            width:100%;
            table-layout:fixed;
        }
    </style>
</head>
<body ng-app="app" ng-controller="Main">
    <ng-view></ng-view>

    {{-- LOGIN --}}
    <script type="text/ng-template" id="login.html">
        <div id="login" class="input-group" ng-init="initLogin()" style="width:180px">
            <input ng-model="filter.pass" type="password" class="form-control" placeholder="Password..." autofocus="autofocus" ng-keydown="($event.which==13) ? apply() : 0;">
            <span class="input-group-btn">
                <button ng-click="apply()"  class="btn btn-default" type="button">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </span>
        </div>
    </script>

    {{-- INDEX --}}
    <script type="text/ng-template" id="index.html">
        <div class="container-fluid">
            {{-- STAT --}}
            <div id="stat" class="row-fluid">
                {{-- GEO--}}
                <div id="geo" class="col-md-6">
                    <div id="geo-chart" google-chart chart="charts.geo.chart" style="width:100%;height: 300px;"></div>
                    <table class="table table-condensed table-striped">
                        <thead class="chart">
                            <tr>
                                <th style="border-top: 2px solid #ddd;">Location</th>
                                <th style="border-top: 2px solid #ddd;">Count</th>
                                <th style="border-top: 2px solid #ddd;">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="chart">
                            <tr ng-repeat="g in charts.geo.info | orderBy:'-percent'">
                                <td>@{{ g.location }}</td>
                                <td>@{{ g.count }}</td>
                                <td>@{{ g.percent }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                {{-- SYSTEM --}}
                <div class="col-md-6">
                    <div id="system-chart" google-chart chart="charts.system.chart" style="width:100%;height: 300px;"></div>
                    <table class="table table-condensed table-striped">
                        <thead class="chart">
                        <tr>
                            <th style="border-top: 2px solid #ddd;">System</th>
                            <th style="border-top: 2px solid #ddd;">Count</th>
                        </tr>
                        </thead>
                        <tbody class="chart">
                        <tr ng-repeat="g in charts.system.info | orderBy:'-percent'">
                            <td>@{{ g.system }}</td>
                            <td>@{{ g.count }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                {{-- USER_SYSTEM --}}
                {{--<div class="col-md-4">--}}
                    {{--<div id="usersystem-chart" google-chart chart="charts.userSystem.chart" style="width:100%;height: 300px;"></div>--}}
                {{--</div>--}}
            </div>
            {{-- TABLE --}}
            <div class="row-fluid">
                <div class="pull-left">Total: @{{ bigTotalItems }}</div>
                <div class="col-md-12">
                    <table class="table table-condensed table-striped" ng-init="checkAuth()" ng-cloak>
                        <thead ng-show="showPagination()">
                            <tr>
                                <td colspan="10" class="text-center">
                                    <ul uib-pagination total-items="bigTotalItems" ng-model="$parent.bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-link-numbers="true" ng-change="getData()" items-per-page="itemsPerPage" previous-text="&laquo;" next-text="&raquo;"></ul>
                                </td>
                            </tr>
                        </thead>
                        <thead ng-if="bigTotalItems">
                            <tr>
                                <td ng-repeat="field in fields">
                                    <span ng-if="field.value" style="cursor: pointer;">
                                        <a ng-click="sort(field.value)">@{{ field.title }} <i class="glyphicon" ng-class="{'glyphicon-chevron-up' : isSortUp(field.value), 'glyphicon-chevron-down' : isSortDown(field.value)}"></i></a>
                                    </span>
                                    <span ng-if="!field.value">
                                        @{{ field.title }}
                                    </span>
                                </td>
                            </tr>
                        </thead>
                        <tbody ng-if="bigTotalItems">
                            <tr ng-repeat="d in data | orderBy:sortField:reverse">
                                <td>@{{ d.id }}</td>
                                <td>@{{ d.client }}</td>
                                <td>@{{ d.group }}</td>
                                <td>@{{ d.created_at }}</td>
                                <td>@{{ d.last_activity }}</td>
                                <td>@{{ d.sys_ver }}</td>
                                <td>@{{ d.ip }}</td>
                                <td>@{{ d.country }}</td>
                            </tr>
                        </tbody>
                        <tbody ng-if="bigTotalItems == 0">
                            <tr>
                                <td colspan="10" style="text-align: center;">
                                    <h4 class="text-danger">No records!</h4>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot ng-show="showPagination()">
                            <tr>
                                <td colspan="10" class="text-center">
                                    <ul uib-pagination total-items="bigTotalItems" ng-model="$parent.bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-link-numbers="true" ng-change="getData()" items-per-page="itemsPerPage" previous-text="&laquo;" next-text="&raquo;"></ul>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </script>
</body>
@include('TEMPLATE.js.table')
<script type="text/javascript">
    app.config(function($routeProvider){
        $routeProvider.when('/', { templateUrl : 'index.html' })
            .when('/login', { templateUrl : 'login.html' })
            .otherwise({ redirectTo: '/login' });
    });

    app.controller('Main', function($controller, $scope, $http, $location, $timeout) {
        $controller('table', {$scope: $scope});
        $scope.charts = {
            geo: { charts: {}, info: {}, url : '/rest/group/geo/' },
            system: { charts: {}, info: {}, url : '/rest/group/system/' },
//            userSystem: { charts: {}, info: {}, url : '/rest/group/usersystem/' }
        };
        $scope.reverse = false;
        $scope.sortField = 'id';
        $scope.fields = [
            {'value': 'id', 'title': '№'},
            {'value': '', 'title': 'Client'},
            {'value': 'group', 'title': 'Group'},
            {'value': 'created_at', 'title': 'CreatedAt'},
            {'value': 'last_activity', 'title': 'LastActivity'},
            {'value': 'sys_ver', 'title': 'System'},
            {'value': 'ip', 'title': 'Ip'},
            {'value': 'country', 'title': 'Country'}
        ];

        $scope.filter = {
            name : '{{ $name }}',
            pass : undefined
        };

        $scope.checkAuth = function () {
            if(!$scope.filter.pass){
                $location.path('/login');
            }
        };

        $scope.sendPost = function () {
            $scope.getStat($scope.charts.geo);
            $scope.getStat($scope.charts.system);
//            $scope.getStat($scope.charts.userSystem);
            $http.post('/rest/group/clients/' + $scope.bigCurrentPage, $scope.post).then(function (res) {
              $scope.data = res.data['data'];
              $scope.bigCurrentPage = res.data['current_page'];
              $scope.bigTotalItems = res.data['total_items'];
              $scope.itemsPerPage = res.data['items_per_page'];
              $location.path('/');
            }, function (res) {
                // $scope.errors = res.error;
            });
        };

        $scope.initLogin = function(){
            angular.element('#login').css({
                'position': 'absolute',
                'width': 200,
                'left': '50%',
                'top': '50%',
                'margin-left': -100,
                'margin-top': -angular.element('#login').height() / 2
            });
        };

        $scope.getStat = function(params){
            $http.post(params.url, $scope.post).then(function (res) {
              if(res.data.detailedStat != undefined) { params.info = res.data.detailedStat; }
              params.chart = {
                  type: "PieChart",
                  data: res.data,
                  options: {
                      title: '',
                      legend: 'none',
                      pieSliceText: 'label',
                      pieStartAngle: 100,
                      chartArea: { left: 0, top: 0, width: "100%", height: "100%" }
                  }
              };
            }, function (res) {
                // $scope.errors = res.error;
            });
        };


        // Function to replicate setInterval using $timeout service.
        $scope.intervalFunction = function(){
            $timeout(function() {
                $scope.getData();
                $scope.intervalFunction();
            }, 10000)
        };

        // Kick off the interval
        $scope.intervalFunction();
    });
</script>
</html>
