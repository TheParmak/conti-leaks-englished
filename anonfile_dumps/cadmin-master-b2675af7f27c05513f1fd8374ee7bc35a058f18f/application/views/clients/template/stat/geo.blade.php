<div class="modal fade" id="myModalGeo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">GeoStat</h4>
            </div>
            <div class="modal-body" style="max-height: 780px;">
                <div id="chart_div_geo"></div>
                <div id="other_geo" style="margin-top: 15px;">
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th style="border-top: 2px solid #ddd;">Location</th>
                                <th style="border-top: 2px solid #ddd;">Count</th>
                                <th style="border-top: 2px solid #ddd;">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="i in info | orderBy:'-percent'">
                                <td>@{{ i.location }}</td>
                                <td>@{{ i.count }}</td>
                                <td>@{{ i.percent }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button('send', 'Close', ['class' => 'btn btn-default btn-inverse', 'data-dismiss' => 'modal']) !!}
            </div>
        </div>
    </div>
</div>