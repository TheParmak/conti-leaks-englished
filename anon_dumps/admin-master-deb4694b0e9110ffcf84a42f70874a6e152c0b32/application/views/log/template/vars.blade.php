<table ng-controller="vars" ng-init="apply()" class="table table-bordered table-condensed" ng-cloak>
    @include('TEMPLATE.thead')
    <tbody class="well" ng-if="bigTotalItems">
        <tr ng-repeat="d in data | orderBy:sortField:reverse">
            <td>@{{ d.key }}</td>
            <td>@{{ d.value }}</td>
            <td>@{{ d.updated_at }}</td>
        </tr>
    </tbody>
    @include('TEMPLATE.hideEmptyFields')
    @include('TEMPLATE.pagination')
</table>

<script type="text/javascript">
    app.controller('vars', function($controller, $scope, $http){
        $scope.name = 'vars';
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
            $http.post('/rest/clients/storage/' + $scope.bigCurrentPage, $scope.post).success(function(data){
                $scope.data = data['data'];
                $scope.bigCurrentPage = data['current_page'];
                $scope.bigTotalItems = data['total_items'];
                $scope.itemsPerPage = data['items_per_page'];
            });
        };
    });
</script>