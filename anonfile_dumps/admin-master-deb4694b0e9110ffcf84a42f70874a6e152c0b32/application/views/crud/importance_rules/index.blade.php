{!! Form::open() !!}
<table class="table table-striped table-condensed">
    <thead>
    <tr>
        <td style="width: 1px;">
            <span class="glyphicon glyphicon-check"></span>
        </td>
        <td>Class</td>
        <td>Params</td>
        <td>PrePlus</td>
        <td>Mul</td>
        <td>PostPlus</td>
        <td></td>
    </tr>
    </thead>
    <tbody style="word-break: break-all;">
    @foreach($model as $item)
        <tr>
            <td>
                <input type="checkbox" name="check[]" value="{{ $item->id }}" style="margin-top: 0">
            </td>
            <td>{{ $item->class }}</td>
            <td>{{ $item->params }}</td>
            <td>{{ $item->preplus }}</td>
            <td>{{ $item->mul }}</td>
            <td>{{ $item->postplus }}</td>
            <td>
                <a href="/crud/importancerules/editor/{{ $item->id }}" class="btn btn-primary pull-right btn-xs btn-inverse">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="15">
            <input type="checkbox" id="select_all">
            <div class="btn-group btn-group-sm pull-right">
                {!! Form::button('delete', '<span class="glyphicon glyphicon-trash"></span>', [
                    'class' => 'btn btn-inverse btn-danger',
                    'type' => 'submit'
                ]) !!}
                <a href="/crud/importancerules/editor/" class="btn btn-success btn-inverse">
                    <span class="glyphicon glyphicon-plus"></span>
                </a>
            </div>
        </td>
    </tr>
    </tfoot>
</table>
{!! Form::close() !!}

<script>
    $(function(){
        $('#select_all').click(function(event){
            if(this.checked) {
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            }else{
                $(':checkbox').each(function(){
                    this.checked = false;
                });
            }
        });
    });
</script>