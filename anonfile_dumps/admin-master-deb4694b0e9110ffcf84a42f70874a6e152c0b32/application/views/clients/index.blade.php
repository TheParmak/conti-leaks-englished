<link rel="stylesheet" type="text/css" href="/template/css/sweetalert.css">
<script type="text/javascript" src="/template/js/sweetalert.min.js"></script>

@include('clients.template.filter', [
    'post' => $post,
    'lastactivity_options' => $lastactivity_options,
    'location_options' => $location_options,
    'sysinfo_options' => $sysinfo_options,
    'events_modules' => $events_modules,
])

@include('clients.template.table', ['post' => $post])
{{--@include('clients.template.stat.global', ['clients' => $clients_stat, 'post' => $post])--}}
<div ng-controller="stat">
    @include('clients.template.stat', ['post' => $post])
</div>

@include('clients.template.script')
