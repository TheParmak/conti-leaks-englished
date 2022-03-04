@if($importanceChart || $userdefinedChart || $lastActChart)
<div id="widget-chart" class="col-md-6 col-md-offset-3"
    @if($importanceChart)
        data-alt="Importance chart"
        data-var="Importance"
        data-xaxis="{{ HTML::chars(json_encode(array_keys($importanceChart))) }}"
        data-data="{{ HTML::chars(json_encode(array_values($importanceChart))) }}"
    @elseif($lastActChart)
	    data-alt="LastAct chart"
	    data-var="LastAct"
	    data-xaxis="{!! json_encode(array_keys($lastActChart)) !!}"
	    data-data="{!! json_encode(array_values($lastActChart)) !!}"
    @elseif($userdefinedChart)
        data-alt="UserDefined chart"
        data-var="UserDefined"
        data-xaxis="{{ json_encode(array_keys($userdefinedChart)) }}"
        data-data="{{ json_encode(array_values($userdefinedChart)) }}"
    @endif
></div>
@endif

<script src="/template/js/highcharts.js"></script>
<script>
$(document).ready(function() {
    var $widgetChart = $('#widget-chart');
    if ( $widgetChart.length ) {
        $widgetChart.highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: $widgetChart.data('alt')
            },
            xAxis: {
                categories: $widgetChart.data('xaxis'),
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Count clients'
                }
            },
            tooltip: {
                shared: true,
                useHTML: true,
                headerFormat: '<span style="font-size:10px">' + $widgetChart.data('var') + ': {point.key}</span><br />'
            },
            credits: {
                enabled: false
            },                
            series: [{
                name: 'Count clients',
                data: $widgetChart.data('data')
            }]
        });
    }
});
</script>
