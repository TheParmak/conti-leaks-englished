{!! Form::open() !!}
<table class="table table-striped table-condensed">
    <thead>
        <tr>
            <td style="width: 1px;">
                <span class="glyphicon glyphicon-check"></span>
            </td>
            <td>Name</td>
            <td>Groups</td>
            <td>Password</td>
            <td></td>
        </tr>
    </thead>
    <tbody style="word-break: break-all;">
        @foreach($groups as $item)
            <tr>
                <td>
                    <input type="checkbox" name="check[]" value="{{ $item->id }}" style="margin-top: 0">
                </td>
                <td>
                    <a target="_blank" href="/groups/statistics/{{ $item->name }}">{{ $item->name }}</a>
                </td>
                <td>{{ $item->groups ?: '*' }}</td>
                <td>{{ $item->pass }}</td>
                <td>
                    <a href="/groups/editor/{{ $item->id }}" class="btn btn-primary pull-right btn-xs btn-inverse">
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
                <a href="/groups/editor/" class="btn btn-success btn-inverse">
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