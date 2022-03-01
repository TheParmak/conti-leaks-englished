{!! Form::open(null, ['class' => 'form-horizontal']) !!}
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">Groups</h4>
            </div>
            <div class="modal-body">
                @include('TEMPLATE.errors', ['errors' => $errors])

                <div class="form-group form-group-sm">
                    <label for="count" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::input('name', Arr::get($group->as_array(), 'name', bin2hex(openssl_random_pseudo_bytes(50))), [
                            'class' => 'form-control input-sm',

                            'readonly'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="group" class="col-sm-2 control-label">Group</label>
                    <div class="col-sm-10">
                        {!! Form::input('groups', Arr::get($_POST, 'groups', $group->groups), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="country" class="col-sm-2 control-label">Country</label>
                    <div class="col-sm-10">
                        {!! Form::select('country[]', $location_options, Arr::get($_POST, 'country', $group->country), [
                            'class' => 'form-control input-sm',
                            'style' => 'width: 100%;',
                            'multiple' => '',
                            'id' => 'country',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        {!! Form::input('pass', Arr::get($_POST, 'pass', $group->pass), [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn-group btn-group-sm pull-right">
                    <a href="/groups/" class="btn-danger btn btn-inverse">
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

<script type="text/javascript">
    $(function() {
        $('#country').select2({
            placeholder: "Country..",
            tags: true,
            allowClear: true,
            tokenSeparators: [',', ' ']
        }).data('select2').$container.addClass("input-sm").css('padding', 0);
    });
</script>