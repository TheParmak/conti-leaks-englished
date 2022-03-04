<div class="modal fade" id="myModalStat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">@{{ type }}</h4>
            </div>
            <div class="modal-body" style="max-height: 780px;">
                <div id="chart"></div>
                    <table class="table table-condensed table-striped">
                        {{-- GEO --}}
                        <thead ng-show="type == 'Geo'">
                            <tr>
                                <th style="border-top: 2px solid #ddd;">Location</th>
                                <th style="border-top: 2px solid #ddd;">Count</th>
                                <th style="border-top: 2px solid #ddd;">Percentage</th>
                            </tr>
                        </thead>
                        <tbody ng-show="type == 'Geo'">
                            <tr ng-repeat="i in info | orderBy:'-percent'">
                                <td>@{{ i.location }}</td>
                                <td>@{{ i.count }}</td>
                                <td>@{{ i.percent }}%</td>
                            </tr>
                        </tbody>
                        {{-- END GEO --}}
                        {{-- IP --}}
                        <thead ng-show="type == 'Ip'">
                            <tr>
                                <th style="border-top: 2px solid #ddd;">IP</th>
                                <th style="border-top: 2px solid #ddd;">Count</th>
                            </tr>
                        </thead>
                        <tbody ng-show="type == 'Ip'">
                            <tr ng-repeat="i in info | orderBy:'-count'">
                                <td>@{{ i.ip }}</td>
                                <td>@{{ i.count }}</td>
                            </tr>
                        </tbody>
                        {{-- END IP --}}
                        {{-- AV --}}
                        <thead ng-show="type == 'Av'">
                            <tr>
                                <th style="border-top: 2px solid #ddd;">Antivirus</th>
                                <th style="border-top: 2px solid #ddd;">Count</th>
                            </tr>
                        </thead>
                        <tbody ng-show="type == 'Av'">
                            <tr ng-repeat="i in info | orderBy:'-count'">
                                <td>@{{ i.av }}</td>
                                <td>@{{ i.count }}</td>
                            </tr>
                        </tbody>
                        {{-- END AV --}}
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                {!! Form::button('send', 'Close', ['class' => 'btn btn-default btn-inverse', 'data-dismiss' => 'modal']) !!}
            </div>
        </div>
    </div>
</div>