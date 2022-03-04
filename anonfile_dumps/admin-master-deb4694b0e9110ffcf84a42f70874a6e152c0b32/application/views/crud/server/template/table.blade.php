{!! Form::open() !!}
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <td>
                <span class="glyphicon glyphicon-check"></span>
            </td>
            <td>Name</td>
            <td>IP</td>
            <td>Port</td>
            <td>Password1</td>
            <td>Password2</td>
            <td></td>
        </tr>
    </thead>
    <tbody>
    @foreach ($servers as $server)
        <tr>
            <td>
                <input type="checkbox" name="check[]" value="{{ $server->id }}">
            </td>
            <td>
                {{ $server->ip }}:{{ $server->port }}
            </td>
            <td>
                {{ $server->ip }}
            </td>
            <td>
                {{ $server->port }}
            </td>
            <td>
                {{ $server->password1 }}
            </td>
            <td>
                {{ $server->password2 }}
            </td>
            <td>
                <a name="push_back" type="button" class="btn btn-inverse btn-xs btn-primary pull-right" href="/crud/server/editor/{{ $server->id }}" title="Edit {{ $server->id }}">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>
                <button class="btn btn-inverse btn-xs btn-primary pull-right btn-get-list" style="margin-right: 10px;" type="button" title="GetList {{ $server->id }}" data-server-name="{{ $server->id }}">
                    <span class="glyphicon glyphicon-list"></span>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
		<tfoot>
			<tr>
                <td colspan="7">
					<input type="checkbox" id="select_all">
					<a name="push_back" type="button" class="btn btn-inverse btn-primary pull-right" href="/crud/server/editor/">
						<span class="glyphicon glyphicon-new-window"></span>
					</a>
					<button type="submit" name="delete" title="Delete" class="btn btn-inverse btn-danger pull-right" style="margin-right: 10px;">
						<span class="glyphicon glyphicon-trash"></span>
					</button>
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

@include('crud.server.template.get_list')
