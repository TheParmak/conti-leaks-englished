<div class="container">
    <div class="col-md-12">
        {!! Form::open() !!}
            <div class="input-group">
                <span class="input-group-addon" style="border-left-width: 1px;">Incode</span>
                {!! Form::input('incode', null, [
                    'class' => 'form-control input-sm',
                    'type' => 'number',
                    'required'
                ]) !!}
                <span class="input-group-addon">Params</span>
                {!! Form::input('params', null, [
                    'class' => 'form-control input-sm',
                ]) !!}
            </div>
            <div style="margin-bottom: 7px">
                <textarea placeholder="Input Clients.." id="textarea-comment" class="form-control" name="clients" rows="20"></textarea>
            </div>
            <div>
                {!! Form::button('upd', 'Add', [
                    'class' => 'btn btn-primary pull-right btn-sm btn-inverse',
                ]) !!}
            </div>
        {!! Form::close() !!}
    </div>
</div>