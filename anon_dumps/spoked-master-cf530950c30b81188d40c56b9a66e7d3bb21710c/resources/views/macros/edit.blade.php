@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li><a href="{{ route('macros') }}">Dictionaries</a></li>
                    @if($record->exists)
                        <li class="active">Update</li>
                    @else
                        <li class="active">New</li>
                    @endif
                </ol>

                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif
            </div>

            @if($record->exists)
                <div class="col-md-12">
                    {{ Form::open() }}
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <table class="table table-condensed form-horizontal">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                Name
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::text('name', $record->name,  [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Name',
                                                ]) }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">
                                                Value
                                            </label>
                                            <div class="col-sm-10">
                                                {{ Form::textarea('value', $record->value,  [
                                                    'class' => 'form-control',
                                                    'placeholder' => 'Value',
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
            @else
                <div class="col-md-offset-3 col-lg-6">
                    <div class="well">
                        {{ Form::open(['enctype' => 'multipart/form-data']) }}
                        <div class="form-group">
                            {{ Form::file('file', ['style' => 'color: gray;']) }}
                        </div>
                        <div class="input-group">
                            {{ Form::text('name', null, [
                                'class' => 'form-control',
                                'placeholder' => 'File name..',
                            ]) }}
                            <span class="input-group-btn">
                                {{ Form::button('<span class="glyphicon glyphicon-ok"></span>', [
                                    'class' => 'btn btn-default btn-success',
                                    'type' => 'submit',
                                ]) }}
                            </span>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            @endif
        </div>

    </div>

    <style>
        .breadcrumb{
            margin-bottom: 10px;
        }

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
