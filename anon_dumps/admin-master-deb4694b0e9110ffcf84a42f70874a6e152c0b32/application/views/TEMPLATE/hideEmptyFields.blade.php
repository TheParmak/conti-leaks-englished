@if(!Session::instance()->get('hideEmptyFields'))
    <tbody class="well" ng-if="!bigTotalItems">
    <tr>
        <td colspan="6" style="text-align: center;">
            <h4 class="text-danger">No @{{ name }}</h4>
        </td>
    </tr>
    </tbody>
@endif