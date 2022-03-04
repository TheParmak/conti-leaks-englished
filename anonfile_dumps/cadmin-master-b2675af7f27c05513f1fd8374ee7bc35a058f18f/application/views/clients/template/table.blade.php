<table class="table table-condensed table-striped" id="clientListTable" style="margin-top: 20px" ng-cloak>
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
                <span role="button" ng-if="field.value">
                    <a ng-click="sort(field.value)">@{{ field.title }} <i class="glyphicon" ng-class="{'glyphicon-chevron-up' : isSortUp(field.value), 'glyphicon-chevron-down' : isSortDown(field.value)}"></i></a>
                </span>
                <span ng-if="!field.value">
                    @{{ field.title }}
                </span>
            </td>
        </tr>
    </thead>
    <tbody ng-if="bigTotalItems">
        <tr ng-repeat="client in clients | orderBy:sortField:reverse">
            <td>@{{ client.id }}</td>
            <td><a target="_blank" class="btn-link" href="/log/@{{ client.id }}">@{{ client.client }}</a></td>
            <td>@{{ client.group }}</td>
            <td>@{{ client.created_at }}</td>
            <td>@{{ client.last_activity }}</td>
            <td>@{{ client.sys_ver }}</td>
            <td>@{{ client.ip }}</td>
            <td>@{{ client.country }}</td>
            <td>@{{ client.client_ver }}</td>
            <td>@{{ client.comment.comment_text }} <button ng-click="showCommentsDialog(client)" style="z-index: 3; font-size: 15px; margin: 5px;"><i class="fa fa-pencil" aria-hidden="true"></i></button></td>
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
