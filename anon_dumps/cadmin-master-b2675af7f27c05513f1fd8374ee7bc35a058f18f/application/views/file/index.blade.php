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
    @foreach($files as $f)
        <tr>
            <td>{{ $f->client_id }}</td>
            <td>{{ $f->filename }}</td>
            <td>{{ $f->priority }}</td>
            <td>{{ $f->userdefined_low }}</td>
            <td>{{ $f->userdefined_high }}</td>
            <td>{{ $f->group }}</td>
            <td>{{ $f->country }}</td>
            <td>{{ $f->sys_ver }}</td>
            <td>
                <a href="/download/index/{{ $f->id }}" class="btn btn-primary pull-right btn-xs">
                    <span class="glyphicon glyphicon-download-alt"></span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@include('crud.file.template.script')