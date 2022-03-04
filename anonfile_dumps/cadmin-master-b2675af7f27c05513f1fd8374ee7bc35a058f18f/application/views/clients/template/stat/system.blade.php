<div class="modal fade" id="myModalSystem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content" id="SystemModalBody">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">SystemStat</h4>
			</div>
			<div class="modal-body">
				<div id="chart_div_system"></div>
			</div>
			<div class="modal-footer">
				{!! Form::button('send', 'Close', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) !!}
			</div>
		</div>
	</div>
</div>