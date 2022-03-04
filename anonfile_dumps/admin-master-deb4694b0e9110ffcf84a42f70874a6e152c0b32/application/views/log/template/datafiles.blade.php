<table ng-controller="datafiles" ng-init="apply()" ng-cloak class="table table-bordered table-condensed">
    @include('TEMPLATE.thead')
    <tbody class="well" ng-if="bigTotalItems">
        <tr ng-repeat="d in data | orderBy:sortField:reverse">
            <td>
                <a href="/download/datafiles/@{{ d.id }}">
                    <span class="glyphicon glyphicon-download-alt"></span> @{{ d.name }}
                </a>
            </td>
            <td>@{{ d.created_at }}</td>
            <td>@{{ d.ctl }}</td>
            <td>@{{ d.ctl_result }}</td>
            <td>@{{ d.aux_tag }}</td>
        </tr>
    </tbody>
    @include('TEMPLATE.hideEmptyFields')
    @include('TEMPLATE.pagination')
</table>

<script type="text/javascript">
    app.controller('datafiles', function($controller, $scope, $http){
        $scope.name = 'datafiles';
        $controller('table', {$scope: $scope});
        $scope.reverse = true;
        $scope.sortField = 'created_at';
        $scope.fields = [
            {'value': 'name', 'title': 'Name'},
            {'value': 'created_at', 'title': 'CreatedAt'},
            {'value': 'ctl', 'title': 'Ctl'},
            {'value': 'ctl_result', 'title': 'CtlResult'},
            {'value': 'aux_tag', 'title': 'AuxTag'}
        ];
        $scope.filter = {
            client_id: '{{ $client->id }}'
        };

        $scope.sendPost = function(){
            $http.post('/rest/clients/datafiles/' + $scope.bigCurrentPage, $scope.post).success(function(data){
                $scope.data = data['data'];
                $scope.bigCurrentPage = data['current_page'];
                $scope.bigTotalItems = data['total_items'];
                $scope.itemsPerPage = data['items_per_page'];
            });
        };
    });
</script>