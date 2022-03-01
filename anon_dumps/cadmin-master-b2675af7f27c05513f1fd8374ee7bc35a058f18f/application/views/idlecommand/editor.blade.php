{!! Form::open(null, ['class' => 'form-horizontal']) !!}
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">IdleCommand</h4>
            </div>
            <div class="modal-body">
                @include('TEMPLATE.errors', ['errors' => $errors])

                <div class="form-group form-group-sm">
                    <label for="count" class="col-sm-2 control-label">Count</label>
                    <div class="col-sm-10">
                        {!! Form::input('count', Arr::get($_POST, 'count', $idle->count), [
                            'class' => 'form-control input-sm',
                            'type' => 'number'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="group" class="col-sm-2 control-label">Group</label>
                    <div class="col-sm-10">
                        {!! Form::input('group', Arr::get($_POST, 'group', $idle->group ?: '*'), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">System</label>
                    <div class="col-sm-10">
                        {!! Form::input('sys_ver', Arr::get($_POST, 'sys_ver', $idle->sys_ver ?: '*'), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label class="col-sm-2 control-label">UserDefined</label>
                    <div class="col-sm-10">
                        <div class="input-daterange input-group">
                            <span class="input-group-addon" style="border-left-width: 1px;">Low</span>
                            {!! Form::input('userdefined_low', Arr::get($_POST, 'userdefined_low', $idle->userdefined_low ?: '0'), [
                                'class' => 'form-control input-sm'
                            ]) !!}
                            <span class="input-group-addon">High</span>
                            {!! Form::input('userdefined_high', Arr::get($_POST, 'userdefined_high', $idle->userdefined_high ?: '0'), [
                                'class' => 'form-control input-sm'
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="incode" class="col-sm-2 control-label">Incode</label>
                    <div class="col-sm-10">
                        {!! Form::input('incode', Arr::get($_POST, 'incode', $idle->incode), [
                            'id' => 'command',
                            'class' => 'form-control input-sm',
                            'type' => 'number',
                            'required'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="params" class="col-sm-2 control-label">Params</label>
                    <div class="col-sm-10">
                        {!! Form::input('params', Arr::get($_POST, 'params', $idle->params), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label class="col-sm-2 control-label">
                        Location <span class="glyphicon glyphicon-question-sign text-primary" title="You can specify multiple locations by separating them using space symbol, for example, &quot;DE FR IT ES&quot;." data-toggle="tooltip" data-placement="right"></span>
                    </label>
                    <div class="col-sm-10 form-inline">
                        {!! Form::input('country_1', Arr::get($_POST, 'country_1', $idle->country_1 ?: '*'), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                        {!! Form::input('country_2', Arr::get($_POST, 'country_2', $idle->country_2), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                        {!! Form::input('country_3', Arr::get($_POST, 'country_3', $idle->country_3), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                    <div class="col-sm-offset-2 col-sm-10 form-inline" style="margin-top: 2px;">
                        {!! Form::input('country_4', Arr::get($_POST, 'country_4', $idle->country_4), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                        {!! Form::input('country_5', Arr::get($_POST, 'country_5', $idle->country_5), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                        {!! Form::input('country_6', Arr::get($_POST, 'country_6', $idle->country_6), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                    <div class="col-sm-offset-2 col-sm-10 form-inline" style="margin-top: 2px;">
                        {!! Form::input('country_7', Arr::get($_POST, 'country_7', $idle->country_7), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn-group btn-group-sm pull-right">
                    <a href="/idlecommand/" class="btn-danger btn btn-inverse">
                        <span class="fa fa-flip-horizontal fa-sign-out"></span>
                    </a>
                    <button type="submit" name="apply" class="btn-success btn btn-inverse">
                        <span class="glyphicon glyphicon-ok"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
{!! Form::close() !!}

<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
        $('.datepicker-range').datepicker({
            language: "ru",
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd"
        });

        // http://stackoverflow.com/questions/24981072/bootstrap-datepicker-empties-field-after-selecting-current-date
        $('.datepicker-range :input').each(function() {
            if ('' != $(this).val()) {
                $(this).on('show', function(e) {
                    if ( e.date ) {
                        $(this).data('stickyDate', e.date);
                    } else {
                        $(this).data('stickyDate', null);
                    }
                });
                $(this).on('hide', function(e){
                    var stickyDate = $(this).data('stickyDate');

                    if ( !e.date && stickyDate ) {
                        $(this).datepicker('setDate', stickyDate);
                        $(this).data('stickyDate', null);
                    }
                });
            }
        });
    });
</script>