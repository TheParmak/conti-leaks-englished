<div class="well" ng-controller="clientIdFilter">
    <div class="row">
        <div class="col-lg-12" ng-show="errors" ng-cloak>
            <div class="alert alert-danger">
                @{{ errors }}
                <button type="button" class="close" ng-click="errors = undefined"><span aria-hidden="true">&times;</span></button>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="input-group input-group-sm">
                {!! Form::input('client_filter', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Input client...',
                    'ng-model' => 'clientId'
                ]) !!}
                <span class="input-group-btn">
                    {!! Form::button('client_filter_btn', '<span class="glyphicon glyphicon-search"></span>', [
                        'class' => 'btn btn-primary btn-sm btn-inverse',
                        'style' => 'height:29px;',
                        'ng-click' => 'apply()'
                    ]) !!}
                </span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    app.controller('clientIdFilter', function($scope, $http, $window){
        $scope.clientId = undefined;
        $scope.errors = undefined;

        $scope.apply = function(){
            $scope.errors = undefined;
            $http.post('/rest/clients/client_filter', {'client_id': $scope.clientId}).then(function (res) {
                console.log("success post on appy in clients/template/client_filter.blade.php");
                if(res.data.errors != undefined){ $scope.errors = res.data.errors; }
                else if(res.data.url != undefined){ $window.open(res.data.url, '_blank'); }
            }, function (res) {
                $scope.errors = res.error;
            });
        };
    });
</script>
