<!-- Modal delete -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">@{{ modalDeleteName }}</h4>
            </div>
            <div class="modal-body">
                @{{ modalDeleteItem }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
                <button type="submit" class="btn btn-success" name="create" title="Create" ng-click="modalSendDelete()">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>