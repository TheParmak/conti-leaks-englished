<div class="container">
    <div class="row">
        <div class="col-md-12">
            {!! Form::open() !!}
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <td colspan="15">
                                <input type="checkbox" id="select_all">
                                <div class="btn-group btn-group-sm pull-right">
                                    {!! Form::button('delete', '<span class="glyphicon glyphicon-trash"></span>', [
                                        'class' => 'btn btn-inverse btn-danger',
                                        'type' => 'submit'
                                    ]) !!}
                                    <a href="/crud/commandsevent/editor/" class="btn btn-success btn-inverse">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <td style="width: 1px;">
                                <span class="glyphicon glyphicon-check"></span>
                            </td>
                            <td>Incode</td>
                            <td>Params</td>
                            <td>Module</td>
                            <td>Event</td>
                            <td>Info</td>
                            <td>Interval</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody style="word-break: break-all;">
                        @foreach($model as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" name="check[]" value="{{ $item->id }}" style="margin-top: 0">
                                </td>
                                <td>{{ $item->incode }}</td>
                                <td>{{ $item->params }}</td>
                                <td>{{ $item->module }}</td>
                                <td>{{ $item->event }}</td>
                                <td>{{ $item->info }}</td>
                                <td>{{ $item->interval }}</td>
                                <td>
                                    <a href="/crud/commandsevent/editor/{{ $item->id }}" class="btn btn-primary pull-right btn-xs btn-inverse">
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
                                    <a href="/crud/commandsevent/editor/" class="btn btn-success btn-inverse">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            {!! Form::close() !!}
        </div>
    </div>
</div>

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