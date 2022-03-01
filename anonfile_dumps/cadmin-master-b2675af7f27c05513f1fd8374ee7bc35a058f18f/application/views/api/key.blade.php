{!! Form::open() !!}
<table class="table table-striped table-condensed">
    <thead>
        <tr>
            <td style="width: 1px;"></td>
            <td>ApiKey</td>
            <td>Ip</td>
            <td>Commands</td>
            <td>Pass</td>
            <td style="width: 1px;"></td>
        </tr>
    </thead>
    <tbody>
        @foreach($model as $m)
            <tr>
                <td>
                    <input type="checkbox" name="check[]" value="{{ $m->id }}">
                </td>
                <td>{{ $m->apikey }}</td>
                <td>
                    <ul>
                        @foreach($m->to_list('ip') as $ip)
                            <li>{{ $ip }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <ul>
                        @foreach($m->to_list('commands_allowed') as $command)
                            <li>{{ $command }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>{{ $m->pass }}</td>
                <td>
                    <a class="btn btn-inverse btn-primary btn-xs" href="/api/key/editor/{{ $m->id }}"><span class="glyphicon glyphicon-edit"></span></a>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">
                <div class="btn-group btn-group-sm pull-right">
                    {!! Form::button('del', '<span class="glyphicon glyphicon-trash"></span>', ['class' => 'btn btn-inverse btn-danger', 'type' => 'submit']) !!}
                    <a class="btn btn-inverse btn-success" href="/api/key/editor/"><span class="glyphicon glyphicon-plus"></span></a>
                </div>
            </td>
        </tr>
    </tfoot>
</table>
{!! Form::close() !!}