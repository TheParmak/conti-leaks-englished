<!-- Modal delte from queue -->
<div class="modal fade" id="modaLDeleteFromQueue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash" aria-hidden="true"></i> Delete Task From Queue?</h4>
            </div>
            <div class="modal-body">
                @{{ modalDeleteItemQueue }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
                <button type="submit" class="btn btn-success" name="create" title="Delete" ng-click="modalSendDeleteQeueu()">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>