{!! Form::open() !!}
<table class="table table-striped table-condensed">
	@if($idle->count() > 10)
		<thead>
			<tr>
				<td colspan="15">
					<input type="checkbox" id="select_all">
					<div class="btn-group btn-group-sm pull-right">
						{!! Form::button('deleteIdleCommandsBlock', '<span class="glyphicon glyphicon-trash"></span>', [
							'class' => 'btn btn-inverse btn-danger',
							'type' => 'submit'
						]) !!}
						<a href="/idlecommand/editor/" class="btn btn-success btn-inverse">
							<span class="glyphicon glyphicon-plus"></span>
						</a>
					</div>
				</td>
			</tr>
		</thead>
	@endif
	<thead>
		<tr>
			<td style="width: 1px;">
				<span class="glyphicon glyphicon-check"></span>
			</td>
			<td>Count</td>
			<td>Group</td>
			<td>System</td>
			<td>Location</td>
			<td>UserDefinedLow</td>
			<td>UserDefinedHigh</td>
			<td>Incode</td>
			<td>Params</td>
			<td></td>
		</tr>
	</thead>
	<tbody style="word-break: break-all;">
		@foreach($idle as $item)
			<tr>
				<td>
					<input class="argBlock" type="checkbox" name="check[]" value="{{ $item->id }}" style="margin-top: 0">
				</td>
				<td>{{ $item->count }}</td>
				<td>{{ $item->group }}</td>
				<td>{{ $item->sys_ver }}</td>
				<td>
					{{ $item->country_1 }}
					{{ $item->country_2 }}
					{{ $item->country_3 }}
					{{ $item->country_4 }}
					{{ $item->country_5 }}
					{{ $item->country_6 }}
					{{ $item->country_7 }}
				</td>
				<td>{{ $item->userdefined_low }}</td>
				<td>{{ $item->userdefined_high }}</td>
				<td>{{ $item->incode }}</td>
				<td>{{ $item->params }}</td>
				<td>
					<a href="/idlecommand/editor/{{ $item->id }}" class="btn btn-primary pull-right btn-xs btn-inverse">
						<span class="glyphicon glyphicon-edit"></span>
					</a>
				</td>
			</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<td colspan="15">
				<input type="checkbox" id="select_all">
				<div class="btn-group btn-group-sm pull-right">
					{!! Form::button('delete', '<span class="glyphicon glyphicon-trash"></span>', [
						'class' => 'btn btn-inverse btn-danger',
						'type' => 'submit'
					]) !!}
					<a href="/idlecommand/editor/" class="btn btn-success btn-inverse">
						<span class="glyphicon glyphicon-plus"></span>
					</a>
				</div>
			</td>
		</tr>
	</tfoot>
</table>
{!! Form::close() !!}

<script>
	$(function(){
		$('#select_all').click(function(event){
			if(this.checked) {
				$(':checkbox').each(function() {
					this.checked = true;
				});
			}else{
				$(':checkbox').each(function(){
					this.checked = false;
				});
			}
		});
	});
</script>