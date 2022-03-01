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