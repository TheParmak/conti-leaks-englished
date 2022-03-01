@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Config global macros</li>
                </ol>

                @if (Session::has('success'))
                    <div class="alert alert-success">
                        {!! Session::get('success') !!}
                    </div>
                @endif

                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif

                {{ Form::open() }}
                <div class="panel panel-default">
                    <div class="panel-heading">Global macros</div>
                    <div class="panel-body">
                        <table class="table table-condensed form-horizontal">
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            {{ Form::textarea('global', $config,  [
                                                'class' => 'form-control',
                                            ]) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class='btn btn-success pull-right' type="submit">
                                        <span class="glyphicon glyphicon-ok"></span> Save
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                {{ Form::close() }}
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
    </style>
@endsection
