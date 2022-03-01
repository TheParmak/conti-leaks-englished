<nav class="navbar navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">
                <span class="glyphicon glyphicon-home"></span>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li<?php if ( '/' == $_SERVER['REQUEST_URI'] || '/clients' == $_SERVER['REQUEST_URI'] ) : ?> class="active"<?php endif; ?>>
                    <a href="/"><span class="glyphicon glyphicon-hdd" style="margin-right: 4px;"></span>Clients</a>
                </li>
                <!-- Info -->
                <?php $infoActions = ['File', 'Commands', 'Server', 'Config', 'Link', 'Lastactivity', 'Devhashlookup', 'Statistics', 'Api/Log']; ?>
                <?php $hide = ['Commands'] // TODO ?>
                <?php if ( $user->hasAnyOfActions($infoActions) && ($user->hasAnyOfActions(array_diff($infoActions, $hide)) && !$user->hasAction('HideCommands')) ) : ?>
                <li class="dropdown<?php
                    if ( in_array(substr($_SERVER['REQUEST_URI'], 1), array_map(array('UTF8', 'strtolower'), $infoActions))
                            || preg_match('/^\/(?:' . implode('|', array_map(function($value) { return UTF8::strtolower(preg_quote($value, '/')); }, $infoActions)) . ')\//', $_SERVER['REQUEST_URI']) ) :
                            ?> active<?php
                    endif;
                    ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-menu-hamburger" style="margin-right: 4px;"></span>Info<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach($infoActions as $infoAction) : ?>
                            <?php foreach ( $actions as $action ) : ?>
                                <?php if($infoAction == 'Commands' && $action->name == $infoAction && $user->hasAction('HideCommands')) : ?>
                                    <!-- empty -->
                                <?php elseif ( $action->name == $infoAction && $user->hasAction($action) ) : ?>
                                    <li<?php if ( '/' . UTF8::strtolower($action->name) == $_SERVER['REQUEST_URI'] || 0 === strpos($_SERVER['REQUEST_URI'], '/' . UTF8::strtolower($action->name) . '/') ) : ?> class="active"<?php endif; ?>>
                                        <a href="/<?= UTF8::strtolower($action->name); ?>"><?= $action->description; ?></a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <!-- Manage -->
                <?php $manageActions = ['CRUD/File', 'CRUD/Server', 'CRUD/Config', 'Api/Key', 'Idlecommand', 'Groups', 'CRUD/Importancerules', 'CRUD/Commandsevent']; ?>
                <?php if ( $user->hasAnyOfActions($manageActions) ) : ?>
                <li class="dropdown<?php
                    if ( in_array(substr($_SERVER['REQUEST_URI'], 1), array_map(array('UTF8', 'strtolower'), $manageActions))
                            || preg_match('/^\/(?:' . implode('|', array_map(function($value) { return UTF8::strtolower(preg_quote($value, '/')); }, $manageActions)) . ')\//', $_SERVER['REQUEST_URI']) ) :
                            ?> active<?php
                    endif;
                    ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-wrench" style="margin-right: 4px;"></span>Manage<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach($manageActions as $manageAction) : ?><?php
                            foreach ( $actions as $action ) : ?><?php
                                if ( $action->name == $manageAction && $user->hasAction($action) ) : ?>
                                    <li<?php if ( '/' . UTF8::strtolower($action->name) == $_SERVER['REQUEST_URI'] || 0 === strpos($_SERVER['REQUEST_URI'], '/' . UTF8::strtolower($action->name) . '/') ) : ?> class="active"<?php endif; ?>>
                                        <a href="/<?= UTF8::strtolower(preg_replace('/^Clients$/', '', $action->name)); ?>"><?= $action->description; ?></a>
                                    </li><?php
                                endif; ?><?php
                            endforeach; ?><?php
                        endforeach; ?>
                        <?php if($user->hasAction('PushBackList')) : ?>
                            <li>
                                <a href="/PushBackList">PushBackList</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <!-- Administration -->
                <?php $administrationActions = array('Users', 'Userslogs', 'Users/Online', 'Roles'); ?>
                <?php if ( $user->hasAnyOfActions($administrationActions) ) : ?>
                <li class="dropdown<?php
                    if ( in_array(substr($_SERVER['REQUEST_URI'], 1), array_map(array('UTF8', 'strtolower'), $administrationActions))
                            || preg_match('/^\/(?:' . implode('|', array_map(function($value) { return UTF8::strtolower(preg_quote($value, '/')); }, $administrationActions)) . ')\//', $_SERVER['REQUEST_URI']) ) :
                            ?> active<?php
                    endif;
                    ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-bookmark" style="margin-right: 4px;"></span>Administration<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach($administrationActions as $administrationAction) : ?><?php
                            foreach ( $actions as $action ) : ?><?php
                                if ( $action->name == $administrationAction && $user->hasAction($action) ) : ?>
                                    <li<?php if ( '/' . UTF8::strtolower($action->name) == $_SERVER['REQUEST_URI'] || 0 === strpos($_SERVER['REQUEST_URI'], '/' . UTF8::strtolower($action->name) . '/') && '/users/online' != strtolower($_SERVER['REQUEST_URI']) ) : ?> class="active"<?php endif; ?>>
                                        <a href="/<?=UTF8::strtolower($action->name)?>"><?php
                                            if ( $action->name == 'Users' ) :
                                                ?><span class="glyphicon glyphicon-user" style="margin-right: 4px;"></span><?php
                                            elseif ( $action->name == 'Roles' ) :
                                                ?><span class="glyphicon glyphicon-hand-right" style="margin-right: 4px;"></span><?php
                                            elseif ( $action->name == 'Userslogs' ) :
                                                ?><span class="glyphicon glyphicon-film" style="margin-right: 4px;"></span><?php
                                            elseif ( $action->name == 'Users/Online' ) :
                                                ?><span class="glyphicon glyphicon-off" style="margin-right: 4px;"></span><?php
                                            endif;
                                        ?><?=$action->description?></a>
                                    </li><?php
                                endif; ?><?php
                            endforeach; ?><?php
                        endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>

            <ul class="nav navbar-nav pull-right">
                <li class="divider-vertical"></li>
                <p ng-controller="utcTimer" class="navbar-text myUTCTimer" ng-init="updUtcTimer()"></p>
                <li<?php if ( '/profile' == $_SERVER['REQUEST_URI'] || 0 === strpos($_SERVER['REQUEST_URI'], '/profile/') ) : ?> class="active"<?php endif; ?>>
                    <a href="/profile"><span class="glyphicon glyphicon-user" style="margin-right: 4px;"></span>Profile</a>
                </li>
                <li>
                    <a href="/login/logout"><span class="glyphicon glyphicon-log-out" style="margin-right: 4px;"></span>Logout (<?= $user->username; ?>)</a>
                </li>
            </ul>

        </div>
    </div>
</nav>

<script type="text/javascript">
    app.controller('utcTimer', function($scope, $http, $interval){
        $scope.min = $scope.hours = 0;
        $scope.updUtcTimer = function(){
            $http.get('/rest/utc_timer').then(function(data){
                $('.myUTCTimer').text(data.data);
                $scope.min = parseInt(data.data.split(":")[1]);
                $scope.hours = parseInt(data.data.split(":")[0]);
            });
        };

        $interval(function() {
            if ($scope.min > 59) {
                $scope.min = 0;
                if ($scope.hours > 23) $scope.hours = 0;
                else $scope.hours ++;
            } else $scope.min ++;

            let exMin = $scope.min;
            let exH = $scope.hours;
            if ($scope.min <= 9) exMin = "0" + $scope.min;
            if ($scope.hours <= 9) exH = "0" + $scope.hours;

            $('.myUTCTimer').text(exH + ":" + exMin);

        }, 60 * 1000);
    });
</script>
