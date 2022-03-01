@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Config database</li>
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

                <div class="alert alert-info">
                    <strong>PLEASE!</strong> RESTART SERVER AFTER CHANGING THESE OPTIONS!
                </div>

                {{ Form::open() }}
                <div class="panel panel-default">
                    <div class="panel-heading">Database</div>
                    <div class="panel-body">
                        <table class="table table-condensed form-horizontal">
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            UserId
                                        </label>
                                        <div class="col-sm-9">
                                            {{ Form::text('User_Id', $config->User_Id,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'UserId',
                                            ]) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            Password
                                        </label>
                                        <div class="col-sm-9">
                                            {{ Form::text('Password', $config->Password,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'Password',
                                            ]) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            Database
                                        </label>
                                        <div class="col-sm-9">
                                            {{ Form::text('Database', $config->Database,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'Database',
                                            ]) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            DataSource
                                        </label>
                                        <div class="col-sm-9">
                                            {{ Form::text('Data_Source', $config->Data_Source,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'DataSource',
                                            ]) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Fields</div>
                    <div class="panel-body">
                        <table class="table table-condensed form-horizontal">
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            TableName
                                        </label>
                                        <div class="col-sm-9">
                                            {{ Form::text('TableName', $config->TableName,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'TableName',
                                            ]) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            ResultTableName
                                        </label>
                                        <div class="col-sm-9">
                                            {{ Form::text('ResultTableName', $config->ResultTableName,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'ResultTableName',
                                            ]) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            ThunderbirdResultTableName
                                        </label>
                                        <div class="col-sm-9">
                                            {{ Form::text('ThunderbirdResultTableName', $config->ThunderbirdResultTableName,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'ThunderbirdResultTableName',
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
