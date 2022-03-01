@if(count($log))
    <table class="table table-bordered table-condensed">
        <thead class="well">
            <tr>
                <th>CreatedAt</th>
                <th>Info</th>
                <th>Type</th>
                <th>Command</th>
            </tr>
        </thead>
        <tbody class="well">
            @foreach($log as $item)
                <tr>
                    <td>{{ $item['created_at'] }}</td>
                    <td>{{ $item['info'] }}</td>
                    <td>{{ $item['type'] }}</td>
                    <td>{{ $item['command'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="well">
            <tr>
                <td colspan="4" class="text-center">
                    @if( Helper::checkActionInRole('Remove'))
                        <div class="pull-right">
                            {!! Form::open('/remove?redirect_to=https://' . $_SERVER['HTTP_HOST'] . '/log/' . $client->id) !!}
                            {!! Form::hidden('clientid', $client->id) !!}
                            {!! Form::button('DeleteLog', 'DeleteLog 70%', [
                                'type' => 'submit',
                                'class' => 'btn btn-danger pull-right btn-inverse pull-right btn-xs',
                            ]) !!}
                            {!! Form::close() !!}
                        </div>
                    @endif
                    {!! $pagination !!}
                </td>
            </tr>
        </tfoot>
    </table>
@elseif(!Session::instance()->get('hideEmptyFields'))
    <table class="table table-bordered">
        <thead class="well">
            <tr>
                <th>CreatedAt</th>
                <th>Info</th>
                <th>Type</th>
                <th>Command</th>
            </tr>
        </thead>
        <tbody class="well">
            <tr>
                <td colspan="4" style="text-align: center;">
                    <h4 class="text-danger">No logs</h4>
                </td>
            </tr>
        </tbody>
    </table>
@endif
