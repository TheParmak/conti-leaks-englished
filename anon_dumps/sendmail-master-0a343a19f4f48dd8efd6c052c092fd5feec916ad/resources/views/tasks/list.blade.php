@extends('layouts.app')

@section('content')
    <div class="container" ng-controller="tasks" ng-init="apply()" ng-cloak>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <td colspan="4">
                                <a class="btn btn-sm btn-success pull-right" href="/edit/"><span class="glyphicon glyphicon-plus"></span> New</a>
                            </td>
                        </tr>
                    </thead>
                    <tbody ng-if="bigTotalItems">
                        <tr ng-repeat="d in data" class="task-row-">
                            <td class="col-md-12" style="border-top: none;">
                                <div class="container task-line">
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
                    @include('TEMPLATE.pagination')
                </table>
            </div>
        </div>

        @include('TEMPLATE.modal_delete')
        @include('TEMPLATE.modal_statistic')
        @include('TEMPLATE.modal_start_task')
        @include('TEMPLATE.modal_delete_task_from_queue')
    </div>

    @include('TEMPLATE.js.table')
    <script type="text/javascript">
    var test ;
        app.controller('tasks', function($controller, $scope, $http, $timeout, blockUIConfig){
            $controller('table', {$scope: $scope});
            blockUIConfig.autoBlock = false;
            $scope.progress = {};
            $scope.modalDeleteName  = 'Delete task?';
            $scope.modalDeleteItemId = null;
            $scope.modalDeleteItem = null;
            $scope.deleteRow = null;
            $scope.modalDeleteItemQueueData = null;

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

            $scope.sendPost = function(){
                $http.get('/api/tasks?page=' + $scope.bigCurrentPage).then(function(data){
                    $scope.data = data.data['data'];
                    $scope.progress = data.data['progress'];
                    $scope.bigCurrentPage = data.data['current_page'];
                    $scope.bigTotalItems = data.data['total_items'];
                    $scope.itemsPerPage = data.data['items_per_page'];
                });
            };

            $scope.intervalFunction = function(){
                $timeout(function() {
                    $scope.getData();
                    $scope.intervalFunction();
                }, 10000)
            };

            $scope.intervalFunction();
        });
    </script>
@endsection
