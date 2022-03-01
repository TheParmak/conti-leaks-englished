<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">
                <span class="glyphicon glyphicon-home"></span>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-menu-hamburger" style="margin-right: 4px;"></span>Info<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="/clients">Clients</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/result">Result</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/stats">Statistics</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-wrench" style="margin-right: 4px;"></span>Manage<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Tasks</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/emails">Emails</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('email_list') }}">EmailsLists</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/get_info">Register client</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/settings">Settings</a>
                        </li>
                    </ul>
                </li>
            </ul>

            <ul class="nav navbar-nav pull-right" ng-controller="backEndStatus" ng-cloak>
                <p class="navbar-text" ng-if="backEndStatus === undefined">
                    <i class="fa fa-cog fa-spin fa-fw"></i> Loading..
                </p>
                <p class="navbar-text" ng-if="backEndStatus === null"><span class="label label-danger">Backend Off</span></p>
                <p class="navbar-text" ng-if="backEndStatus === true"><span class="label label-success">Listening</span></p>
                <p class="navbar-text" ng-if="backEndStatus === false"><span class="label label-success">Resolving</span></p>
                <li class="divider-vertical"></li>
                <li>
                    <a href="/logout"><span class="glyphicon glyphicon-log-out" style="margin-right: 4px;"></span>Logout ({{ Auth::user()->name }})</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script type="text/javascript">
    app.controller('backEndStatus', function($controller, $scope, $http, $timeout, blockUIConfig){
        blockUIConfig.autoBlock = false;
        $scope.backEndStatus = undefined;

        $scope.statusBack = function () {
            $http.get('/api/tasks/status').then(function (data) {
                $scope.backEndStatus = data.data.status;
            });
        };

        $scope.intervalFunction = function(){
            $timeout(function() {
                $scope.statusBack();
                $scope.intervalFunction();
            }, 10000)
        };

        $scope.statusBack();
        $scope.intervalFunction();
    });
</script>