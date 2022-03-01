{!! Form::open(null, ['class' => 'form-horizontal']) !!}
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center">ApiKey</h4>
            </div>
            <div class="modal-body">

                <div class="form-group form-group-sm">
                    <label for="commands_allowed" class="col-sm-2 control-label">Commands</label>
                    <div class="col-sm-10">
                        {!! Form::input('commands_allowed', $model->commands_allowed, [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="ip" class="col-sm-2 control-label">Ip</label>
                    <div class="col-sm-10">
                        {!! Form::input('ip', $model->ip, [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="apikey" class="col-sm-2 control-label">ApiKey</label>
                    <div class="col-sm-10">
                        {!! Form::input('apikey', $model->apikey, [
                            'class' => 'form-control input-sm'
                        ]) !!}
                    </div>
                </div>

                <div class="form-group form-group-sm">
                    <label for="pass" class="col-sm-2 control-label">Pass</label>
                    <div class="col-sm-10">
                        {!! Form::input('pass', $model->pass, [
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