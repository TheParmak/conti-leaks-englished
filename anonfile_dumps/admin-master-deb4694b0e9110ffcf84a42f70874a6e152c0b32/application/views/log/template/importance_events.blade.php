<table ng-controller="importanceEvents" ng-init="apply()" class="table table-bordered table-condensed" ng-cloak>
    @include('TEMPLATE.thead')
    <tbody class="well" ng-if="bigTotalItems">
        <tr ng-repeat="d in data | orderBy:sortField:reverse">
            <td>@{{ d.created_at }}</td>
            <td>@{{ d.info }}</td>
            <td>@{{ d.command }}</td>
        </tr>
    </tbody>
    @include('TEMPLATE.hideEmptyFields')
    @include('TEMPLATE.pagination')
</table>

<script type="text/javascript">
    app.controller('importanceEvents', function($controller, $scope, $http){
        $scope.name = 'Importance Events';
        $controller('table', {$scope: $scope});
        $scope.reverse = true;
        $scope.sortField = 'created_at';
        $scope.fields = [
            {'value': 'created_at', 'title': 'CreatedAt'},
            {'value': 'info', 'title': 'Info'},
            {'value': 'command', 'title': 'Command'}
        ];
        $scope.filter = {
            client_id: '{{ $client->id }}'
        };

        $scope.sendPost = function(){
            $http.post('/rest/log/importance_events/' + $scope.bigCurrentPage, $scope.post).success(function(data){
                $scope.data = data['data'];
                $scope.bigCurrentPage = data['current_page'];
                $scope.bigTotalItems = data['total_items'];
                $scope.itemsPerPage = data['items_per_page'];
            });
        };
    });
</script>