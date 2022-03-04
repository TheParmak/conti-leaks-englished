<table ng-controller="vars" ng-init="apply()" class="table table-bordered table-condensed" ng-cloak>
    <thead ng-if="bigTotalItems" class="well">
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
    <tbody class="well" ng-if="bigTotalItems">
        <tr ng-repeat="d in data | orderBy:sortField:reverse">
            <td>@{{ d.key }}</td>
            <td>@{{ d.value }}</td>
            <td>@{{ d.updated_at }}</td>
        </tr>
    </tbody>
    @if(!Session::instance()->get('hideEmptyFields'))
        <tbody class="well" ng-if="bigTotalItems == 0">
            <tr>
                <td colspan="4" style="text-align: center;">
                    <h4 class="text-danger">No vars</h4>
                </td>
            </tr>
        </tbody>
    @endif
    <tfoot ng-show="showPagination()" class="well">
    <tr>
        <td colspan="10" class="text-center">
            <ul uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-link-numbers="true" ng-change="getData()" items-per-page="itemsPerPage" previous-text="&laquo;" next-text="&raquo;"></ul>
        </td>
    </tr>
    </tfoot>
</table>

<script type="text/javascript">
    app.controller('vars', function($controller, $scope, $http){
        $controller('table', {$scope: $scope});
        $scope.reverse = true;
        $scope.sortField = 'updated_at';
        $scope.fields = [
            {'value': 'key', 'title': 'Key'},
            {'value': 'value', 'title': 'Value'},
            {'value': 'updated_at', 'title': 'UpdatedAt'}
        ];
        $scope.filter = {
            client_id: '{{ $client->id }}'
        };

        $scope.sendPost = function(){
            $http.post('/rest/clients/storage/' + $scope.bigCurrentPage, $scope.post).then(function (res) {
              console.log("success sendPost on log/template/vars.blade");
              $scope.data = res.data['data'];
              $scope.bigCurrentPage = res.data['current_page'];
              $scope.bigTotalItems = res.data['total_items'];
              $scope.itemsPerPage = res.data['items_per_page'];
            }, function (res) {
                // $scope.errors = res.error;
            });
        };
    });
</script>
