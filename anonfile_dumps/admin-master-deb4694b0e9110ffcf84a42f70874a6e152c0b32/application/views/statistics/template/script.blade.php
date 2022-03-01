<script>
    $(document).ready(function() {
        var $scopeFilter = $('#filter');

        $('.selectpicker', $scopeFilter).selectpicker({
            style: 'btn-default btn-sm'
        });
        $('.datepicker-range', $scopeFilter).datepicker({
            language: "ru",
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd"
        });
        $('.bootstrap-datepicker', $scopeFilter).datepicker({
            language: "ru",
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd"
        });
    });
</script>