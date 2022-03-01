<script type="text/javascript">
	$('.datepicker-range').datepicker({
		language: "ru",
		autoclose: true,
		todayHighlight: true,
		format: "yyyy/mm/dd"
	}).on('changeDate', function(dateEvent) {
		var start = $('#datepicker').datepicker('startDate');
	});
	$('.selectpicker').selectpicker({
        style: 'btn-default btn-sm'
    });

	$('#myModal').on('show.bs.modal', function () {
		$('.modal-body').css('max-height',$( window ).height()*0.8);
	});
</script>