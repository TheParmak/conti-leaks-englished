<div class="well well-sm" ng-controller="clientFilter">
    <div id="filter" class="panel-body">
        {!! Form::open() !!}
        <table class="table table-condensed" id="clientFilterTable" style="margin-bottom: 0px;">
            <thead>
            <tr>
                <td>Prefix</td>
                <td>IP</td>
                <td>Created at</td>
                <td>Last activity</td>
            </tr>
            </thead>
            <tbody>
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
                <td>
                    <div class="input-daterange input-group input-group-sm datepicker-range">
                        {!! Form::input('start', null, ['class' => 'form-control input-sm', 'ng-model' => 'filter.start']) !!}
                        <span class="input-group-addon">/</span>
                        {!! Form::input('end', null, ['class' => 'form-control input-sm', 'ng-model' => 'filter.end']) !!}
                    </div>
                </td>
                <!-- Last Activity -->
                <td>
                    {!! Form::select('last_activity', $lastactivity_options, null, [
                        'class' => 'selectpicker',
                        'ng-model' => 'filter.last_activity'
                    ]) !!}
                </td>

            </tr>
            <tr>
                <td>Country code</td>
                <td>Have comment</td>
                <td colspan="2">Group</td>
            </tr>
            <tr>
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
                    {!! Form::select('have_comment', $have_comment_options, null, [
                        'class' => 'selectpicker',
                        'id' => 'commentFilterSelector',
                    ]) !!}
                </td>
                <td colspan="2">
                    {!! Form::select('group', $group_options, null, [
                        'ng-model' => 'filter.group',
                        'style' => 'width: 100%;',
                        'multiple' => '',
                        'id' => 'group',
                    ]) !!}
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="7">
{{--                    {!! Form::checkbox('get_stat', '1', '1' == Arr::get($post, 'get_stat')).' Get stat?' !!}--}}
                    <div ng-controller="statBtn" class="btn-group btn-group-sm pull-left">
                        <button type="button" class="btn btn-primary btn-inverse" ng-click="geoStatBtn()">GeoStat</button>
                        <button type="button" class="btn btn-primary btn-inverse" ng-click="systemStatBtn()">SystemStat</button>
                        <button type="button" class="btn btn-primary btn-inverse" ng-click="userSystemStatBtn()">UserSystemStat</button>
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
                <td colspan="3">

                </td>
            </tr>
            </tfoot>
        </table>
        {!! Form::close() !!}
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

<script type="text/javascript">
    $('#group').select2({
        placeholder: "Group..",
        tags: true,
        allowClear: true,
        tokenSeparators: [',', ' ']
    }).data('select2').$container.addClass("input-sm").css('padding', 0);

    $('#country').select2({
        placeholder: "Country..",
        tags: true,
        allowClear: true,
        tokenSeparators: [',', ' ']
    }).data('select2').$container.addClass("input-sm").css('padding', 0);

    app.controller('clientFilter', function($scope, $rootScope, $http){
        $scope.filter = {};
        $scope.pushBackBtn = true;
        $scope.total = 0;

        $scope.pushBack = function(){
            $scope.post = {
                'post': $scope.filter,
                'params': $scope.modalParams,
                'incode': $scope.modalIncode
            };
            $http.post('/rest/clients/push_back', $scope.post);
        };

        $scope.apply = function(){
            $scope.filter.have_comment = $('#commentFilterSelector').val();
            $rootScope.$broadcast("applyFilter", $scope.filter);
        };

        $scope.reset = function(){
            $('.selectpicker').val(null).selectpicker('refresh');
            $("#group").val(null).trigger('change.select2');
            $("#country").val(null).trigger('change.select2');
            // todo work auto in develop, but not in production
            $('.select2-selection__clear').remove();
            $scope.total = 0;
            $scope.pushBackBtn = true;

            $scope.filter = {};
            $rootScope.$broadcast("resetFilter");
            angular.forEach($scope.filter, function(value,index){
                $scope.filter[index] = null;
            });
        };

        $rootScope.$on("getPostFilter", function (event, args) {
            $rootScope.$broadcast('forStat', {
                'post': $scope.filter,
                'chart': args.chart,
                'modal': args.modal,
                'url': args.url
            });
        });

        $rootScope.$on("total", function (event, args) {
            $scope.total = args;
            /* pushBack btn disbl/enbl */
            $scope.pushBackBtn = !args;
        });
    });
</script>
