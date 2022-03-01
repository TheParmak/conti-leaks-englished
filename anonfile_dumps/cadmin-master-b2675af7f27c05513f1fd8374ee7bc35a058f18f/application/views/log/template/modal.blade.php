<div ng-controller="bcTool">
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Create command</h4>
                </div>
                {!! Form::open() !!}
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <td>
                                Client
                            </td>
                            <td>
                                {!! Form::input('client_id', $client->id, ['class' => 'form-control', 'readonly']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Code
                            </td>
                            <td>
                                {!! Form::input('incode', null, ['id' => 'command', 'class' => 'form-control', 'type' => 'number', 'required']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Params
                            </td>
                            <td>
                                {!! Form::input('params', null, ['class' => 'form-control']) !!}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-danger btn-inverse" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-inverse" name="create" title="Create">Create</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalServer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Select Server</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead style="font-weight: bold">
                        <tr>
                            <td style="width: 1px;"></td>
                            <td>
                                Name
                            </td>
                            <td>
                                Ip
                            </td>
                            <td>
                                Port
                            </td>
                        </tr>
                        </thead>
                        <tbody id="myForm">
                            @foreach($servers as $server)
                                <tr>
                                    <td>
                                        {!! Form::input('radioName', $server->id, ['type' => 'radio']) !!}
                                    </td>
                                    <td>
                                        {{ $server->ip }}:{{ $server->port }}
                                    </td>
                                    <td>
                                        {{ $server->ip }}
                                    </td>
                                    <td>
                                        {{ $server->port }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-body" id="output"></div>
                <div class="modal-footer">
                    <div class="btn-group btn-group-sm pull-right">
                        <button type="button" class="btn btn-primary btn-inverse" id="GetAddr" disabled ng-click="getAddr()">GetAddr</button>
                        <button type="button" class="btn btn-danger btn-inverse" data-dismiss="modal">Close</button>
                    </div>
                    <div class="input-group">
                        <span class="input-group-btn btn-group-sm">
                            <button type="button" class="btn btn-primary" id="MapPort" disabled ng-click="mapPort()">MapPort</button>
                        </span>
                        <input style="width: 100px" id="portForMapPort" type="text" class="form-control" placeholder="Port">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    app.controller('bcTool', function($scope, $rootScope, $http){
        $scope.outputField = $('#output');

        $scope.blockUI = function () {
            $.blockUI({
                message: '<h1 style="color:black;">Loading...</h1>',
                baseZ: 20000,
                zIndex: 20000
            });
        };

        $scope.getAddr = function(){
            $scope.outputField.empty();
            $scope.blockUI();
            var post = {
                'client': "{{ $client->getClientID() }}",
                'server': $('input[name=radioName]:checked', '#myForm').val()
            };
            $scope.getData(post, 'getAddr');
        };

        $scope.mapPort = function(){
            $scope.outputField.empty();
            $scope.blockUI();
            var post = {
                'client': "{{  $client->getClientID() }}",
                'server': $('input[name=radioName]:checked', '#myForm').val(),
                'port': $('#portForMapPort').val()
            };
            $scope.getData(post, 'mapPort');
        };

        $scope.errorCallback = function(response){
            if(response.data == null){
                response.data = 'Timeout'
            }
            $scope.outputField.empty();
            $scope.outputField.append('<div class="alert alert-error">'+response.data+'</div>');
        };

        $scope.successCallback = function(response){
            console.log("success post callback on modal.blade.php");
            $scope.outputField.empty();
            $scope.outputField.append('<ul>');
            $.each(response.data, function(key, value) {
                $scope.outputField.append('<li>'+value+'</li>');
            });
            $scope.outputField.append('</ul>');
        };

        $scope.completeCallback = function(){
            $.unblockUI();
        };

        $scope.getData = function (post, method) {
            $http.post('/rest/log/'+method, post, {timeout: 3000})
                .then($scope.successCallback, $scope.errorCallback)
                .finally($scope.completeCallback);
        }
    });

    $('input[name=radioName]').change(function(){
        if ($(this).is(':checked') ) {
            $('#GetAddr').prop('disabled', false);
            $('#MapPort').prop('disabled', false);
        }
    });
</script>
