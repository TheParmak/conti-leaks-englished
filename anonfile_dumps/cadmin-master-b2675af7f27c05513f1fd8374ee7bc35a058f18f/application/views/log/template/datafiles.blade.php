<div>
    <!-- Modal -->
    <div class="modal fade" id="myModalDataFiles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">DataFiles SystemInfo</h4>
                </div>
                <div id='modal-datafiles' class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-condensed" ng-controller="datafiles" ng-init="apply()" ng-cloak>
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
        @if(!Session::instance()->get('hideEmptyFields'))
            <tbody class="well" ng-if="!bigTotalItems">
                <tr>
                    <td colspan="6" style="text-align: center;">
                        <h4 class="text-danger">No data</h4>
                    </td>
                </tr>
            </tbody>
        @endif
        @include('TEMPLATE.pagination')
    </table>
</div>

<script type="text/javascript">
    app.controller('datafiles', function($controller, $scope, $http){
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
            $http.post('/rest/clients/datafiles/' + $scope.bigCurrentPage, $scope.post).then(function (res) {
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
