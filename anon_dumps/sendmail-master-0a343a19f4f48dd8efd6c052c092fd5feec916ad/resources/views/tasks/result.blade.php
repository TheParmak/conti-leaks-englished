@extends('layouts.app')

@section('content')
    <div class="container" ng-controller="result">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed table-bordered">
                    <thead>
                        <tr>
                            <td>Task</td>
                            <td colspan="3">ResultFiles</td>
                            <td colspan="2">Good / Bad</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="d in data">
                            <td>
                                @{{ d.task }}
                            </td>
                            <td>
                                <a target="_blank" href="/api/emails/download_result/@{{ d.id }}/result">Result</a>
                            </td>
                            <td>
                                <a target="_blank" href="/api/emails/download_result/@{{ d.id }}/result.err">ResultErr</a>
                            </td>
                            <td>
                                <a target="_blank" href="/api/emails/download_result/@{{ d.id }}/result.email">ResultEmail</a>
                            </td>
                            <td>
                                @{{ d.good }} / @{{ d.bad }}
                            </td>
                            <td style="width: 60px;">
                                <div class="btn-group btn-group-xs pull-right">
                                    <button class="btn btn-primary" ng-click="openModal(d.id)">
                                        <span class="glyphicon glyphicon-download-alt"></span>
                                    </button>
                                    <button class="btn btn-danger" ng-click="openModalDelete($event, d.id, d.task)">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">Upload resolve result to FTP</h4>
                    </div>
                    <div class="modal-body">
                        <div ng-if="errorMsg" class="alert alert-danger">@{{ errorMsg }}</div>
                        {!! Form::text('ftp', null, [
                            'class' => 'form-control',
                            'ng-model' => 'filter.ftp',
                            'placeholder' => 'ftp://login:pass@127.0.0.1/file.txt',
                        ]) !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            <span class="glyphicon glyphicon-remove"></span>
                        </button>
                        <button type="submit" class="btn btn-success" name="create" title="Create" ng-click="sendPost()">
                            <span class="glyphicon glyphicon-download-alt"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('TEMPLATE.modal_delete')

    </div>

    <script>
        app.controller('result', function($scope, $http, blockUIConfig, $timeout){
            blockUIConfig.autoBlock = false;
            $scope.timeUpdate = 10000;
            $scope.errorMsg = false;
            $scope.filter = {};
            $scope.modalDeleteName  = 'Delete result ?';
            $scope.modalDeleteItemId = null;
            $scope.modalDeleteItem = null;
            $scope.deleteRow = null;

            $scope.sendPost = function(){
                $http.post('/api/tasks/result_downloader', $scope.filter).then(function (){
                    $('#myModal').modal('hide');
                }, function (data) {
                    $scope.errorMsg = data.data.error;
                });
            };

            $scope.getData = function(){
                $http.get('/api/tasks/result/').then(function(data){
                    $scope.data = data.data;
                });
            };

            $scope.modalSendDelete = function () {
                $http.get('{{ route('api_result_delete', null) }}/' + $scope.modalDeleteItemId).then(function (){
                    $('#modalDelete').modal('hide');
                    $scope.deleteRow.remove();
                });
            };

            $scope.openModalDelete = function(e, id, item){
                $scope.deleteRow = $(e.target).parent().parent().parent();
                $scope.modalDeleteItemId = id;
                $scope.modalDeleteItem = item;
                $('#modalDelete').modal('show');
            };

            $scope.openModal = function(id){
                $scope.filter.id = id;
                $('#myModal').modal('show');
            };

            $scope.intervalFunction = function(){
                $scope.timerClientsUpdate = $timeout(function() {
                    $scope.getData();
                    $scope.intervalFunction();
                }, $scope.timeUpdate)
            };

            $scope.getData();
            $scope.intervalFunction();
        });
    </script>
@endsection
