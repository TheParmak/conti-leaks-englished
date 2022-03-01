{!! Form::open() !!}
<table class="table table-striped table-condensed">
	<thead>
		<tr>
			<td>
				<span class="glyphicon glyphicon-check"></span>
			</td>
			<td>
				Client
			</td>
			<td>
				Group
			</td>
			<td>
				System
			</td>
			<td>
				Country
			</td>
			<td>
				Code
			</td>
			<td>
				Params
			</td>
            <td>
                ResultCode
            </td>
            <td>
                CreatedAt
            </td>
            <td>
                ResultedAt
            </td>
		</tr>
	</thead>
	<tbody>
		@foreach($commands as $command)
			<tr>
				<td>
					<input type="checkbox" name="check[]" value="{{ $command->id }}">
				</td>
				<td>
					<a href="/log/{{ $command->client_id }}">
						{{ $command->client->getFullName() }}
					</a>
				</td>
				<td>
					{{ $command->client->group }}
				</td>
				<td>
					{{ $command->client->sys_ver }}
				</td>
				<td>
					{{ $command->client->country }}
				</td>
				<td>
					{{ $command->incode }}
				</td>
				<td>
					{{ $command->params }}
				</td>
                <td>
                    {{ $command->result_code }}
                </td>
                <td>
                    {{ $command->created_at }}
                </td>
                <td>
                    {{ $command->resulted_at }}
                </td>
			</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10">
				<input type="checkbox" id="select_all">
				<button name="push_back" type="button" class="btn btn-primary pull-right btn-inverse" data-toggle="modal" data-target="#myModal">Push Back</button>
				<button name="push" type="submit" style="margin-right: 10px" class="btn btn-primary pull-right btn-inverse" disabled>Push</button>
				<button type="submit" name="delete" title="Delete" class="btn btn-danger pull-right btn-inverse" style="margin-right: 10px;">
					<span class="glyphicon glyphicon-trash"></span>
				</button>
				<button type="submit" name="delete_all" title="Delete All" class="btn btn-danger pull-right btn-inverse" style="margin-right: 10px;">
					Delete All
				</button>
			</td>
		</tr>
	</tfoot>
</table>
{!! Form::close() !!}

{!! $pagination !!}

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="myModalLabel">Create command</h4>
			</div>
			<div class="modal-body">
                @include('TEMPLATE.errors', ['errors' => $errors])
                {!! Form::open(null, ['class' => 'form-horizontal', 'id' => 'addCommand']) !!}
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">
                            Client
                        </label>
                        <div class="col-sm-10">
                            {!! Form::input('client_id', Arr::get($post, 'client_id'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">
                            Code
                        </label>
                        <div class="col-sm-10">
                            {!! Form::input('incode', Arr::get($post, 'incode'), ['id' => 'command', 'class' => 'form-control', 'type' => 'number', 'required']) !!}
                        </div>
                    </div>
                    <div class="form-group form-group-sm">
                        <label class="col-sm-2 control-label">
                            Params
                        </label>
                        <div class="col-sm-10">
                            {!! Form::input('params', Arr::get($post, 'params'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                {!! Form::close() !!}
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-success" name="create" title="Create" form="addCommand">Create</button>
			</div>
		</div>
	</div>
</div>

@include('commands.script', ['errors' => $errors])