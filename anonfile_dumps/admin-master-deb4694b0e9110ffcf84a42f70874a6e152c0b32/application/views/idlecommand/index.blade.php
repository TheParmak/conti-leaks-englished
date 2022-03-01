<div ng-controller="idleCommands">
    {!! Form::open() !!}
    <table class="table table-striped table-condensed">
        @if($idle->count() > 10)
            <thead>
                <tr>
                    <td colspan="15">
                        <input type="checkbox" ng-click="selectAll($event)">&nbsp; <small>Select all</small></input>
                        <div class="btn-group btn-group-sm pull-right">
                            {!! Form::button('stop', '<span class="glyphicon glyphicon-stop"></span>', [
                                'class' => 'btn btn-inverse btn-warning',
                                'type' => 'button',
                                'ng-disabled' => '!multipleAvaible',
                                'ng-click' => 'massStop()',
                            ]) !!}
                            {!! Form::button('delete', '<span class="glyphicon glyphicon-trash"></span>', [
                                'class' => 'btn btn-inverse btn-danger',
                                'type' => 'button',
                                'ng-disabled' => '!multipleAvaible',
                                'ng-click' => 'massDel()',
                            ]) !!}
                            <a href="/idlecommand/editor/" class="btn btn-success btn-inverse">
                                <span class="glyphicon glyphicon-plus"></span>
                            </a>
                            <button type="button" class="btn btn-danger btn-xs btn-inverse" ng-disabled="!multipleAvaible" ng-click="massRefresh()">
                                <span class="glyphicon glyphicon-refresh"></span>
                            </button>
                            @if(isset($_GET['params_as']))
                                <a href="/idlecommand/" class="btn btn-success btn-inverse">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            </thead>
        @endif
        <thead>
            <tr>
                <td style="width: 1px;">
                    <span class="glyphicon glyphicon-check"></span>
                </td>
                <td>Count</td>
                <td>GroupInclude</td>
                <td>GroupExclude</td>
                <td>System</td>
                <td>Location</td>
                <td>ImportanceLow</td>
                <td>ImportanceHigh</td>
                <td>UserDefinedLow</td>
                <td>UserDefinedHigh</td>
                <td>Incode</td>
                <td>Params</td>
                <td>Timer</td>
                <td></td>
            </tr>
        </thead>
        <tbody style="word-break: break-all;">
            @foreach($idle as $item)
                <tr>
                    <td>
                        <input class="itemSelector" type="checkbox" value="{{ $item->id }}" ng-click="checkHandler($event, {{ $item->id }})" style="margin-top: 0">
                    </td>
                    <td>{{ $item->count }}</td>
                    <td>{{ $item->group_include }}</td>
                    <td>{{ $item->group_exclude }}</td>
                    <td>{{ $item->sys_ver }}</td>
                    <td>
                        {{ $item->country_1 }}
                        {{ $item->country_2 }}
                        {{ $item->country_3 }}
                        {{ $item->country_4 }}
                        {{ $item->country_5 }}
                        {{ $item->country_6 }}
                        {{ $item->country_7 }}
                    </td>
                    <td>{{ $item->importance_low }}</td>
                    <td>{{ $item->importance_high }}</td>
                    <td>{{ $item->userdefined_low }}</td>
                    <td>{{ $item->userdefined_high }}</td>
                    <td>{{ $item->incode }}</td>
                    <td>
                        {!! $item->getUriForFilter() !!}
                    </td>
                    <td>{{ $item->timer }}</td>
                    <td>
                        <div class="btn-group pull-right" role="group">
                            <a href="/idlecommand/editor/{{ $item->id }}" class="btn btn-primary btn-xs btn-inverse">
                                <span class="glyphicon glyphicon-edit"></span>
                            </a>
                            <!-- TODO need rewrite all page to angular! -->
                            <button type="button" class="btn btn-danger btn-xs btn-inverse" ng-click="makeRefresh('{{ $item->id }}')">
                                <span class="glyphicon glyphicon-refresh"></span>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="15">
                    <input type="checkbox" ng-click="selectAll($event)">&nbsp; <small>Select all</small></input>
                    <div class="btn-group btn-group-sm pull-right">
                        {!! Form::button('stop', '<span class="glyphicon glyphicon-stop"></span>', [
                            'class' => 'btn btn-inverse btn-warning',
                            'type' => 'submit',
                            'ng-disabled' => '!multipleAvaible',
                            'ng-click' => 'massStop()',
                        ]) !!}
                        {!! Form::button('delete', '<span class="glyphicon glyphicon-trash"></span>', [
                            'class' => 'btn btn-inverse btn-danger',
                            'type' => 'button',
                            'ng-disabled' => '!multipleAvaible',
                            'ng-click' => 'massDel()',
                        ]) !!}
                        <a href="/idlecommand/editor/" class="btn btn-success btn-inverse">
                            <span class="glyphicon glyphicon-plus"></span>
                        </a>
                        <button type="button" class="btn btn-danger btn-xs btn-inverse" ng-disabled="!multipleAvaible" ng-click="massRefresh()">
                            <span class="glyphicon glyphicon-refresh"></span>
                        </button>
                        @if(isset($_GET['params_as']))
                            <a href="/idlecommand/" class="btn btn-success btn-inverse">
                                Reset
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    {!! Form::close() !!}

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">@{{ modalCaption }}</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group form-group-sm">
                        <label for="count" class="col-sm-2 control-label">Count</label>
                        <div class="col-sm-10">
                            <input class="form-control" ng-model="refreshCount">
                        </div>
                    </div>

                    <div class="form-group form-group-sm" style="margin-top: 35px;">
                        <label for="count" class="col-sm-2 control-label">Timer</label>
                        <div class="col-sm-10">
                            <input class="form-control" ng-model="refreshTimer">
                        </div>
                    </div>

                    <div class="form-group form-group-sm" style="margin-top: 70px;">
                        <label class="col-sm-2 control-label">
                            Location <span class="glyphicon glyphicon-question-sign text-primary" title="You can specify multiple locations by separating them using space symbol, for example, &quot;DE FR IT ES&quot;." data-toggle="tooltip" data-placement="right"></span>
                        </label>
                        <div class="col-sm-10 form-inline">
                            {!! Form::input('country_1', Arr::get($_POST, 'country_1', '*'), [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'refreshCountry1',
                            ]) !!}
                            {!! Form::input('country_2', Arr::get($_POST, 'country_2'), [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'refreshCountry2',
                            ]) !!}
                            {!! Form::input('country_3', Arr::get($_POST, 'country_3'), [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'refreshCountry3',
                            ]) !!}
                        </div>
                        <div class="col-sm-offset-2 col-sm-10 form-inline" style="margin-top: 2px;">
                            {!! Form::input('country_4', Arr::get($_POST, 'country_4'), [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'refreshCountry4',
                            ]) !!}
                            {!! Form::input('country_5', Arr::get($_POST, 'country_5'), [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'refreshCountry5',
                            ]) !!}
                            {!! Form::input('country_6', Arr::get($_POST, 'country_6'), [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'refreshCountry6',
                            ]) !!}
                        </div>
                        <div class="col-sm-offset-2 col-sm-10 form-inline" style="margin-top: 2px;">
                            {!! Form::input('country_7', Arr::get($_POST, 'country_7'), [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'refreshCountry7',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="create" title="Create" ng-click="sendPost()">Create</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    app.controller('idleCommands', function($controller, $scope, $http, $window){
        $scope.refreshId = 0;
        $scope.refreshCount = '';
        $scope.refreshCountry1 = '*';
        $scope.refreshTimer = 0;
        $scope.modalCaption = 'Refresh idle command';
        $scope.multipleAvaible = false;
        $scope.checkedItems = [];

        angular.element(document).ready(function () {
            let checkbox_massive = $('.itemSelector');
            if (checkbox_massive.length >0){
                angular.forEach(checkbox_massive, function(value, index){
                    if(value.checked){
                        $scope.multipleAvaible = true;
                        $scope.checkedItems.push(String(value.value)); }
                });
            }
        });

        $scope.selectAll = function (e) {
            $scope.checkedItems = [];
            let current_target = e.target || e.currentTarget;
            if (current_target.checked) {
                $('.itemSelector').each(function() {
                    this.checked = true;
                    $scope.multipleAvaible = true;
                    $scope.checkedItems.push(String(this.value));
                });
            }else{
                $('.itemSelector').each(function(){
                    this.checked = false;
                    $scope.multipleAvaible = false;
                });
            }
        };

        $scope.makeRefresh = function (value) {
            $scope.refreshId = value;
            $scope.modalCaption = 'Refresh idle command';
            $('#myModal').modal('show');
        };

        $scope.sendPost = function(){
            $http.post('/rest/idlecommands/refresh/', {
                'id': $scope.refreshId,
                'count': $scope.refreshCount,
                'timer': $scope.refreshTimer,
                'country_1': $scope.refreshCountry1,
                'country_2': $scope.refreshCountry2,
                'country_3': $scope.refreshCountry3,
                'country_4': $scope.refreshCountry4,
                'country_5': $scope.refreshCountry5,
                'country_6': $scope.refreshCountry6,
                'country_7': $scope.refreshCountry7
            }).success(function (){
                $window.location.reload();
            });
        };

        $scope.checkHandler = function(e, id){
            let current_target = e.target || e.currentTarget;
            if (current_target.checked) {
                $scope.multipleAvaible = true;
                $scope.checkedItems.push(String(id));
            }
            else {
                let id_index = $scope.checkedItems.indexOf(String(id));
                $scope.checkedItems.splice(id_index, 1);
                if ($scope.checkedItems.length == 0){
                    $scope.multipleAvaible = false;
                }
            }
        };

        $scope.massStop = function(){
            $http.post('/rest/idlecommands/stop/', {
                'ids': $scope.checkedItems
            }).success(function (){
                $window.location.reload();
            });
        };

        $scope.massDel = function(){
            $http.post('/rest/idlecommands/del/', {
                'ids': $scope.checkedItems
            }).success(function (){
                $window.location.reload();
            });
        };

        $scope.massRefresh = function(){
            // Actually this cycle don't need, because we have checkHandler
            // But to avoid glitches we check selected again here
            $scope.checkedItems = [];
            let checkbox_massive = $('.itemSelector');
            if (checkbox_massive.length >0){
                angular.forEach(checkbox_massive, function(value, index){
                    if(value.checked){ $scope.checkedItems.push(value.value); }
                });
                $scope.refreshId = $scope.checkedItems;
                $scope.modalCaption = 'Refresh idle command for '+$scope.checkedItems.length+' items';
                $('#myModal').modal('show');
            }
        };
    });
</script>
