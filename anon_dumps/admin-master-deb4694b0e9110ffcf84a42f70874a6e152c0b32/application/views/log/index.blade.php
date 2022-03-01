@include('TEMPLATE.js.table')
@include('log.template.client', [
    'client' => $client,
    'countSameDevhash' => $countSameDevhash,
])

@include('log.template.log', [
    'log' => $log,
    'client' => $client,
    'pagination' => $pagination,
//    'importanceevents' => $importanceevents
])

@include('log.template.importance_events', [
    'client' => $client,
])

@include('log.template.clients_events', [
    'client' => $client,
])

@include('log.template.vars', [
    'vars' => $vars,
    'client' => $client,
])

@include('log.template.datafiles', [
    'client' => $client,
])

@include('log.template.sysinfo', [
    'client' => $client,
])

@include('log.template.commands', [
    'commands' => $commands,
    'isAllowedToWorkWithCommands' => $isAllowedToWorkWithCommands
])

@if(Helper::checkActionInRole('Commands'))
    @include('log.template.modal', [
        'client' => $client,
        'servers' => $servers,
        'columns' => $columns,
    ])
@endif

<?php //echo View::factory('/log/template/v_backconndata')->bind('backconndata', $backconndata)->bind('pagination_bc', $pagination_bc)->bind('client', $client); ?>

@include('log.template.comment', ['comment' => $comment])
