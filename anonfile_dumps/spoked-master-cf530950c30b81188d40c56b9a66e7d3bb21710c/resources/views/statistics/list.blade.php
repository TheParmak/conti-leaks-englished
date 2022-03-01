@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Statistics</li>
                </ol>

                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <td></td>
                            <td>AddData</td>
                            <td>Outlook<br>TotalAddress</td>
                            <td>Outlook<br>AdditionalAddress</td>
                            <td>Outlook<br>SentAddress</td>
                            <td>Thunderbird<br>Version</td>
                            <td>IsTbAddonsInstalled</td>
                            <td>Outlook<br>EmailBlockedByName</td>
                            <td>Outlook<br>EmailBlockedByDomain</td>
                            <td>Conn<br>AddrRecv</td>
                            <td>Conn<br>ErrorCode</td>
                            <td>SysVer</td>
                            <td>Outlook<br>Ver</td>
                            <td>Outlook<br>Platform</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $item)
                            <tr>
                                <td>
                                    <a href="/statistics/{{ $item->connection_id }}">
                                        <span class="glyphicon glyphicon-eye-open"></span>
                                    </a>
                                </td>
                                <td>{{ $item->add_date }}</td>
                                <td>{{ $item->outlook_total_address }}</td>
                                <td>{{ $item->outlook_additional_address }}</td>
                                <td>{{ $item->outlook_sent_address }}</td>
                                <td>{{ $item->thunderbird_version }}</td>
                                <td>
                                    @if($item->is_tb_addons_installed)
                                        <span class="glyphicon glyphicon-plus" style="color:green"></span>
                                    @else
                                        <span class="glyphicon glyphicon-minus" style="color:red"></span>
                                    @endif
                                </td>
                                <td>{{ $item->outlook_email_blocked_by_name }}</td>
                                <td>{{ $item->outlook_email_blocked_by_domain }}</td>
                                <td>{{ $item->conn_addr_recv }}</td>
                                <td>{{ $item->conn_error_code }}</td>
                                <td>{{ $item->sys_ver }}</td>
                                <td>{{ $item->outlook_ver }}</td>
                                <td>{{ $item->outlook_platform }}</td>
                            </tr>
                        @endforeach
                        @if(empty($records->toArray()))
                            <tr>
                                <td colspan="13" class="no-records">
                                    Records not found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
