<!-- Modal delete domain -->
<div class="modal fade" id="modalDeleteDomain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash" aria-hidden="true"></i> Delete Domain</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group">
                            Delete all domains@{{ deleteDomain ? " except  \"" + deleteDomain + "\"" : "" }}?
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
                <button type="submit" class="btn btn-success" name="create" title="Delete" ng-click="modalSendDeleteDomain();">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>