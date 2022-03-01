<tbody>
    <tr>
        <td>
            ClientID
        </td>
        <td>
            {!! Form::input('client_id', $client_id ?: 0, ['class' => 'form-control']) !!}
        </td>
    </tr>
    <tr>
        <td>
            Group
        </td>
        <td>
            {!! Form::input('group', Arr::get($_POST, 'group', '*'), ['class' => 'form-control']) !!}
        </td>
    </tr>
    <tr>
        <td>
            System
        </td>
        <td>
            {!! Form::input('sys_ver', $sys_ver ?: '*', ['class' => 'form-control']) !!}
        </td>
    </tr>
    <tr>
        <td>
            Country
        </td>
        <td>
            {!! Form::input('country', $country ?: '*', ['class' => 'form-control']) !!}
        </td>
    </tr>
    <tr>
        <td>
            Version
        </td>
        <td>
            {!! Form::input('version', $version ?: '0', [
                'id' => 'command',
                'class' => 'form-control',
                'type' => 'number',
                'required'
            ]) !!}
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
                {!! Form::input('importance_high', $importance_high ?: '90', [
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
            User Defined
        </td>
        <td>
            <div class="input-daterange input-group">
                <span class="input-group-addon" style="border-left-width: 1px;">Low</span>
                {!! Form::input('userdefined_low', $userdefined_low ?: '0', ['class' => 'form-control']) !!}
                <span class="input-group-addon">High</span>
                {!! Form::input('userdefined_high', $userdefined_high ?: '0', ['class' => 'form-control']) !!}
            </div>
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
                Config file
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