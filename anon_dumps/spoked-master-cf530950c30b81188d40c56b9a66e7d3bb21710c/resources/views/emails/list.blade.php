@extends('layouts.app')

@section('content')
    <div class="container" ng-controller="listDispatch" ng-init="init()">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Mailouts</li>
                </ol>

                <ul class="nav nav-tabs tabcontrol">
                    <li ng-click="switchTab($event, 'active')" style="cursor: pointer;"><a>Current Active Mailout</a></li>
                    <li ng-click="switchTab($event, 'sent')" style="cursor: pointer;"><a>Sent</a></li>
                    <li ng-click="switchTab($event, 'draft')" style="cursor: pointer;"><a>Drafts</a></li>
                </ul>

                <table class="table table-condensed" id="tab_active">
                    <thead>
                    <tr>
                        <td>Subject</td>
                    </tr>
                    </thead>
                    <tbody id="all_mails">
                    @foreach($active as $email)
                        <tr>
                            <td><a href="{{ route('emails_edit', ['id' => $email->id]) }}">{{ $email->message_subject }}</a></td>
                            <td>
                                {{ Form::open() }}
                                <div class="btn-group btn-group-xs pull-right">
                                    {{ Form::hidden('id', $email->id) }}
                                    <a href="/statistics/{{ $email->id }}" class="btn btn-info">
                                        <span class="glyphicon glyphicon-stats"></span>
                                    </a>
                                </div>
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach
                    @if(collect($active)->count() == 0)
                        <tr>
                            <td colspan="2" class="no-records">
                                Records not found
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>


                <table class="table table-condensed" id="tab_sent">
                    <thead>
                    <tr>
                        <td>Subject</td>
                        <td>Started</td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody id="all_mails">
                    @foreach($sent as $email)
                        <tr>
                            <td><a href="{{ route('emails_edit', ['id' => $email->id]) }}">{{ $email->message_subject }}</a></td>
                            <td>{{ $email->updated_at ? $email->updated_at : '' }}</td>
                            <td>
                                {{ Form::open() }}
                                <div class="btn-group btn-group-xs pull-right">
                                    {{ Form::hidden('id', $email->id) }}
                                    <a href="/statistics/{{ $email->id }}" class="btn btn-info">
                                        <span class="glyphicon glyphicon-stats"></span>
                                    </a>

                                    <button name="action" class="btn btn-success" value="clone" onclick="return confirm('Do you want to clone this dispatch?');" title="{{ \App\Email::attributes()["mailout_clone"] }}">
                                        <span class="glyphicon glyphicon-copy"></span>
                                    </button>
                                </div>
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach
                    @if(collect($sent)->count() == 0)
                        <tr>
                            <td colspan="2" class="no-records">
                                Records not found
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>


                <table class="table table-condensed" id="tab_draft">
                    <thead>
                    <tr>
                        <td>Subject</td>
                        <td>
                            <a class="btn btn-success pull-right btn-xs" href="{{ route('emails_edit') }}">
                                <span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </td>
                    </tr>
                    </thead>
                    <tbody id="all_mails">
                    @foreach($drafts as $email)
                        <tr>
                            <td><a href="{{ route('emails_edit', ['id' => $email->id]) }}">{{ $email->message_subject }}</a></td>
                            <td>
                                {{ Form::open() }}
                                <button name="action" class="btn btn-info btn-xs pull-right" value="active" style="margin-left: 10px;" onclick="return confirm('Are you sure?')" title="{{ \App\Email::attributes()["mailout_is_inactive"] }}">
                                    <span class="glyphicon glyphicon-play"></span>
                                </button>
                                <div class="btn-group btn-group-xs pull-right">
                                    {{ Form::hidden('id', $email->id) }}
                                    <button name="action" class="btn btn-danger" value="delete" onclick="return confirm('Are you sure?');">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </div>
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach
                    @if(collect($drafts)->count() == 0)
                        <tr>
                            <td colspan="2" class="no-records">
                                Records not found
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            app.controller("listDispatch", function ($scope) {
                $scope.init = function() {
                    try {
                        var tab = location.href.toString().match(/\?tab=.+?$/g)[0];
                    } catch (e) {
                        tab = "active";
                    }
                    $('.table.table-condensed').hide();
                    $('.tabcontrol > li').removeClass('active');

                    switch (tab){
                        case "?tab=sent":
                            $(".tabcontrol > li:eq(1)").addClass("active");
                            $('#tab_sent').show();
                            break;

                        case "?tab=draft":
                            $(".tabcontrol > li:eq(2)").addClass("active");
                            $('#tab_draft').show();
                            break;

                        default:
                            $(".tabcontrol > li:eq(0)").addClass("active");
                            $('#tab_active').show();
                    }
                }

                $scope.switchTab = function (e, tab) {
                    $('.tabcontrol > li').removeClass('active');
                    $('.table.table-condensed').hide();

                    $(e.currentTarget).addClass('active');
                    $('#tab_' + tab).show();
                }
            })
        </script>
    </div>
@endsection
