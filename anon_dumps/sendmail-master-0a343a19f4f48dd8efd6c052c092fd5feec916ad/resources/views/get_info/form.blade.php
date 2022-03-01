@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Add rsa for get_info</div>

                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        {{ Form::open() }}
                        <table class="table table-condensed">
                            <tr>
                                <td>
                                    {{ Form::text('base64', array_get($_GET, 'client'),  [
                                        'class' => 'form-control',
                                        'placeholder' => 'Base64',
                                    ]) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ Form::text('domain', array_get($data, '0'),  [
                                        'class' => 'form-control',
                                        'placeholder' => 'Domain',
                                    ]) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ Form::text('type', array_get($data, '1', 'mail'),  [
                                        'class' => 'form-control',
                                        'placeholder' => 'Type',
                                    ]) }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {{ Form::textarea('rsa', array_get($data, '2'), [
                                        'class' =>"form-control",
                                        'placeholder' => 'Rsa',
                                    ]) }}
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
