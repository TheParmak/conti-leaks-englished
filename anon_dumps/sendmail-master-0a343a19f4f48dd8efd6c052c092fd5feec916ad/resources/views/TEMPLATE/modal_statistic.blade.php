<!-- Modal statistic -->
<div class="modal fade" id="modalStatistic" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-pie-chart" aria-hidden="true"></i> Statistic</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">Good: @{{ modalStatisticData.status.email_number_right }}
                    </div>
                    <div class="col-md-6">Bad: @{{ modalStatisticData.status.email_number_fail }} 
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-6">In process: @{{ modalStatisticData.status.in_process }}
                    </div>
                    <div class="col-md-6">In process percent: @{{ modalStatisticData.status.in_process_pr | number:6 }}
                    </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-6">Processed: @{{ modalStatisticData.status.processed }}
                    </div>
                    <div class="col-md-6">Processed percent: @{{ modalStatisticData.status.processed_pr | number:6 }}
                    </div>
                </div>
                <div class="row" style="text-align: center; margin-top: 10px;">
                    <div class="col-md-12">Total: @{{ modalStatisticData.status.size }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" data-dismiss="modal">
                    <span class="glyphicon glyphicon-ok"></span>
                </button>
            </div>
        </div>
    </div>
</div>