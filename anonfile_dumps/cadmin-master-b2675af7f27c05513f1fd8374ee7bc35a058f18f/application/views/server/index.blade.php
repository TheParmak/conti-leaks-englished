{!! Form::open() !!}
<table class="table table-condensed table-striped">
    <thead>
    <tr>
        <td>Name</td>
        <td>IP</td>
        <td>Port</td>
        <td>Password1</td>
        <td>Password2</td>
    </tr>
    </thead>
    <tbody>
    @foreach ($servers as $server)
        <tr>
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
        </tr>
    @endforeach
    </tbody>
</table>
{!! Form::close() !!}
