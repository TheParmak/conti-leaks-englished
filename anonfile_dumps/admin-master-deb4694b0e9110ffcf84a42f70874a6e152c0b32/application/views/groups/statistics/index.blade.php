<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/template/css/bootstrap.min.css">
    <link rel="stylesheet" href="/template/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/template/css/datepicker.css">
    <link rel="stylesheet" href="/template/css/datepicker3.css">
    <link rel="stylesheet" href="/template/css/select2.min.css">

    <script src="/template/js/jquery-2.1.1.min.js"></script>
    <script src="/template/js/angular.min.js"></script>
    <script src="/template/js/angular-route.min.js"></script>
    <script src="/template/js/angular-animate.min.js"></script>
    <script src="/template/js/ng-google-chart.min.js"></script>
    <script src="/template/js/ui-bootstrap-tpls.min.js"></script>
    <script src="/template/js/bootstrap-datepicker.js"></script>
    <script src="/template/js/select2.min.js"></script>

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
    <div class="container-fluid" ng-init="initIndex()">
        {{-- STAT --}}
        <div id="stat" class="row-fluid">
            {{-- GEO--}}
            <div id="geo" class="col-md-4">
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
            <div class="col-md-4">
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
            {{-- GROUP --}}
            <div class="col-md-4">
                <div id="system-chart" google-chart chart="charts.group.chart" style="width:100%;height: 300px;"></div>
                <table class="table table-condensed table-striped">
                    <thead class="chart">
                    <tr>
                        <th style="border-top: 2px solid #ddd;">Group</th>
                        <th style="border-top: 2px solid #ddd;">Count</th>
                    </tr>
                    </thead>
                    <tbody class="chart">
                    <tr ng-repeat="g in charts.group.info | orderBy:'-percent'">
                        <td>@{{ g.group }}</td>
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
            <div class="col-md-12">

                <br>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-11">
                                <div class="row">
                                    <div class="col-md-1">
                                        Date:
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-daterange input-group input-group-sm datepicker-range">
                                            <input type="text" name="events_start" class="form-control input-sm" ng-model="miniFilter.start" ng-change="changeFirstDate()" placeholder="Date From"/>
                                            <span class="input-group-addon">/</span>
                                            <input type="text" name="events_end" class="form-control input-sm" ng-model="miniFilter.end" placeholder="Date To"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        Last Activity:
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::select('last_activity', $lastactivity_options, null, [
                                            'class' => 'form-control input-sm',
                                            'ng-model' => 'miniFilter.last_activity',
                                            'ng-selected' => '$index==1'
                                        ]) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <input type="checkbox" name="importanceCheckbox" ng-model="miniFilter.importance" id="importanceCheckbox">
                                        <label for="importanceCheckbox">Importance</label>
                                    </div>
                                </div>

                                <div class="row"><div class="col-md-12"> &nbsp; </div></div>

                                <div class="row">
                                    <div class="col-md-1">
                                        Group:
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::select('groups', $groups, null, [
                                            'id' => 'groups',
                                            'class' => 'form-control input-sm',
                                            'ng-model' => 'miniFilter.groups',
                                            'multiple' => '',
                                        ]) !!}
                                    </div>
                                    <div class="col-md-1">
                                        IP:
                                    </div>
                                    <div class="col-md-3">
                                        {!! Form::input('ip', null, [
                                            'class' => 'form-control input-sm',
                                            'ng-model' => 'miniFilter.ip',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class=row>
                                    Total: @{{ bigTotalItems }}
                                </div>
                                <div class="row"><div class="col-md-12"> &nbsp; </div></div>
                                <div class=row>
                                    <button ng-click="updateMiniFilter()" ng-disabled="dataLoading">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                        <td ng-bind="d.id"></td>
                        <td ng-bind="d.client"></td>
                        <td ng-bind="d.group"></td>
                        <td ng-bind="d.created_at"></td>
                        <td ng-bind="d.last_activity"></td>
                        <td ng-bind="d.sys_ver"></td>
                        <td ng-bind="d.ip"></td>
                        <td ng-bind="d.country"></td>
                        <td ng-bind="d.importance"></td>
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

@include('TEMPLATE.spinner', ['show_trigger_name' => 'dataLoading'])

</body>

@include('TEMPLATE.js.table')

<script type="text/javascript">
    app.config(function($routeProvider){
        $routeProvider.when('/', { templateUrl : 'index.html' })
            .when('/login', { templateUrl : 'login.html' })
            .otherwise({ redirectTo: '/login' });
    });

    app.controller('Main', function($controller, $scope, $http, $location, $timeout, $interval) {
        $controller('table', {$scope: $scope});
        $scope.charts = {
            geo: { charts: {}, info: {}, url : '/rest/group/geo/' },
            system: { charts: {}, info: {}, url : '/rest/group/system/' },
            group: { charts: {}, info: {}, url : '/rest/group/group/' },
//            userSystem: { charts: {}, info: {}, url : '/rest/group/usersystem/' }
        };
        $scope.dataLoading = false;
        $scope.reverse = false;
        $scope.sortField = null;
        $scope.fields = [
            {'value': 'id', 'title': '№'},
            {'value': '', 'title': 'Client'},
            {'value': 'group', 'title': 'Group'},
            {'value': 'created_at', 'title': 'CreatedAt'},
            {'value': 'last_activity', 'title': 'LastActivity'},
            {'value': 'sys_ver', 'title': 'System'},
            {'value': 'ip', 'title': 'Ip'},
            {'value': 'country', 'title': 'Country'},
            {'value': 'importance', 'title': 'Importance'}
        ];

        $scope.filter = {
            name : '{{ $name }}',
            pass : undefined,
            last_activity: '10',
        };

        $scope.miniFilter = {
            last_activity: '10',
            start: '',
            end: '',
            isImportance: false,
        };

        $scope.checkAuth = function () {
            if(!$scope.filter.pass){
                $location.path('/login');
            }
        };

        $scope.getStat = function(params){
            return $http.post(params.url, $scope.post).success(function(data){
                if(data.detailedStat != undefined)
                    params.info = data.detailedStat;

                params.chart = {
                    type: "PieChart",
                    data: data,
                    options: {
                        title: '',
                        legend: 'none',
                        pieSliceText: 'label',
                        pieStartAngle: 100,
                        chartArea: { left: 0, top: 0, width: "100%", height: "100%" }
                    }
                };
            });
        };

        // TODO! REWRITE FCKN PROMISE!
        $scope.sendPost = function(){
            var getGeoPromise = $scope.getStat($scope.charts.geo);
            getGeoPromise.then(function() {
                var getSystemsPromise = $scope.getStat($scope.charts.system);
                getSystemsPromise.then(function() {
                    var getGroupsPromise = $scope.getStat($scope.charts.group)
                    getGroupsPromise.then(function() {
                        $http.post('/rest/group/clients/' + $scope.bigCurrentPage, $scope.post).success(function (data) {
                            $scope.data = data['data'];
                            $scope.bigCurrentPage = data['current_page'];
                            $scope.bigTotalItems = data['total_items'];
                            $scope.itemsPerPage = data['items_per_page'];
                            $location.path('/');
                            $scope.dataLoading = false;
                        });
                    });
                });
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

        $scope.initIndex = function () {
            $('.datepicker-range > input').datepicker({
                language: "ru",
                autoclose: true,
                todayHighlight: true,
                format: "yyyy/mm/dd"
            });

            $('#groups').select2({
                placeholder: "Group..",
                tags: true,
                allowClear: true,
                tokenSeparators: [',', ' ']
            }).data('select2').$container.addClass("input-sm").css('padding', 0);
        };

        // check filter
        $scope.changeFirstDate = function(){
            if (!$scope.miniFilter.end.length) {
                $scope.miniFilter.end = $scope.miniFilter.start;
            }
        };

        $scope.updateMiniFilter = function(){
            $scope.dataLoading = true;
            $scope.post = $.extend($scope.post, $scope.miniFilter);
            console.log($scope.post);
            $scope.sendPost();
        }
    });
</script>
</html>
