@extends('layouts.app')

@section('content')
    <div class="container" ng-controller="blackList">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Config BlackList</li>
                </ol>

                @if (Session::has('success'))
                    <div class="alert alert-success">
                        {!! Session::get('success') !!}
                    </div>
                @endif

                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif

                {{ Form::open() }}
                <div class="panel panel-default">
                    <div class="panel-heading">BlackList</div>
                    <div class="panel-body">
                        <table class="table table-condensed form-horizontal">
                            <tr>
                                <td>
                                    <div class="col-md-12">
                                        <div class="form-group form-group-sm">
                                            <div class="input-group" ng-repeat="value in json track by $index">
                                                <input name="config[]" type="text" class="form-control" ng-model="value" ng-change="changeInput($index, value)">
                                                <span class="input-group-btn ">
                                                <button class="btn btn-sm btn-danger" type="button" ng-click="delField(value)">
                                                    <span class="glyphicon glyphicon-minus"></span>
                                                </button>
                                                <button ng-show="$last" class="btn btn-sm btn-success" type="button" ng-click="addField()">
                                                    <span class="glyphicon glyphicon-plus"></span>
                                                </button>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class='btn btn-success pull-right' type="submit">
                                        <span class="glyphicon glyphicon-ok"></span> Save
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>

    </div>

    <style>
        .form-group, .table{
            margin-bottom: 0;
        }
        .panel-heading{
            font-weight: bold;
        }

        .control-label{
            white-space:nowrap;
        }
    </style>

    <script type="text/javascript">
        app.controller('blackList', function($scope){
            $scope.json = JSON.parse({!! json_encode($config) !!});

            $scope.addField = function(){
                $scope.json.push('');
            };

            $scope.changeInput = function (index, value) {
                $scope.json[index] = value;
            };

            $scope.$watchCollection('json', function () {
                if(!$scope.json.length){
                    $scope.addField();
                }
            });

            $scope.delField = function(item){
                $scope.json.splice($scope.json.indexOf(item), 1);
            }
        });
    </script>
@endsection
