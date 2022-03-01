@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Upload email list</div>

                    <div class="panel-body" >
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                {{ Session::get('message') }}
                            </div>
                        @endif
                            @if (Session::has('message_error'))
                                <div class="alert alert-danger">
                                    {{ Session::get('message_error') }}
                                </div>
                            @endif
                        {{ Form::open() }}
                        <table class="table table-condensed">
                            <tr>
                                <td>
                                    {{ Form::text('name', Session::get('name'),  [
                                        'class' => 'form-control',
                                        'placeholder' => 'Name',
                                    ]) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        {{ Form::text('email_path', Session::get('email_path'),  [
                                            'class' => 'form-control',
                                            'placeholder' => 'URL or ftp://login:pass@127.0.0.1/email_list.txt',
                                        ]) }}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                               <td>
                                   <button class='btn btn-success pull-right' type="submit">
                                       <span class="glyphicon glyphicon-ok"></span>
                                   </button>
                               </td>
                            </tr>
                        </table>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
