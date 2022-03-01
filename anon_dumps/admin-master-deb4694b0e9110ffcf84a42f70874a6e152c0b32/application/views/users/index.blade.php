{!! Form::open() !!}
<table class="table table-striped table-condensed">
	<thead>
	<tr style="font-weight: bold">
		<td>
			<span class="glyphicon glyphicon-check"></span>
		</td>
		<td>Login</td>
		<td>Role</td>
        @if (Helper::checkActionInRole('See user net list'))
		    <td>Groups</td>
        @endif
		<td></td>
	</tr>
	</thead>
	<tbody>
	@foreach($users as $user)
		<tr>
			<td>
				<input type="checkbox" name="check[]" value="{{ $user->id }}">
			</td>
			<td>
				{{ $user->username }}
			</td>
			<td>
				<ul style="margin-bottom: 0">
					@foreach($user->roles->find_all() as $role)
						@if($role->id != '1')
							<li>
								{{ $role->name }}
							</li>
						@endif
					@endforeach
				</ul>
			</td>
            @if (Helper::checkActionInRole('See user net list'))
			<td>
				@if($file_path = Kohana::find_file('net_access', $user->id, 'json'))
					<ul style="margin-bottom: 0">
                        @foreach(json_decode(file_get_contents($file_path)) as $net)
                            <li>{{ $net }}</li>
                        @endforeach
					</ul>
				@else
					<ul style="margin-bottom: 0">
                        <li>All</li>
                    </ul>
				@endif
			</td>
            @endif
			<td>
				<a class="btn btn-primary pull-right btn-inverse btn-xs" style="margin-left: 10px;" href="/users/editor/{{ $user->id }}">
					<span class="glyphicon glyphicon-edit"></span>
				</a>
				@if(Helper::checkActionInRole('Edit user net list'))
					<a class="btn btn-primary pull-right btn-inverse btn-xs" href="/users/groups/{{ $user->id }}" style="margin-left: 10px;">
						Groups
					</a>
				@endif
				@if ( Helper::checkActionInRole('Userslogs') )
                    <a class="btn btn-primary pull-right btn-inverse btn-xs" style="margin-left: 10px;" href="/userslogs?find_user={{ $user->username }}">
                        View Logs
                    </a>
                @endif
				@if ( Helper::checkActionInRole('ActiveSessionsAndLastLogins') )
                    <a class="btn btn-primary pull-right btn-inverse btn-xs" style="margin-left: 10px;" href="/users/lastlogins/{{ $user->id }}">
                        Last logins
                    </a>
                    <a class="btn btn-primary pull-right btn-inverse btn-xs" style="margin-left: 10px;" href="/users/activesessions/{{ $user->id }}">
                        Active sessions
                    </a>
                @endif
			</td>
		</tr>
	@endforeach
	</tbody>
	<tfoot>
	<tr>
		<td colspan="{{ Helper::checkActionInRole('See user net list') ? '5' : '4'  }}">
			<input type="checkbox" id="select_all">
			<div class="btn-group btn-group-sm pull-right">
				{!! Form::button('del', '<span class="glyphicon glyphicon-trash"></span>', [
                    'class' => 'btn btn-xs btn-danger',
                    'type' => 'submit',
                ]) !!}

				<a href="/users/editor/" class="btn btn-success">
					<span class="glyphicon glyphicon-plus"></span>
				</a>
			</div>
		</td>
	</tr>
	</tfoot>
</table>
{!! Form::close() !!}

<script>
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
</script>