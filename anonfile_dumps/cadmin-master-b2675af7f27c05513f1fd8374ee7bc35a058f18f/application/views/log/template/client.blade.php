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
	    <td id="dnsbl" ng-class="class" role="alert">
            <img ng-show="loading" src="/template/img/loader.svg" alt="Loading..." />
        </td>
    </tr>
    </tbody>
    <tfoot class="well">
    <tr>
        <td colspan="8">
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

            <div class="clearfix"></div>
        </td>
    </tr>
    </tfoot>
</table>

<script>
    app.controller('dnsblCtrl', function($scope, $http){
        $scope.loading = true;

        angular.element(document).ready(function () {
            $http.post('/rest/clients/dnsbl', {ip: "<?=$client->ip?>"}).then(function (res) {
              $scope.loading = false;
              $scope.class = 'alert alert-';
              $scope.dnsbl = angular.element(document.querySelector('#dnsbl'));
              if(res.data) {
                  console.log("sucess dnsbl post on log/template/client.blade.php");
                  $scope.class += 'danger';
                  $scope.dnsbl.text('Listed');
              }else {
                  $scope.class += 'success';
                  $scope.dnsbl.text('Not listed');
              }
            }, function (res) {
                //$scope.errors = res.error;
            });
        });
    });

    /* TODO need upd from jquery to angular */
    $(document).ready(function() {
        'use strict';
        var $scopeWidgetClient = $('#widget-client');

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
    });
</script>
