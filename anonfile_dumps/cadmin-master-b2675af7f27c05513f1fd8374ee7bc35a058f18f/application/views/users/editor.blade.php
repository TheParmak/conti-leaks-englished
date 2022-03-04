<div ng-controller='userEditor'>
    <div id="success" class="alert alert-success small" ng-show="success" on-finish-render="unescapeHtml()">@{{ success }}</div>
    <div ng-repeat="error in errors" ng-show="errors">
        <div class="alert alert-danger small" on-finish-render="unescapeHtml()">@{{ error }}</div>
    </div>
    <table class="table">
        <tbody>
        <tr>
            <td>
                {!! Form::input('username', $user->username, [
                    'class' => 'form-control',
                    $user->loaded() ? 'disabled' : '' => '',
                    'placeholder' => $user->loaded() ? 'Login' : '',
                    'autocomplete' => 'off',
                    'ng-model' => 'username',
                ]) !!}
            </td>
        </tr>
        @if(!$user->loaded())
            <tr>
                <td>
                    {!! Form::password('password', $user->password, [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'placeholder' => 'Password',
                        'ng-model' => 'password',
                    ]) !!}
                </td>
            </tr>
            <tr>
                <td>
                    {!! Form::password('password_confirm', $user->password, [
                        'class' => 'form-control',
                        'placeholder' => 'Password confirm',
                        'ng-model' => 'password_confirm',
                    ]) !!}
                </td>
            </tr>
        @endif
        <tr>
            <td>
                @foreach($roles as $role)
                    @if($role->id != '1')
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox(null, $role->id, $user->has('roles', $role), ['checklist-model' => 'roles', 'checklist-value' => $role->id]) !!}
                                {{ $role->name }}
                            </label>
                        </div>
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td>
                <div class="btn-group btn-group-sm pull-right">
                    {!! Form::button('apply', 'Apply', [
                        'class' => 'btn-primary btn btn-inverse',
                        'type' => 'button',
                        'ng-click' => 'apply()',
                    ]) !!}
                    @if($reset && $user->id)
                        <a href="/users/reset_password/{{ $user->id }}" class="btn-danger btn btn-inverse">
                            Reset password
                        </a>
                    @endif
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<script type="text/javascript">
    app.directive('onFinishRender', function () {
        return {
            restrict: 'A',
            link: function (scope, element, attr) {
                if (scope.$last === true) {
                    scope.$evalAsync(attr.onFinishRender);
                }
            }
        }
    });

    app.controller('userEditor', function($scope, $http){
        $scope.success = null;
        $scope.errors = null;
        $scope.username = "{{ $user->username }}";
        $scope.roles = JSON.parse("{!! $user_roles !!}");
        $scope.id = "{{ $user->id }}";

        $scope.updPost = function(){
            $scope.post = {
                'id': $scope.id,
                'roles': $scope.roles,
                'username': $scope.username,
                'password': $scope.password,
                'password_confirm': $scope.password_confirm
            };
        };

        $scope.unescapeHtml = function(){
            $('.alert').html(function(){
                $(this).html($(this).text());
            });
        };

        $scope.apply = function(){
            $scope.updPost();
            $scope.success = null;
            $scope.errors = null;
            $http.post('/rest/users/editor', $scope.post).then(function (res) {
              if(res.data.success != undefined) { $scope.success = res.data.success; }
              else if(res.data.errors != undefined) { $scope.errors = res.data.errors; }
            }, function (res) {
                $scope.errors = res.error;
            });
        };
    });
</script>
