<div id="net_access_js" class="col-md-12">
    {!! Form::open() !!}
        <div class="form-group">
            <label style="font-weight: normal;">Allowed nets for user <strong>{{ $user->username }}</strong> <span class="glyphicon glyphicon-question-sign text-primary" title="By default user has access to all nets. Restrict access by selecting them in input box." data-toggle="tooltip" data-placement="right"></span></label>
            <div class="clearfix">
                {!! Form::select('group[]', $nets, $file, [
                    'style' => 'width: 100%;',
                    'multiple' => '',
                    'id' => 'group',
                ]) !!}
            </div>
        </div>
        <div class="form-group">
            <label class="checkbox" style="padding-left: 20px; font-weight: normal;">
                <input class="checkbox-allow-all" type="checkbox">
                Allow access to all
            </label>
        </div>
        {!! Form::button('update', 'Update', ['class' => 'btn btn-success']) !!}
    {!! Form::close() !!}
</div>

<script>
    $('#group').select2({
        placeholder: "Group..",
        tags: true,
        allowClear: true,
        tokenSeparators: [',', ' ']
    }).data('select2').$container.addClass("input-sm").css('padding', 0);
</script>
    