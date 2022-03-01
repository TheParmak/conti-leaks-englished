{!! Form::open(null, ['class' => 'form-horizontal']) !!}
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">Groups</h4>
            </div>
            <div class="modal-body">
                @include('TEMPLATE.errors', ['errors' => $errors])

                <div class="form-group form-group-sm">
                    <label for="count" class="col-sm-2 control-label">Incode</label>
                    <div class="col-sm-10">
                        {!! Form::input('incode', Arr::get($_POST, 'incode', trim($model->incode)), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="group" class="col-sm-2 control-label">Params</label>
                    <div class="col-sm-10">
                        {!! Form::input('params', Arr::get($_POST, 'params', trim($model->params)), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">Module</label>
                    <div class="col-sm-10">
                        {!! Form::input('module', Arr::get($_POST, 'module', trim($model->module)), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">Event</label>
                    <div class="col-sm-10">
                        {!! Form::input('event', Arr::get($_POST, 'event', trim($model->event)), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">Info</label>
                    <div class="col-sm-10">
                        {!! Form::input('info', Arr::get($_POST, 'info', trim($model->info) ?: '.*'), [
                            'class' => 'form-control input-sm',
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="sys_ver" class="col-sm-2 control-label">Interval</label>
                    <div class="col-sm-10">
                        {!! Form::input('interval', Arr::get($_POST, 'interval', trim($model->interval)), [
                            'class' => 'form-control input-sm',
                            'type' => 'number',
                        ]) !!}
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="btn-group btn-group-sm pull-right">
                    <a href="/crud/commandsevent/" class="btn-danger btn btn-inverse">
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