@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Webmail Hosts</li>
                </ol>

                <table class="table table-condensed">
                    <thead>
                    <tr>
                        <td>Host</td>
                        <td>
                            <a class="btn btn-success pull-right btn-xs" href="{{ route('config_edit_web_hosts') }}">
                                <span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </td>
                    </tr>
                    </thead>
                    <tbody id="all_types">
                    @foreach($hosts as $host)
                        <tr>
                            <td>{{ $host->name }}</td>
                            <td>
                                {{ Form::open() }}
                                <div class="btn-group btn-group-xs pull-right">
                                    {{ Form::hidden('del', $host->id) }}
                                    <button class="btn btn-danger" value="{{ $host->id }}" onclick="return confirm('Are you sure?');">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                    <a class="btn btn-success" href="{{ route('config_edit_web_hosts', ['id' => $host->id]) }}">
                                        <span class="glyphicon glyphicon-edit"></span>
                                    </a>
                                </div>
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endforeach
                    @if(empty($hosts->toArray()))
                        <tr>
                            <td colspan="2" class="no-records">
                                Hosts not found
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
