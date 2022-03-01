@extends('layouts.app')

@section('content')
    <style>
        [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
            display: none !important;
        }
    </style>
    <div class="container" ng-cloak>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading"></div>

                    <div class="panel-body" ng-controller="sendmail" ng-init="init()">
                        <div class="col-md-12" ng-if="errorMsg" ng-cloak>
                            <div ng-repeat="m in errorMsg" class="alert alert-danger">
                                @{{ m }}
                            </div>
                        </div>
                        <form name="taskForm">
                            <table class="table table-condensed form-horizontal">
                                <tr>
                                    <td colspan="2">
                                        {{ Form::text('name', $task->name,  [
                                            'class' => 'form-control',
                                            'placeholder' => 'Name',
                                            'ng-model' => 'task_name'
                                        ]) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="form-group" style="margin-bottom: 0">
                                            <label class="col-sm-2 control-label">EmailList</label>
                                            <div class="col-sm-10">
                                                {{ Form::select('email_list', $emails_list, $task->email_list,  [
                                                    'class' => 'form-control selectpicker',
                                                    'ng-model' => 'email_list',
                                                    'ng-options' => 'key as value for (key , value) in emails_list',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="form-group" style="margin-bottom: 0">
                                            <label class="col-sm-2 control-label">Or send to test email</label>
                                            <div class="col-sm-10">
                                                <div style="margin-bottom: 10px;" ng-repeat="mail in test_email" class="mails-row">
                                                    <input type="text" class="form-control" ng-model="mail.mail" style="padding-right: 35px;" >

                                                    <button class="btn btn-xs btn-danger pull-right" style="position: absolute; right: 0; margin-top: -28px; margin-right: 20px;" ng-click="dropMail(mail.id)">
                                                        <i class="fa fa-minus" aria-hidden="true"></i>
                                                    </button>
                                                    <button class="btn btn-xs btn-success pull-right" style="position: absolute; right: 0; margin-top: -28px; margin-right: 20px;" ng-click="addMail()">
                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <style>
                                        .mails-row:not(:last-child) > .btn-danger {
                                            display: block;
                                        }

                                        .mails-row:not(:last-child) > .btn-success {
                                            display: none;
                                        }


                                        .mails-row:last-child > .btn-danger {
                                            display: none;
                                        }

                                        .mails-row:last-child > .btn-success {
                                            display: block;
                                        }
                                    </style>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="form-group" style="margin-bottom: 0">
                                            <label class="col-sm-2 control-label">Email</label>
                                            <div class="col-sm-10">
                                                {{ Form::select('email_id', $emails, $task->email_id,  [
                                                    'class' => 'form-control selectpicker',
                                                    'ng-model' => 'email_id',
                                                    'ng-options' => 'key as value for (key , value) in emails',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <td>
                                <td colspan="2">
                                    <button type="button" class="btn btn-success pull-right" ng-click="createGroup()" ng-disabled="taskId == 0"><i class="fa fa-plus" aria-hidden="true"></i> Create group</button>
                                </td>
                                <tr>
                                    <td colspan="2">
                                        <div class="panel panel-default" ng-repeat="group in groups track by $index" ng-init="$indexGroup = $index">
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" ng-model="group.name" name="group" required placeholder="Group name"/>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <select class="form-control" ng-model="group.type" name="type">
                                                            <option value="@{{ item.id }}" ng-repeat="item in [{id:0,name:'All'},{id:1,name:'Random'}]">@{{ item.name }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-danger btn-small" style="padding:0;width: 25px;height: 25px; margin-top: 5px;" ng-click="removeGroup($index)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel-body">
                                                <h5 ng-if="!group.id">Before adding file please save changes!</h5>
                                                <div ng-if="group.id">
                                                    <table class="table table-dark">
                                                        <tbody>
                                                        <tr ng-repeat="file in group.files">
                                                            <th scope="row" style="text-align: center;line-height: 35px;">@{{ $index + 1 }}</th>
                                                            <td style="width: 100%;"><a href="/api/emails/read/@{{ taskId }}/@{{ file.name }}" style="display: block; padding-top: 7px;">@{{ file.name }}</a></td>
                                                            <td><button type="button" class="btn btn-danger btn-small" style="padding:0;width: 25px;height: 25px; margin-top: 5px;" ng-click="deleteFile($indexGroup, $index)">-</button></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                    <button type="button" class="btn btn-success pull-right" ng-click="createFile(group.id)" style="padding:0 4px;height: 25px; margin-top: 5px;"><i class="fa fa-file" aria-hidden="true"></i> Add file</button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                {{--<tr ng-show="taskId">
                                    <td colspan="2">
                                        <form name="groupsForm">
                                            <h4 align="center">Groups</h4>
                                            <table class="table table-dark">
                                                <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col" style="width: 1px;">#</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col" style="width: 1px;"><button type="button" class="btn btn-success btn-small" style="padding:0;width: 25px;height: 25px;" ng-click="createGroup()">+</button></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="group in groups track by $index">
                                                    <th scope="row" style="text-align: center;line-height: 35px;">@{{ $index + 1 }}</th>
                                                    <td style="width: 50%;"><input type="text" class="form-control" ng-model="group.name" name="group" required /></td>
                                                    <td style="width: 50%;">
                                                        <select class="form-control" ng-model="group.type" name="type">
                                                            <option value="@{{ item.id }}" ng-repeat="item in [{id:0,name:'All'},{id:1,name:'Random'}]">@{{ item.name }}</option>
                                                        </select>
                                                    </td>
                                                    <td><button type="button" class="btn btn-danger btn-small" style="padding:0;width: 25px;height: 25px; margin-top: 5px;" ng-click="removeGroup($index)">-</button></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </form>
                                    </td>
                                </tr>
                                <tr ng-show="groups.length && taskId">
                                    <td colspan="2">
                                        <form name="filesForm">
                                            <h4 align="center">Files</h4>
                                            <table class="table table-dark">
                                                <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col" style="width: 1px;">#</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Group</th>
                                                    <th scope="col" style="width: 1px;"><button type="button" class="btn btn-success btn-small" style="padding:0 3px;height: 25px;" ng-click="createFile()">+ (@{{ uploader.queue.length }})</button></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="file in files track by $index">
                                                    <th scope="row" style="text-align: center;line-height: 35px;">@{{ $index + 1 }}</th>
                                                    <td style="width: 50%;">@{{ file.name }}</td>
                                                    <td style="width: 50%;">
                                                        <select class="form-control" ng-model="group.group" name="type">
                                                            <option value="@{{ group.name }}" ng-repeat="group in groups" selected>@{{ group.name }}</option>
                                                        </select>
                                                    </td>
                                                    <td><button type="button" class="btn btn-danger btn-small" style="padding:0;width: 25px;height: 25px; margin-top: 5px;" ng-click="removeFile($index)">-</button></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            {{ Form::file('file[]', [
                                                 'uploader' => 'uploader',
                                                 'nv-file-select' => '',
                                                 'multiple',
                                                 'ng-show' => 'false'
                                             ]) }}
                                        </form>
                                    </td>
                                </tr>--}}
                                {{--@foreach($files as $file)
                                    <tr>
                                        <td><a target="_blank" href="/api/emails/read/{{ $file }}">{{ basename($file) }}</a></td>
                                        <td>
                                            <button class="btn btn-xs btn-danger pull-right" ng-click="deleteFile($event, '{{ $file }}')">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach--}}
                                {{--@foreach($files as $file)
                                    <tr>
                                        <td><a target="_blank" href="/api/emails/read/{{ $file }}">{{ basename($file) }}</a></td>
                                        <td>
                                            <button class="btn btn-xs btn-danger pull-right" ng-click="deleteFile($event, '{{ $file }}')">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2">

                                        <p>Queue length: @{{ uploader.queue.length }}</p>
                                    </td>
                                </tr>--}}
                                <tr>
                                    <td colspan="2">
                                        <button class='btn btn-success pull-right' type="button" ng-click="genTask()" ng-disabled="taskForm.$invalid">
                                            <span class="glyphicon glyphicon-ok"></span> Save
                                        </button>
                                        <div class="btn-group" role="group">
                                            <button class='btn btn-primary' type="button" ng-click="execTask($event)" ng-disabled="!watch_email || taskForm.$invalid">
                                                <span class="glyphicon glyphicon-play"></span> Execute
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr ng-show="false">
                                    <td colspan="2">
                                        {{ Form::file('file[]', [
                                             'uploader' => 'uploader',
                                             'nv-file-select' => '',
                                             'multiple'
                                         ]) }}
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        app.controller('sendmail', function($scope, $http, FileUploader, $window, blockUI, blockUIConfig){
            $scope.taskId = {{ $id ?: 0 }};
            $scope.errorMsg = {};

            /* TODO */
            $scope.task_name = {!! json_encode($task->name) !!};
            $scope.test_email = {!! $task->test_email ? $task->test_email : "['']" !!};
            $scope.emails = {!! json_encode($emails) !!};
            $scope.email_id = {!! json_encode($task->email_id) !!};
            $scope.emails_list = {!! json_encode($emails_list) !!};
            $scope.groups = {!! empty($files) ? "[]" : \json_encode($files) !!};
            $scope.email_list = '{{ $task->email_list }}';
            $scope.init_watch_email = false;
            $scope.watch_email = true;

            $scope.filesToGroup = 'untracking';

            $scope.init = function () {
                let mails = [];

                for (let i = 0; i < $scope.test_email.length; i++)
                {
                    mails.push({id: i, mail: $scope.test_email[i]});
                }

                $scope.test_email = mails;
            };

            $scope.uploader = new FileUploader({
                url: '/api/emails/upload',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                removeAfterUpload: false
            });

            $scope.uploader.onBeforeUploadItem = function(fileItem) {
                blockUIConfig.message = 'Upload files..';
                fileItem.formData.push({
                    id: $scope.taskId,
                    group: $scope.filesToGroup
                });
            };

            $scope.uploader.onAfterAddingFile = function (item) {

                $scope.groups.find(item => item.id == $scope.filesToGroup).files.push({
                    name: item.file.name
                });

                $scope.uploader.uploadAll();
            };

            $scope.uploader.onCompleteAll = function(items) {
                blockUIConfig.message = 'Generate Task..';
                const data = {
                    groups: $scope.groups
                };
                $http.post('/api/emails/gen/'+$scope.taskId, data).then(function(){
                    blockUI.stop();
                    if($window.location.href == '/edit/' + $scope.taskId){
                        $window.location.reload();
                    }else{
                        $window.location.href = '/edit/' + $scope.taskId;
                    }
                }, function(){
                    blockUI.stop();
                });
            };

            $scope.$watchGroup(['email_list', 'email_id'], function() {
                if(!$scope.init_watch_email && $scope.watch_email){
                    $scope.init_watch_email = true;
                }else{
                    $scope.watch_email = false;
                }

                $('.selectpicker').selectpicker('refresh');
            });

            $scope.deleteFile = function(group, id){
                $scope.groups[group]['files'].splice(id, 1);
            };

            $scope.genTask = function () {
                let mails = [];
                for (let i = 0; i < $scope.test_email.length; i++)
                {
                    if (mails.filter(item => item === $scope.test_email[i].mail).length == 0) mails.push($scope.test_email[i].mail);
                }

                if (mails[0] == "") mails = null;

                blockUIConfig.message = 'Loading..';
                blockUI.start();
                var data = {
                    email_list: $scope.email_list,
                    email_id: $scope.email_id,
                    test_email: mails == null ? null : JSON.stringify(mails),
                    name: $scope.task_name,
                    groups: $scope.groups,
                    files: $scope.files
                };
                $http.post('/api/emails/create{{ $id ? '/'.$id : '' }}', data).then(function(data){
                    $scope.taskId = data.data.id;
                    $scope.uploader.uploadAll();
                    if(!$scope.uploader.queue.length){
                        $scope.uploader.onCompleteAll();
                    }
                    $scope.watch_email = true;
                }, function (data) {
                    $scope.errorMsg = data.data;
                    blockUI.stop();
                });
            };

            $scope.execTask = function (e) {
                blockUIConfig.message = 'Execute Task..';
                $(e.target).attr("disabled", "disabled");
                $(e.target).html('<span class="glyphicon glyphicon-play"></span> Executing...');
                $http.get("{{ route('api_task_queue_add', null) }}/" + $scope.taskId + ($scope.uploader.queue.length == 0 || $scope.uploader.queue.length == null || typeof($scope.uploader.queue.length) == 'undefined' ? "" : "/" + $scope.uploader.queue.length)).then(function (){
                    $scope.successMsg = "Execute success!";
                    $(e.target).html('<span class="glyphicon glyphicon-play"></span> Execute success!');
                    $(e.target).removeAttr("disabled");
                    blockUI.stop();
                }, function(){
                    $scope.errorMsg = {'Error': 'Error execute task'};
                    $(e.target).html('<span class="glyphicon glyphicon-play"></span> Execute error!');
                    $(e.target).removeAttr("disabled");
                    blockUI.stop();
                });
            };

            $scope.addMail = function () {
                if($scope.test_email[$scope.test_email.length - 1].mail != "") {
                    $scope.test_email.push({id: $scope.test_email.length, mail: ""});
                }
            };

            $scope.dropMail = function (id) {
                for (let i = 0; i < $scope.test_email.length; i++)
                {
                    id != $scope.test_email[i].id || $scope.test_email.splice(i, 1);
                }
            };

            // -------------------------------------------------- FILES ------------------------------------------------

            $scope.createGroup = function () {
                const group = {
                    name: '',
                    type: '0',
                    files: []
                };

                $scope.groups.push(group);
            };

            $scope.removeGroup = function (i) {
                $scope.groups.splice(i, 1);
            };

            $scope.createFile = function (group) {
                $scope.filesToGroup = group;
                $("input[uploader='uploader']").click();
            }
        });
    </script>
@endsection
