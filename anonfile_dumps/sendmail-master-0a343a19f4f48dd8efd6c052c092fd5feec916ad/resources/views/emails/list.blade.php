@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if (Session::has('message'))
                    <div class="alert alert-success">
                        {{ Session::get('message') }}
                    </div>
                @endif
                <table class="table table-condensed">
                    <thead>
                    <tr>
                        <td colspan="2">
                            <a class="btn btn-sm btn-success pull-right" href="/emails/edit/"><span class="glyphicon glyphicon-plus"></span> New</a>
                        </td>
                    </tr>

                    </thead>
                    @foreach($emails as $email)
                        <tr>
                            <td>
                                <a target="_blank" href="/emails/edit/{{ $email['id'] }}">{{ $email['title'] }}</a>
                            </td>
                            <td>
                                <a class="btn btn-xs btn-danger pull-right" href="/emails/delete/{{ $email['id'] }}">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
            {!! $emails->render() !!}
        </div>
    </div>
@endsection
