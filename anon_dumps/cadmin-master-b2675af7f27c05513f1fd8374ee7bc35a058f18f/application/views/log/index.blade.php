@include('TEMPLATE.js.table')
@include('log.template.client', [ 'client' => $client, ])

<div class="row">
    <div class="col-md-6">
        @include('log.template.log', [
            'log' => $log,
            'client' => $client,
            'pagination' => $pagination,
        ])
    </div>
    <div class="col-md-6">
        @include('log.template.vars', [
            'vars' => $vars,
            'client' => $client,
        ])
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        @include('log.template.datafiles', [
            'client' => $client,
        ])
    </div>
    <div class="col-md-6">
        @include('log.template.commands', [
            'commands' => $commands,
            'isAllowedToWorkWithCommands' => $isAllowedToWorkWithCommands
        ])
    </div>
</div>

@include('log.template.sysinfo', [
    'client' => $client,
])

@if(Helper::checkActionInRole('Commands'))
    @include('log.template.modal', [
        'client' => $client,
        'servers' => $servers,
        'columns' => $columns,
    ])
@endif

@include('log.template.comments')
