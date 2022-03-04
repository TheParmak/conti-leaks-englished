<div class="well well-sm">
    <div class="panel-body">
        {!! Form::open() !!}
            <div style="margin-bottom: 7px">
                <textarea placeholder="Input comment.." id="textarea-comment" class="form-control" name="comment" rows="6">{{ $comment->value }}</textarea>
            </div>
            <div>
                {!! Form::button('update-comment', 'Update', [
                    'class' => 'btn btn-primary pull-right btn-sm btn-inverse',
                ]) !!}
            </div>
        {!! Form::close() !!}
    </div>
</div>
    
<p>&nbsp;</p>
