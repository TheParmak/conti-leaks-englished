<!-- Modal statistic -->
<div class="modal fade" id="modalStartTaskQueue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-play" aria-hidden="true"></i> Start Task</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon3">Start From</span>
                            <input type="text" class="form-control" ng-model="modalSendDelete.startFrom" aria-describedby="basic-addon3">
                            <span class="input-group-addon" id="basic-addon3">%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
                <button type="submit" class="btn btn-success" name="create" title="Start" ng-click="modalSendStartTask(modalSendDelete.startFrom); modalSendDelete.startFrom = '';">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>