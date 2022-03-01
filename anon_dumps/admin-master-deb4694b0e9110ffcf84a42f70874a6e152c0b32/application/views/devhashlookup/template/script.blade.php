<script>
    $(document).ready(function() {
        var $scopeFilter = $('#filter');

        $('.selectpicker', $scopeFilter).selectpicker();
        $('.bootstrap-datepicker', $scopeFilter).datepicker({
            language: "ru",
            autoclose: true,
            todayHighlight: true,
            format: "yyyy/mm/dd"
        });
    });
</script>