@if($commands->count())
    <table class="table table-bordered table-condensed">
        <thead style="font-weight: bold" class="well">
        <tr>
            @if($isAllowedToWorkWithCommands)
                <td style="width: 1px;">
                    <span class="glyphicon-check glyphicon"></span>
                </td>
            @endif
            <td>Command</td>
            <td>Param</td>
        </tr>
        </thead>
        <tbody class="well" style="word-break: break-all;">
            @foreach($commands as $item)
                <tr>
                    @if($isAllowedToWorkWithCommands)
                        <td>
                            {!! Form::checkbox('id-delete-commands[]', $item->id, false, [
                                'form' => 'my_command_form'
                            ]) !!}
                        </td>
                    @endif
                    <td>{{ $item->incode }}</td>
                    <td>{{ $item->params }}</td>
                </tr>
            @endforeach
        </tbody>
        @if($isAllowedToWorkWithCommands)
            <tfoot class="well">
            <tr>
                <td colspan="3">
                    {!! Form::open(null, ['id' => 'my_command_form']) !!}
                    <div class="btn-group btn-group-xs pull-right">
                        {!! Form::button('delete-command', 'Delete',[
                            'type' => 'submit',
                            'class' => 'btn btn-danger btn-inverse',
                        ]) !!}
                        {!! Form::button('CreateUserAdv', 'Create user (adv)',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {!! Form::button('CreateUser', 'Create user',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {!! Form::button('Start-WG', 'Start WG',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {!! Form::button('RDP-patch', 'RDP patch',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {!! Form::button('VNC', 'VNC',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {{--MODAL--}}
                        {!! Form::button('push_back', 'Push Back',[
                            'type' => 'button',
                            'class' => 'btn btn-primary btn-inverse',
                            'data-toggle' => 'modal',
                            'data-target' => '#myModal',
                        ]) !!}
                        {!! Form::button('button', 'BC tool',[
                            'type' => 'button',
                            'class' => 'btn btn-primary btn-inverse',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalServer',
                        ]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
            </tfoot>
        @endif
    </table>
@elseif(!Session::instance()->get('hideEmptyFields'))
    <table class="table table-bordered table-condensed">
        <thead style="font-weight: bold" class="well">
        <tr>
            @if($isAllowedToWorkWithCommands)
                <td style="width: 1px;">
                    <span class="glyphicon-check glyphicon"></span>
                </td>
            @endif
            <td>Command</td>
            <td>Param</td>
        </tr>
        </thead>
        <tbody class="well">
            <tr>
                <td colspan="3" style="text-align: center;">
                    <h4 class="text-danger">No commands</h4>
                </td>
            </tr>
        </tbody>
        @if($isAllowedToWorkWithCommands)
            <tfoot class="well">
            <tr>
                <td colspan="3">
                    {!! Form::open(null, ['id' => 'my_command_form']) !!}
                    <div class="btn-group btn-group-xs pull-right">
                        {!! Form::button('delete-command', 'Delete',[
                            'type' => 'submit',
                            'class' => 'btn btn-danger btn-inverse',
                        ]) !!}
                        {!! Form::button('CreateUserAdv', 'Create user (adv)',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {!! Form::button('CreateUser', 'Create user',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {!! Form::button('Start-WG', 'Start WG',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {!! Form::button('RDP-patch', 'RDP patch',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {!! Form::button('VNC', 'VNC',[
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-inverse',
                        ]) !!}
                        {{--MODAL--}}
                        {!! Form::button('push_back', 'Push Back',[
                            'type' => 'button',
                            'class' => 'btn btn-primary btn-inverse',
                            'data-toggle' => 'modal',
                            'data-target' => '#myModal',
                        ]) !!}
                        {!! Form::button('button', 'BC tool',[
                            'type' => 'button',
                            'class' => 'btn btn-primary btn-inverse',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalServer',
                        ]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
            </tfoot>
        @endif
    </table>
@endif