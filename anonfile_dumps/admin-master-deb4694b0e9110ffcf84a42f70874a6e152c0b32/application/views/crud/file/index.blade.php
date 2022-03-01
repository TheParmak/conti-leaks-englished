{!! Form::open(null, ['id' => 'file_list']) !!}
<table class="table table-condensed table-striped">
    <thead>
    <tr style="font-weight: bold">
        <td>Client</td>
        <td>Name</td>
        <td>Priority</td>
        <td>ImportanceLow</td>
        <td>ImportanceHigh</td>
        <td>UserDefinedLow</td>
        <td>UserDefinedHigh</td>
        <td>GroupInclude</td>
        <td>GroupExclude</td>
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
            <td>{{ $file->importance_low }}</td>
            <td>{{ $file->importance_high }}</td>
            <td>{{ $file->userdefined_low }}</td>
            <td>{{ $file->userdefined_high }}</td>
            <td>{{ $file->group_include }}</td>
            <td>{{ $file->group_exclude }}</td>
            <td>{{ $file->sys_ver }}</td>
            <td>{{ $file->country }}</td>
            <td>
                <a title="Download File" href="/download/index/{{ $file->id }}" class="btn btn-primary pull-right btn-xs btn-inverse">
                    <span class="glyphicon glyphicon-download-alt"></span>
                </a>
                <a title="Edit record" href="/crud/file/editor/{{ $file->id }}" class="btn btn-primary pull-right btn-xs btn-inverse" style="margin-right: 10px;">
                    <span class="glyphicon glyphicon-edit"></span>
                </a>
                <a title="Replace File" href="/crud/file/upload_replace/{{ $file->id }}" class="btn btn-primary pull-right btn-xs btn-inverse" style="margin-right: 10px;">
                    <span class="glyphicon glyphicon-upload"></span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="12">
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