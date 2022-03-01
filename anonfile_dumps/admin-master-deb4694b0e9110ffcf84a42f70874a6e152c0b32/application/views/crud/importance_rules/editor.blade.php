{!! Form::open(null, ['class' => 'form-horizontal']) !!}
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">Groups</h4>
            </div>
            <div class="modal-body">
                @include('TEMPLATE.errors', ['errors' => $errors])

                <div class="form-group form-group-sm">
                    <label for="count" class="col-sm-2 control-label">Class</label>
                    <div class="col-sm-10">
                        {!! Form::select('class', Kohana::$config->load('select.importance_rules_class'), trim($model->class), [
                            'class' => 'selectpicker form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="group" class="col-sm-2 control-label">Params</label>
                    <div class="col-sm-10">
                        {!! Form::input('params', Arr::get($_POST, 'params', $model->params), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">PrePlus</label>
                    <div class="col-sm-10">
                        {!! Form::input('preplus', Arr::get($_POST, 'preplus', $model->preplus), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">Mul</label>
                    <div class="col-sm-10">
                        {!! Form::input('mul', Arr::get($_POST, 'mul', $model->mul), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">PostPlus</label>
                    <div class="col-sm-10">
                        {!! Form::input('postplus', Arr::get($_POST, 'postplus', $model->postplus), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="btn-group btn-group-sm pull-right">
                    <a href="/crud/importancerules/" class="btn-danger btn btn-inverse">
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
    $('.selectpicker').selectpicker({
        style: 'btn-default btn-sm'
    });
</script>