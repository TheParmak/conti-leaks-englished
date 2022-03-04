<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Stat</h4>
			</div>
			<div class="modal-body">
				@if(isset($post['get_stat']))
					{!! Request::factory('/globalinfo')
						->method(Request::POST)
						->post(['clients' => $clients])
						->execute() !!}
				@endif
			</div>
			<div class="modal-footer">
                {!! Form::button('send', 'Close', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) !!}
			</div>
		</div>
	</div>
</div>