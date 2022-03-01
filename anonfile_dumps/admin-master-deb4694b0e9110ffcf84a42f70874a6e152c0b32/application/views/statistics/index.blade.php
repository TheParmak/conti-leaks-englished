@include('TEMPLATE.errors', ['errors' => $errors])

@include('statistics.template.filter', [
    'lastactivity_options' => $lastactivity_options,
    'location_options' => $location_options,
    'post' => $post
])

@include('statistics.template.chart', [
    'userdefinedChart' => $userdefinedChart,
    'lastActChart' => $lastActChart,
    'importanceChart' => $importanceChart
])