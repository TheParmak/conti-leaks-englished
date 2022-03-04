{!! Form::open(null, ['id' => 'file_list']) !!}
<table class="table table-condensed table-striped">
    <thead>
    <tr style="font-weight: bold">
        <td>Client</td>
        <td>Name</td>
        <td>Priority</td>
        <td>UserDefinedLow</td>
        <td>UserDefinedHigh</td>
        <td>Group</td>
        <td>System</td>
        <td>Location</td>
        <td></td>
    </tr>
    </thead>
    <tbody>
    @foreach($files as $file)
        <tr>
            <td>{{ $file->client_id }}</td>
            <td>{{ $file->filename }}</td>
            <td>{{ $file->priority }}</td>
            <td>{{ $file->userdefined_low }}</td>
            <td>{{ $file->userdefined_high }}</td>
            <td>{{ $file->group }}</td>
            <td>{{ $file->sys_ver }}</td>
            <td>{{ $file->country }}</td>
            <td>
                <a href="/download/index/{{ $file->id }}" class="btn btn-primary pull-right btn-xs btn-inverse">
                    <span class="glyphicon glyphicon-download-alt"></span>
                </a>
                <a href="/crud/file/editor/{{ $file->id }}" class="btn btn-primary pull-right btn-xs btn-inverse" style="margin-right: 10px;">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="11">
            <button type="button" class="btn btn-success pull-right btn-inverse" data-toggle="modal" data-target="#myModal">
                <span class="glyphicon glyphicon-upload"></span>
            </button>

            <button type="submit" name="back_del" class="btn btn-danger pull-right btn-inverse" style="margin-right: 10px;">
                Delete bak files
            </button>
        </td>
    </tr>
    </tfoot>
</table>
{!! Form::close() !!}

@include('crud.file.template.modal', ['errors' => $errors])
@include('crud.file.template.script')