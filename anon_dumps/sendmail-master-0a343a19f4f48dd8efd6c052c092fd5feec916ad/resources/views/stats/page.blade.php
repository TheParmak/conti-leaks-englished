@extends('layouts.app')

@section('content')
    <div class="container" ng-controller="stats">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Statistics</div>

                    <div class="panel-body">
                        <table class="table table-condensed table-bordered">
                            <tr style="background-color: #d9edf7;font-weight: bold;">
                                <td colspan="7">Resolve</td>
                            </tr>
                            <tr>
                                <td>EmailFail</td>
                                <td>EmailRight</td>
                                <td>InProcess</td>
                                <td>InProcess %</td>
                                <td>Processed</td>
                                <td>Processed %</td>
                                <td>Size</td>
                            </tr>
                            <tr>
                                <td>
                                    @{{ resolve.email_number_fail }}
                                </td>
                                <td>
                                    @{{ resolve.email_number_right }}
                                </td>
                                <td>
                                    @{{ resolve.in_process }}
                                </td>
                                <td>
                                    @{{ resolve.in_process_pr }} %
                                </td>
                                <td>
                                    @{{ resolve.processed }}
                                </td>
                                <td>
                                    @{{ resolve.processed_pr }} %
                                </td>
                                <td>
                                    @{{ converter(resolve.size, 3) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        app.controller('stats', function($controller, $scope, $http, $timeout, blockUIConfig){
            blockUIConfig.autoBlock = false;
            $scope.resolve = {};
            $scope.timeUpdate = 10000;

            $scope.converter = function(bytes, precision) {
                if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) return '-';
                if (typeof precision === 'undefined') precision = 1;
                var units = ['bytes', 'kB', 'MB', 'GB', 'TB', 'PB'],
                        number = Math.floor(Math.log(bytes) / Math.log(1024));
                return (bytes / Math.pow(1024, Math.floor(number))).toFixed(precision) +  ' ' + units[number];
            };

            $scope.getResolveData = function(){
                $http.get('/api/stats/').then(function(data){
                    $scope.resolve = data.data;
                });
            };

            $scope.intervalFunction = function(){
                $scope.timerClientsUpdate = $timeout(function() {
                    $scope.getResolveData();
                    $scope.intervalFunction();
                }, $scope.timeUpdate)
            };

            $scope.getResolveData();
            $scope.intervalFunction();
        });
    </script>
@endsection
