@extends('layouts.app')

@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/sweetalert.css') }}">
    <script type="text/javascript" src="{{ asset('/js/sweetalert.min.js') }}"></script>

    <div class="container" ng-controller="clients" ng-init="apply()" ng-cloak>
        <div class="row">
            <div class="col-md-offset-4 col-md-8" style="margin-bottom: 10px;">
                <button class="btn btn-xs btn-default">&nbsp;</button> - Not checked
                <button class="btn btn-xs btn-success">&nbsp;</button> - Not in blacklist
                <button class="btn btn-xs btn-danger">&nbsp;</button> - In blacklist
            </div>
            <div class="col-md-12">
                <table class="table table-condensed table-stripped table-bordered">
                    <tbody>
                        <tr ng-show="bigTotalItems">
                            <td>
                                <div class="total pull-left" ng-cloak>Total: @{{ bigTotalItems }}</div>
                            </td>
                            <td>
                                <div class="total pull-left" ng-cloak>WhiteList: @{{ count.white }}</div>
                            </td>
                            <td>
                                <div class="total pull-left" ng-cloak>BlackList: @{{ count.black }}</div>
                            </td>
                        </tr>
                    </tfoot>
                </table>

                <table class="table table-condensed table-stripped table-bordered">
                    <tbody>
                        <tr>
                            <td>
                                {!! Form::select('online', ['' => '--', 'Offline (last 3 days)', 'Online'], null, [
                                    'class' => 'selectpicker',
                                    'ng-model' => 'filter.online'
                                ]) !!}
                            </td>
                            <td>
                                {!! Form::select('whiteList', ['' => '--', 'Not in WhiteList', 'In WhiteList'], null, [
                                    'class' => 'selectpicker',
                                    'ng-model' => 'filter.whiteList'
                                ]) !!}
                            </td>
                            <td>
                                <div class="input-group">
                                  <input type="text" class="form-control" placeholder="Domain exclude" ng-model="currentDomain">
                                  <span class="input-group-btn">
                                    <button class="btn btn-danger" type="button" ng-click="updateDomains($event, currentDomain)">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                  </span>
                                </div><!-- /input-group -->
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="8">
                                <div class="btn-group btn-group-sm pull-left">
                                    {!! Form::submit('ClearAll', [
                                        'class' => 'btn btn-danger btn-sm btn-inverse',
                                        'type'  => 'button',
                                        'ng-click' => 'clearAll()',
                                    ]) !!}
                                    {!! Form::button('AddAll', [
                                        'class' => 'btn btn-success btn-sm btn-inverse',
                                        'type'  => 'button',
                                        'ng-click' => 'addAll()',
                                    ]) !!}
                                </div>
                                <span style="vertical-align: middle; margin-left: 5px; margin-top: 7px;" class="glyphicon glyphicon-question-sign text-primary" title="ClearAll - remove all clients from white list.&#xA;AddAll - add all clients to white list, if they have domain name and not in blacklist." data-toggle="tooltip" data-placement="right"></span>

                                <div class="btn-group btn-group-sm pull-right">
                                    {!! Form::submit('Reset', [
                                        'class' => 'btn btn-danger btn-sm btn-inverse',
                                        'type'  => 'button',
                                        'ng-click' => 'reset()',
                                    ]) !!}
                                    {!! Form::button('Apply', [
                                        'class' => 'btn btn-success btn-sm btn-inverse',
                                        'type'  => 'button',
                                        'ng-click' => 'apply()',
                                    ]) !!}
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                    @include('TEMPLATE.modal_delete_domain')
                </table>

                <table class="table table-condensed">
                    <tbody ng-if="bigTotalItems">
                        <tr ng-repeat="d in activeTasks" class="task-row-">
                            <td class="col-md-10" style="border-top: none;">
                                <div class="container task-line" style="width: 1100px;">
                                    <div class="progress-actions" ng-class="(getInfo(d, 'class'))">
                                        <ul>
                                            <li ng-if="typeof(d.status) != 'undefined' && d.status != null && typeof(d.queue_active) != 'undefined'" title="Complete percent">@{{ d.status.processed_pr | number:1 }}%</li>
                                            <li class="active_element" ng-click="getInfo(d, 'action', $event)" title="@{{ getInfo(d, 'title') }}"><i class="fa fa-@{{ getInfo(d, 'icon') }}" aria-hidden="true"></i></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-2"><a target="_blank" href="/edit/@{{ d.id }}">@{{ d.name }}</a></div>
                                    <div class="col-md-4">
                                        <div ng-if="d.status != null && typeof(d.queue_active) != 'undefined'" class="emails-status">
                                            <span class="good_emails" title="Good">
                                                <i class="fa fa-check" aria-hidden="true"></i> @{{ d.status.email_number_right }}
                                            </span>
                                            |
                                            <span class="bad_emails" title="Bad">
                                                <i class="fa fa-ban" aria-hidden="true"></i> @{{ d.status.email_number_fail }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div ng-if="d.status != null && typeof(d.queue_active) != 'undefined'" class="emails-status-total">
                                            <span class="emails-total" title="Processed">
                                                <i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i> @{{ d.status.processed }}
                                            </span>
                                            |
                                            <span class="emails-total" title="Total">
                                                <i class="fa fa-globe" aria-hidden="true"></i> @{{ d.status.size }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="btn-group btn-group-sm pull-right" style="margin-top: 10px;">
                                        <button ng-if="d.status != null && getSize(d.status) > 0" class="btn btn-info btn-md" title="Statistic" ng-click="openModalStatistic(d)">
                                            <span class="glyphicon glyphicon-stats"></span>
                                        </button>
                                        <button class="btn btn-danger" ng-click="openModalDelete($event, d.id, d.name)">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>

        @include('TEMPLATE.modal_delete')
        @include('TEMPLATE.modal_statistic')
        @include('TEMPLATE.modal_start_task')
        @include('TEMPLATE.modal_delete_task_from_queue')
                </table>

                <table class="table table-condensed table-stripped table-bordered">
                    <tbody ng-if="bigTotalItems">
                        <tr>
                            <td></td>
                            <td>ClientID</td>
                            <td>IP</td>
                            <td>Country</td>
                            <td>OS</td>
                            <td>LastActivity</td>
                            <td>Domain</td>
                            <td>Right</td>
                            <td>Fail</td>
                            <td>Response</td>
                            <td>Sent</td>
                            <td>Tasks</td>
                        </tr>
                        <tr ng-repeat="d in data | orderBy:sortField:reverse" ng-class="{'alert alert-danger': (d.invalid || d.valid == false), 'alert alert-success': d.valid == true}">
                            <td style="width: 1px;">
                                {{--<input type="checkbox" ng-change="setWhiteList(d.base64_full)" ng-model="d.white_list">--}}
                                <input type="checkbox" ng-change="setWhiteList(d.base64)" ng-model="d.white_list">
                            </td>
                            <td>
                                <a href="/get_info/?client=@{{ d.base64 }}">@{{ d.base64 }}</a>
                            </td>
                            <td ng-if="d.ip">@{{ d.ip }}</td>
                            <td ng-if="d.country" style="width: 1px;white-space: nowrap;">@{{ d.country }}</td>
                            <td ng-if="d.os" style="white-space: nowrap">@{{ d.os }}</td>
                            <td ng-if="d.ago" style="white-space: nowrap">@{{ d.ago }}</td>
                            <td ng-if="d.domain != undefined" style="white-space: nowrap">
                                <span ng-if="d.domain">@{{ d.domain }}</span>
                                <span ng-if="!d.domain">&nbsp;</span>
                            </td>
                            <td>@{{ d.email_right }}</td>
                            <td>@{{ d.email_fail }}</td>
                            <td>@{{ d.email_response }}</td>
                            <td>@{{ d.email_sent }}</td>
                            <td>@{{ d.task_count }}</td>
                            <td ng-if="d.invalid" colspan="4" style="font-weight: bold;text-align: right">
                                Invalid
                            </td>
                        </tr>
                    </tbody>
                    @include('TEMPLATE.pagination')
                </table>
            </div>
        </div>
        <div id="footerWhiteList" ng-show="checked" style="margin-top:40px;">
            <nav class="container navbar navbar-default navbar-fixed-bottom" role="navigation">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse">
                        <div class="nav navbar-nav pull-right">
                            <p class="navbar-text">Update <b>@{{ whiteList.length }}</b> clients?</p>
                            <button class="btn btn-success navbar-btn" ng-click="sendWhiteList()">
                                <span class="glyphicon glyphicon-ok"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    @include('TEMPLATE.js.table')
    <script type="text/javascript">
        app.controller('clients', function($controller, $scope, $http, $timeout, $filter, blockUIConfig, blockUI){
            $controller('table', {$scope: $scope});
            blockUIConfig.autoBlock = false;
            $scope.timerClientsUpdate = null;
            $scope.count = {
                black: 0,
                white: 0
            };
            $scope.timeUpdate = 30000;
            $scope.checked = false;
            $scope.whiteList = [];
//            $scope.reverse = true;
//            $scope.sortField = 'id';
//            $scope.fields = [
//                {'value': '', 'title': '#'},
//                {'value': '', 'title': 'ClienID'}
//            ];
            $scope.deleteDomain = null;
            $scope.deleteDomainInput = null;

            $scope.sendPost = function(){
                $http.post('/api/clients/online?page=' + $scope.bigCurrentPage, $scope.post).then(function(data){
                    $scope.data = data.data['data'];
                    $scope.bigCurrentPage = data.data['current_page'];
                    $scope.bigTotalItems = data.data['total_items'];
                    $scope.itemsPerPage = data.data['items_per_page'];
                    $scope.sendPostGetActiveTasks();
                });
            };


            /**********************************Active tasks****************************/
            $scope.sendPostGetActiveTasks = function() {
                $http.get("{{ route('api_task_queue_active', null) }}").then(function (data){
                    $scope.activeTasks = data.data;
                    $('.selectpicker').selectpicker('refresh')
                });
            }

            $scope.sendPostGetActiveTasks();

            $scope.getSize = function (data) { return Object.keys(data).length };
            $scope.getInfo = function(d, type, e = null) {

                switch(type) {
                    case "class":
                        if (d.status != null && d.queue_active) return 'task-active'
                        else if(d.status != null && !d.queue_active) return 'task-stopped';
                        else return 'task-deleted';
                    break;

                    case "title":
                        if (d.status != null && d.queue_active) return 'Stop task'
                        else if(d.status != null && !d.queue_active) return 'Delete task from queue';
                        else return 'Start task';
                    break;

                    case "icon": 
                        if (d.status != null && d.queue_active) return 'stop'
                        else if(d.status != null && !d.queue_active) return 'trash';
                        else return 'play';
                    break;

                    default: 
                        if (d.status != null && d.queue_active) stopTask(e, d);
                        else if(d.status != null && !d.queue_active) openModalDeleteQeueu(e, d);
                        else openModalStartTask(e, d);
                    break;
                }

                
            }

            function stopTask (e, d) {
                $http.get("{{ route('api_task_queue_stop', null) }}/" + d.id).then(function(res) {
                    d.queue_active = false;
                })
            };

            function openModalDeleteQeueu(e, d){
                $scope.modalDeleteItemQueueId = d.id;
                $scope.modalDeleteItemQueue = d.name;
                $scope.modalDeleteItemQueueData = $(e.target).parents("tr:eq(0)");
                $('#modaLDeleteFromQueue').modal('show');
            };

            function openModalStartTask(e, d) {
                $scope.modalStartData = d;
                $scope.modalDeleteItemQueueData = $(e.target).parents("tr:eq(0)");
                $('#modalStartTaskQueue').modal('show');
            };


            $scope.openModalStatistic = function(d) {
                $scope.modalStatisticData = d;
                $('#modalStatistic').modal('show');
            };

            $scope.openModalDelete = function(e, id, item){
                $scope.deleteRow = $(e.target).parents("tr:eq(0)");
                $scope.modalDeleteItemId = id;
                $scope.modalDeleteItem = item;
                $('#modalDelete').modal('show');
            };


            $scope.modalSendDelete = function () {
                $http.get("{{ route('api_tasks_delete', null) }}/" + $scope.modalDeleteItemId).then(function (){
                    $('#modalDelete').modal('hide');
                    $scope.deleteRow.remove();
                });
            };

            $scope.modalSendStartTask = function (start) {
                $http.get("{{ route('api_task_queue_add', null) }}/" + $scope.modalStartData.id + (start == 0 || start == null || typeof(start) == 'undefined' ? "" : "/" + start)).then(function (){
                    $('#modalStartTaskQueue').modal('hide');
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(0)").children("ul").children("li:eq(0)").html('<i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>');
                });
            };

            $scope.modalSendDeleteQeueu = function () {
                $http.get("{{ route('api_task_queue_delete', null) }}/" + $scope.modalDeleteItemQueueId).then(function(res) {
                    $('#modaLDeleteFromQueue').modal('hide');
                    test = $scope.modalDeleteItemQueueData;
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(2)").remove();
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(2)").remove();
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(2)").children("button:eq(0)").remove();
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(0)").removeClass("task-stopped").addClass("task-deleted");
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(0)").children("ul").children("li:eq(0)").hide();
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(0)").children("ul").children("li:eq(1)").attr("title", "Start task");
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(0)").children("ul").children("li:eq(1)").html('<i class="fa fa-play" aria-hidden="true"></i>');
                    $scope.modalDeleteItemQueueData.children("td").children("div").children("div:eq(0)").children("ul").children("li:eq(1)").show();
                })
            };
            /**************************************************************************/

            $scope.updateDomains = function(e, dom)
            {
                $scope.deleteDomain = dom;
                $scope.deleteDomainInput = $(e.target);
                $("#modalDeleteDomain").modal("show");
            }

            $scope.modalSendDeleteDomain = function() {
                $("#modalDeleteDomain").modal("hide");

                $scope.deleteDomainInput.html('<i class="fa fa-spin fa-spinner" aria-hidden="true"></i>');
                $scope.deleteDomainInput.parent().parent().children("input").attr("disabled", "disabled");
                $scope.deleteDomainInput.attr("disabled", "disabled");
            
                 $http.post('/api/clients/clear?domain=' + $scope.deleteDomain).then(function(){
                    $scope.deleteDomainInput.html('<span class="glyphicon glyphicon-trash"></span>');
                    $scope.deleteDomainInput.parent().parent().children("input").removeAttr("disabled");
                    $scope.deleteDomainInput.parent().parent().children("input").val("");
                    $scope.deleteDomainInput.removeAttr("disabled");
                    $scope.deleteDomainInput = null;
                    $scope.deleteDomain = null;
                    $scope.currentDomain = null;
                });
            }

            $scope.setWhiteList = function (base64) {
                $timeout.cancel($scope.timerClientsUpdate);
                $scope.checked = true;
                if($filter('filter')($scope.whiteList, base64).length){
                    $scope.whiteList.splice($scope.whiteList.indexOf(base64), 1);
                }else{
                    $scope.whiteList.push(base64);
                }

                if ($scope.whiteList.length == 0) $scope.checked = false;
            };

            $scope.clearAll = function () {
                blockUI.start();
                $http.get('{{ route('white_list_clear_all') }}').then(function(){
                    $scope.getData();
                    blockUI.stop();
                });
            };

            $scope.addAll = function () {
                blockUI.start();
                $timeout.cancel($scope.timerClientsUpdate);
                $http.get('{{ route('white_list_add_all') }}').then(function(){
                    $scope.getData();
                    $scope.intervalFunction();
                    blockUI.stop();
                });
            };

            $scope.sendWhiteList = function () {
                $timeout.cancel($scope.timerClientsUpdate);
                swal({
                    title: "Saving...",
                    text: "Please wait... We save changes.",
                    showConfirmButton: false
                });

                blockUI.start();
                $http.post('/api/clients/set_white_list', $scope.whiteList).then(function(){
                    $scope.whiteList = [];
                    $scope.intervalFunction();
                    $scope.checked = false;
                    blockUI.stop();

                    swal({
                        title: "Complete",
                        text: "We saved changes",
                        type: "success",
                        timer: 1000,
                        showConfirmButton: false
                    });
                });
            };

            $scope.resetBefore = function(){
                $('.selectpicker').val(null).selectpicker('refresh');
                $("#whiteList").val(null).trigger('change.select2');
                // todo work auto in develop, but not in production
                $('.select2-selection__clear').remove();
            };

            $scope.$watchCollection('data', function () {
                $scope.count = {black: 0, white: 0};
                angular.forEach($scope.data, function(val) {
                    if(val.valid == false){
                        $scope.count.black++;
                    } else if(val.valid == true){
                        $scope.count.white++;
                    }
                });
            });

            $scope.intervalFunction = function(){
                $scope.timerClientsUpdate = $timeout(function() {
                    $scope.getData();
                    $scope.intervalFunction();
                }, $scope.timeUpdate);
            };

            $scope.intervalFunction();
        });
    </script>
@endsection
