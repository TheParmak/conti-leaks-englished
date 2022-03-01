<tbody>
    <tr>
        <td>
            Client
        </td>
        <td>
            {!! Form::input('client', $client, ['class' => 'form-control']) !!}
        </td>
    </tr>
    <tr>
        <td>
            Name
        </td>
        <td>
            {!! Form::input('filename', $filename, ['class' => 'form-control']) !!}
        </td>
    </tr>
    <tr>
        <td>
            Priority
        </td>
        <td>
            {!! Form::input('priority', $priority, [
                'class' => 'form-control',
                'type' => 'number'
            ]) !!}
        </td>
    </tr>
    <tr>
        <td>
            User defined
        </td>
        <td>
            <div class="input-daterange input-group">
                <span class="input-group-addon" style="border-left-width: 1px;">Low</span>
                {!! Form::input('userdefined_low', $userdefined_low, [
                    'class' => 'form-control',
                    'type' => 'number',
                    'min' => 0,
                ]) !!}
                <span class="input-group-addon">High</span>
                {!! Form::input('userdefined_high', $userdefined_high ?: 100, [
                    'class' => 'form-control',
                    'type' => 'number',
                    'min' => 0,
                ]) !!}
            </div>
        </td>
    </tr>
    <tr>
        <td>
            Group
        </td>
        <td>
            {!! Form::input('group', $group, ['class' => 'form-control']) !!}
        </td>
    </tr>
    <tr>
        <td>
            System
        </td>
        <td>
            {!! Form::input('sys_ver', $sys_ver, ['class' => 'form-control']) !!}
        </td>
    </tr>
    <tr>
        <td>
            Country
        </td>
        <td>
            {!! Form::input('country', $country, ['class' => 'form-control']) !!}
        </td>
    </tr>
    @if($file)
        <tr>
            <td>
                File
            </td>
            <td>
                {!! $file !!}
            </td>
        </tr>
    @endif
</tbody>