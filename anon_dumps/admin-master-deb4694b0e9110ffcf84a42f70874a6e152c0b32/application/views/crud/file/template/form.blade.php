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
            Importance
        </td>
        <td>
            <div class="input-daterange input-group">
                <span class="input-group-addon" style="border-left-width: 1px;">Low</span>
                {!! Form::input('importance_low', $importance_low, [
                    'class' => 'form-control',
                    'type' => 'number',
                    'min' => 0,
                    'max' => Auth::instance()->get_user()->getDefaultMaxImportanceEdit()
                ]) !!}
                <span class="input-group-addon">High</span>
                {!! Form::input('importance_high', $importance_high ?: 100, [
                    'class' => 'form-control',
                    'type' => 'number',
                    'min' => 0,
                    'max' => Auth::instance()->get_user()->getDefaultMaxImportanceEdit()
                ]) !!}
            </div>
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
    <tr>
        <td>
            Group Include
        </td>
        <td>
            {!! Form::select('group_include[]', array_combine($group_include?:[], $group_include?:[]), $group_include, [
                'style' => 'width: 100%;',
                'multiple' => '',
                'id' => 'group_include',
            ]) !!}
        </td>
    </tr>
    <tr>
        <td>
            Group exclude
        </td>
        <td>
            {!! Form::select('group_exclude[]', array_combine($group_exclude?:[], $group_exclude?:[]), $group_exclude, [
                'style' => 'width: 100%;',
                'multiple' => '',
                'id' => 'group_exclude',
            ]) !!}
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

<script type="text/javascript">
    $('#group_exclude').select2({
        placeholder: "Group exclude",
        tags: true,
        allowClear: true,
        tokenSeparators: [',', ' ']
    }).data('select2').$container.addClass("input-sm").css('padding', 0);

    $('#group_include').select2({
        placeholder: "Group include",
        tags: true,
        allowClear: true,
        tokenSeparators: [',', ' ']
    }).data('select2').$container.addClass("input-sm").css('padding', 0);
</script>