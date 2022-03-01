{!! Form::open() !!}
	<table class="table table-condensed table-striped">
		<thead>
		<tr>
			<td>
				<span class="glyphicon glyphicon-check"></span>
			</td>
			<td>
				CreatedAt
			</td>
			<td>
				Client
			</td>
			<td>
				Group
			</td>
			<td>
				Country
			</td>
			<td>
				Version
			</td>
		</tr>
		</thead>
		<tbody>
        @foreach($configs as $config)
			<tr>
				<td>
					<input type="checkbox" name="check[]" value="{{ $config->id }}">
				</td>
				<td>
                    {{ $config->created_at }}
				</td>
				<td>
                    {!! $config->client->getLink() !!}
				</td>
				<td>
                    {{ $config->group }}
				</td>
				<td>
                    {{ $config->country }}
				</td>
				<td>
                    {{ $config->version }}
				</td>
			</tr>
		@endforeach
		</tbody>
		<tfoot>
		<tr>
			<td colspan="6">
				<input type="checkbox" id="select_all">
				<div class="btn-group btn-group-sm pull-right">
					{!! Form::button('delete', '<span class="glyphicon glyphicon-trash"></span>', [
						'class' => 'btn btn-inverse btn-danger',
						'type' => 'submit',
						'title' => 'Delete',
					]) !!}
					<button name="push_back" type="button" class="btn btn-success btn-inverse" data-toggle="modal" data-target="#myModal">
						<span class="glyphicon glyphicon-plus"></span>
					</button>
				</div>
			</td>
		</tr>
		</tfoot>
	</table>
{!! Form::close() !!}