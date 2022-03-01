<tfoot ng-show="showPagination()">
    <tr>
        <td colspan="10" class="text-center">
            <ul uib-pagination total-items="bigTotalItems" ng-model="bigCurrentPage" max-size="maxSize" class="pagination-sm" boundary-link-numbers="true" ng-change="getData()" items-per-page="itemsPerPage" previous-text="&laquo;" next-text="&raquo;"></ul>
        </td>
    </tr>
</tfoot>