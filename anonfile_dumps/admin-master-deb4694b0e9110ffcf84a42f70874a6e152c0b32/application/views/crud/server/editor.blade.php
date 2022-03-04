{!! Form::open() !!}
    @if(isset($errors))
        @foreach($errors as $item)
            <div class="alert alert-danger small">{{ $item }}</div>
        @endforeach
    @endif
	<table class="table">
		<tbody>
			<tr>
				<td>
					IP
				</td>
				<td>
					{!! Form::input('ip', Arr::get($post, 'ip', $server->ip), [
						'class' => 'form-control',
						'id' => 'argIP'
					]) !!}
				</td>
			</tr>
			<tr>
				<td>
					Port
				</td>
				<td>
					{!! Form::input('port', Arr::get($post, 'port', $server->port), [
						'class' => 'form-control'
					]) !!}
				</td>
			</tr>
			<tr>
				<td>
					Password1
				</td>
				<td>
					{!! Form::input('password1', Arr::get($post, 'password1', $server->password1), [
						'class' => 'form-control'
					]) !!}
				</td>
			</tr>
			<tr>
				<td>
					Password2
				</td>
				<td>
					{!! Form::input('password2', Arr::get($post, 'password2', $server->password2), [
						'class' => 'form-control'
					]) !!}
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">
					<button type="submit" name="apply" class="btn-primary btn pull-right">Apply</button>
				</td>
			</tr>
		</tfoot>
	</table>
{!! Form::close() !!}

<script src="/template/js/jquery-mask.js"></script>
<script>
	$('#ip').mask('099.099.099.099');
</script>