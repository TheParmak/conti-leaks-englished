<link rel="stylesheet" type="text/css" href="/template/css/sweetalert.css">
<script type="text/javascript" src="/template/js/sweetalert.min.js"></script>

<table class="table table-condensed table-striped" id="clientListTable" style="margin-top: 20px" ng-controller="clientTable" ng-cloak>
    <thead ng-show="showPagination()">
    <tr>
        <td colspan="11" class="text-center">
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
            <td>
                <i ng-if="client.nat == false" class="fa fa-times" aria-hidden="true"></i>
                <i ng-if="client.nat == true" class="fa fa-check" aria-hidden="true"></i>
                <i ng-if="client.nat == null" class="fa fa-question" aria-hidden="true"></i>
            </td>
            <td>@{{ client.group }}</td>
            <td>@{{ client.created_at }}</td>
            <td>@{{ client.last_activity }}</td>
            <td>@{{ client.importance }}</td>
            <td>@{{ client.sys_ver }}</td>
            <td>@{{ client.ip }}</td>
            <td>@{{ client.country }}</td>
            <td>@{{ client.client_ver }}</td>
        </tr>
    </tbody>
    <tbody ng-if="bigTotalItems == 0">
        <tr>
            <td colspan="11" style="text-align: center;">
                <h4 class="text-danger">No records!</h4>
            </td>
        </tr>
    </tbody>
    <tfoot ng-show="showPagination()">
        <tr>
            <td colspan="11" class="text-center">
                <ul uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-link-numbers="true" ng-change="getData()" items-per-page="itemsPerPage" previous-text="&laquo;" next-text="&raquo;"></ul>
            </td>
        </tr>
    </tfoot>
</table>
