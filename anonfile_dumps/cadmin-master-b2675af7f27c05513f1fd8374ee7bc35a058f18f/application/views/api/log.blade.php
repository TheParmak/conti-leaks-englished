<div ng-controller="apiLog">
    {{-- Filter --}}
    <div class="well well-sm">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <td>ApiKey</td>
                    <td>Command</td>
                    <td>Time</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {!! Form::input('apikey', null, [
                            'class' => 'form-control input-sm',
                            'ng-model' => 'filter.apikey'
                        ]) !!}
                    </td>
                    <td>
                        {!! Form::input('command', null, [
                            'class' => 'form-control input-sm',
                            'ng-model' => 'filter.command'
                        ]) !!}
                    </td>
                    <td>
                        <div class="input-daterange input-group input-group-sm datepicker-range">
                            {!! Form::input('start', null, [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'filter.start'
                            ]) !!}
                            <span class="input-group-addon">/</span>
                            {!! Form::input('end', null, [
                                'class' => 'form-control input-sm',
                                'ng-model' => 'filter.end'
                            ]) !!}
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <div class="btn-group btn-group-sm pull-right">
                            {!! Form::button('reset','Reset', [
                                'class' => 'btn btn-danger btn-sm btn-inverse',
                                'type'  => 'button',
                                'ng-click' => 'reset()',
                            ]) !!}
                            {!! Form::button('apply','Apply', [
                                'class' => 'btn btn-success btn-sm btn-inverse',
                                'type'  => 'button',
                                'ng-click' => 'apply()',
                            ]) !!}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Table --}}
    <table class="table table-condensed table-striped" ng-cloak>
        <thead ng-show="showPagination()">
            <tr>
                <td colspan="10" class="text-center">
                    <ul uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-link-numbers="true" ng-change="getData()" items-per-page="itemsPerPage" previous-text="&laquo;" next-text="&raquo;"></ul>
                </td>
            </tr>
        </thead>
        <thead ng-if="bigTotalItems">
            <tr>
                <td ng-repeat="field in fields">
                    <span ng-if="field.value">
                        <a ng-click="sort(field.value)">@{{ field.title }} <i class="glyphicon" ng-class="{'glyphicon-chevron-up' : isSortUp(field.value), 'glyphicon-chevron-down' : isSortDown(field.value)}"></i></a>
                    </span>
                    <span ng-if="!field.value">
                        @{{ field.title }}
                    </span>
                </td>
            </tr>
        </thead>
        <tbody ng-if="bigTotalItems">
            <tr ng-repeat="d in data | orderBy:sortField:reverse">
                <td>
                    <a href="/api/key/editor/@{{ d.apikey_id }}" target="_blank">@{{ d.apikey }}</a>
                </td>
                <td>@{{ d.ip }}</td>
                <td>@{{ d.command }}</td>
                <td>@{{ d.type }}</td>
                <td>@{{ d.time }}</td>
            </tr>
        </tbody>
        <tbody ng-if="bigTotalItems == 0">
            <tr>
                <td colspan="10" style="text-align: center;">
                    <h4 class="text-danger">No records!</h4>
                </td>
            </tr>
        </tbody>
        <tfoot ng-show="showPagination()">
        <tr>
            <td colspan="10" class="text-center">
                <ul uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-link-numbers="true" ng-change="getData()" items-per-page="itemsPerPage" previous-text="&laquo;" next-text="&raquo;"></ul>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

@include('TEMPLATE.js.table')

<script type="text/javascript">
    app.controller('apiLog', function($controller, $scope, $http){
        $controller('table', {$scope: $scope});
        $scope.reverse = true;
        $scope.sortField = 'time';
        $scope.fields = [
            {'value': 'apikey', 'title': 'ApiKey'},
            {'value': 'ip', 'title': 'Ip'},
            {'value': 'command', 'title': 'Command'},
            {'value': 'type', 'title': 'Type'},
            {'value': 'time', 'title': 'Time'}
        ];

        $scope.sendPost = function(){
            $http.post('/rest/api/log/' + $scope.bigCurrentPage, $scope.post).then(function (res) {
              //console.log(res.data);
              $scope.data = res.data['data'];
              $scope.bigCurrentPage = res.data['current_page'];
              $scope.bigTotalItems = res.data['total_items'];
              $scope.itemsPerPage = res.data['items_per_page'];
            }, function (res) {
                //$scope.errors = res.error;
            });
        };
    });

    $('.datepicker-range').datepicker({
        language: "ru",
        autoclose: true,
        todayHighlight: true,
        format: "yyyy/mm/dd"
    }).on('changeDate', function(dateEvent) {
        var start = $('#datepicker').datepicker('startDate');
    });
</script>
