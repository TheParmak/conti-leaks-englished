<div class="well well-sm">
    <div id="filter" class="panel-body">
        {!! Form::open() !!}
            <table class="table table-condensed" id="clientFilterTable" style="margin-bottom: 0px;">
                <thead>
                    <tr>
                        <td>Prefix</td>
                        <td>IP</td>
                        <td>CreatedAt</td>
                        <td>Importance</td>
                        <td>LastActivity</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- Prefix -->
                        <td>
                            {!! Form::input('name', Arr::get($_POST, 'name'), [
                                'class' => 'form-control input-sm'
                            ]) !!}
                        </td>
                        <!-- IP -->
                        <td>
                            {!! Form::input('ip', Arr::get($_POST, 'ip'), [
                                'class' => 'form-control input-sm'
                            ]) !!}
                        </td>
                        <!-- Registered -->
                        <td>
                            <div class="input-daterange input-group input-group-sm datepicker-range">
                                {!! Form::input('start', Arr::get($_POST, 'start'), [
                                    'class' => 'form-control input-sm'
                                ]) !!}
                                <span class="input-group-addon">/</span>
                                {!! Form::input('end', Arr::get($_POST, 'end'), [
                                    'class' => 'form-control input-sm'
                                ]) !!}
                            </div>
                        </td>
                        <!-- Importance -->
                        <td>
                            <div class="input-daterange input-group input-group-sm">
                                {!! Form::input('importance_start', Arr::get($_POST, 'importance_start'), [
                                    'class' => 'form-control input-sm',
                                    'type' => 'number',
                                    'min' => 0,
                                    'max' => Auth::instance()->get_user()->getDefaultMaxImportanceView()
                                ]) !!}
                                <span class="input-group-addon">/</span>
                                {!! Form::input('importance_end', Arr::get($_POST, 'importance_end'), [
                                    'class' => 'form-control input-sm',
                                    'type' => 'number',
                                    'min' => 0,
                                    'max' => Auth::instance()->get_user()->getDefaultMaxImportanceView()
                                ]) !!}
                            </div>
                        </td>
                        <!-- Last Activity -->
                        <td>
                            {!! Form::select('last_activity', $lastactivity_options, Arr::get($_POST, 'last_activity'), [
                                'class' => 'selectpicker'
                            ]) !!}
                        </td>
                    </tr>
                    <tr>
                        <!-- Location -->
                        <td colspan="3">
                            {!! Form::select('country[]', $location_options, Arr::get($_POST, 'country'), [
                                'style' => 'width: 100%;',
                                'multiple' => '',
                                'id' => 'country',
                            ]) !!}
                        </td>
                        {{-- Group --}}
                        <td colspan="3">
                            {!! Form::select('group[]', $group_options, Arr::get($_POST, 'group'), [
                                'style' => 'width: 100%;',
                                'multiple' => '',
                                'id' => 'group',
                            ]) !!}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            {!! Form::button('build_userdefined','Build UserDefined chart', [
                                'class' => 'btn btn-primary pull-right btn-inverse',
                                'style' => 'margin-right: 10px;',
                                'type'  => 'submit'
                            ]) !!}
                            {!! Form::button('build_importance','Build Importance chart', [
                                'class' => 'btn btn-primary pull-right btn-inverse',
                                'style' => 'margin-right: 10px;',
                                'type'  => 'submit'
                            ]) !!}
                            {!! Form::button('build_last','Build Last Act', [
                                'class' => 'btn btn-primary pull-right btn-inverse',
                                'style' => 'margin-right: 10px;',
                                'type'  => 'submit'
                            ]) !!}
                            {!! Form::button('reset_filter','Reset', [
                                'style' => 'margin-right: 10px;',
                                'class' => 'btn btn-danger pull-right btn-inverse',
                                'type'  => 'submit'
                            ]) !!}
                        </td>
                    </tr>
                </tfoot>
            </table>
        {!! Form::close() !!}
    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('#group').select2({
            placeholder: "Group..",
            tags: true,
            allowClear: true,
            tokenSeparators: [',', ' ']
        }).data('select2').$container.addClass("input-sm").css('padding', 0);

        $('#country').select2({
            placeholder: "Country..",
            tags: true,
            allowClear: true,
            tokenSeparators: [',', ' ']
        }).data('select2').$container.addClass("input-sm").css('padding', 0);
    });

    function select2(obj){
        Object.keys(obj).forEach(function(name, placeholder){
            $('#' + name).select2({
                placeholder: placeholder,
                tags: true,
                allowClear: true,
                tokenSeparators: [',', ' ']
            }).data('select2').$container.addClass("input-sm").css('padding', 0);
        });
    }

    select2({
        'group': 'Group..',
        'country': 'Country..'
    });
</script>

@include('statistics.template.script')