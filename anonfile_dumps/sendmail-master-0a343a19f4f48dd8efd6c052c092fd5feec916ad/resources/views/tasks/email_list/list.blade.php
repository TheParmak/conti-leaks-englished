@extends('layouts.app')

@section('content')
    <div class="container" ng-controller="emailList" ng-init="apply()" ng-cloak>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-condensed table-bordered">
                    <thead>
                        <tr>
                            <td colspan="4">
                                <a class="btn btn-sm btn-success pull-right" href="{{ route('email_list_edit') }}"><span class="glyphicon glyphicon-plus"></span> New</a>
                            </td>
                        </tr>
                    </thead>
                    <thead style="font-weight: bold;">
                        <tr>
                            <td>EmailList</td>
                            <td>Result</td>
                            <td colspan="2">Count [Good/Bad]</td>
                        </tr>
                    </thead>
                    <tbody ng-if="bigTotalItems">
                        <tr ng-repeat="d in data">
                            <td class="col-md-3" colspan="@{{ d.result ? 1 : 2 }}">
                                @{{ d.email }}
                            </td>
                            <td ng-if="d.result">
                                <a target="_blank" href="/api/emails/download_result/@{{ d.base64 }}/result">Result</a>
                                <a target="_blank" href="/api/emails/download_result/@{{ d.base64 }}/result.email">ResultEmail</a>
                                <a target="_blank" href="/api/emails/download_result/@{{ d.base64 }}/result.err">ResultErr</a>
                            </td>
                            <td>
                                @{{ d.count }} [@{{ d.good }}/@{{ d.bad }}]
                            </td>
                            <td style="width: 130px;">
                                <div class="btn-group btn-group-xs pull-right">
                                    <button ng-class="{disabled: backEndStatus === false}" class='btn btn-success' type="button" ng-click="resolveTask(d.base64)">
                                        <span class="glyphicon glyphicon-play"></span> Resolve
                                    </button>
                                    <button class="btn btn-primary" ng-click="openModalDownload(d.base64)">
                                        <span class="glyphicon glyphicon-download-alt"></span>
                                    </button>
                                    <button class="btn btn-danger" ng-click="openModalDelete($event, d.base64, d.email)">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    @include('TEMPLATE.pagination')
                </table>
            </div>
        </div>

        <!-- Modal download -->
        <div class="modal fade" id="modalDownload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="myModalLabel">Upload email_list to FTP</h4>
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
                        <button type="submit" class="btn btn-success" name="create" title="Create" ng-click="sendPostDownload()">
                            <span class="glyphicon glyphicon-download-alt"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('TEMPLATE.modal_delete')
    </div>

    @include('TEMPLATE.js.table')
    <script type="text/javascript">
        app.controller('emailList', function($controller, $scope, $http, $timeout, blockUIConfig, blockUI){
            $controller('table', {$scope: $scope});
            blockUIConfig.autoBlock = false;
            $scope.errorMsg = false;
            $scope.filter = {};
            $scope.modalDeleteName  = 'Delete email list ?';
            $scope.modalDeleteItem = null;
            $scope.modalDeleteItemBase64 = null;
            $scope.deleteRow = null;
            $scope.backEndStatus = undefined;

            $scope.openModalDownload = function(base64){
                $scope.filter.name = base64;
                $('#modalDownload').modal('show');
            };

            $scope.openModalDelete = function(e, base64, email){
                $scope.deleteRow = $(e.target).parent().parent().parent();
                $scope.modalDeleteItemBase64 = base64;
                $scope.modalDeleteItem = email;
                $('#modalDelete').modal('show');
            };

            $scope.modalSendDelete = function () {
                $http.get('{{ route('api_email_list_delete', null) }}/' + $scope.modalDeleteItemBase64).then(function (){
                    $('#modalDelete').modal('hide');
                    $scope.deleteRow.remove();
                });
            };

            $scope.sendPostDownload = function(){
                $http.post('{{ route('api_email_list_download') }}', $scope.filter).then(function (){
                    $('#modalDownload').modal('hide');
                }, function (data) {
                    $scope.errorMsg = data.data.error;
                });
            };

            $scope.sendPost = function(){
                $http.get('{{ route('api_email_list') }}?page=' + $scope.bigCurrentPage).then(function(data){
                    $scope.data = data.data['data'];
                    console.log($scope.data);
                    $scope.bigCurrentPage = data.data['current_page'];
                    $scope.bigTotalItems = data.data['total_items'];
                    $scope.itemsPerPage = data.data['items_per_page'];
                });
            };

            $scope.resolveTask = function (base64) {
                $scope.backEndStatus = false;
                blockUIConfig.message = 'Resolve Task..';
                $http.get('{{ route("emails_resolve", null) }}/' + base64).then(function(){
                    $scope.successMsg = "Run resolve success!";
                    blockUI.stop();
                }, function(){
                    $scope.errorMsg = {'Error': 'Error resolve'};
                    blockUI.stop();
                });
            };

            $scope.statusBack = function () {
                $http.get('/api/tasks/status').then(function (data) {
                    $scope.backEndStatus = data.data.status;
                });
            };

            $scope.intervalFunction = function(){
                $timeout(function() {
                    $scope.statusBack();
                    $scope.getData();
                    $scope.intervalFunction();
                }, 10000)
            };

            $scope.getData();
            $scope.intervalFunction();
        });
    </script>
@endsection
