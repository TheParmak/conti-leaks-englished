@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Server settings</li>
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
                    <div class="panel-heading">General</div>
                    <div class="panel-body">
                        <table class="table table-condensed form-horizontal">
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            Server Port
                                        </label>
                                        <div class="col-sm-9">
                                            {{ Form::text('ServerPort', $config->ServerPort,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'ServerPort',
                                                'onkeyup' => 'this.value = this.value.replace(/[^0-9]/g, "")',
                                                'onkeydown' => 'this.value = this.value.replace(/[^0-9]/g, "")',
                                                'onkeypress' => 'this.value = this.value.replace(/[^0-9]/g, "")',
                                                'type' => 'number',
                                                'required' => 'required'
                                            ]) }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">
                                            Use SSL
                                        </label>
                                        <div class="col-sm-9">
                                            <input type="checkbox" style="width: auto;" id="statusSSL" class="form-control" {{ $config->UseSSL ? ($config->UseSSL == "True" ? "checked='checked'": "") : "checked='checked'"}} onclick="changeSSL(this)"/>
                                            {{ Form::text('UseSSL', $config->UseSSL ?: "True" ,  [
                                                'class' => 'form-control',
                                                'placeholder' => 'UseSSL',
                                                'style' => 'display: none;'
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

                <script>
                    function changeSSL(e) {
                        var checked = $(e).prop("checked");
                        $("input[name=UseSSL]").val(checked);

                        if ($("input[name=ServerPort]").val().length == 0 || ($("input[name=ServerPort]").val() == 2050 && checked == false))
                            $("input[name=ServerPort]").val(2048);

                        if ($("input[name=ServerPort]").val().length == 0 || ($("input[name=ServerPort]").val() == 2048 && checked == true))
                            $("input[name=ServerPort]").val(2050);

                        $("input[name=UseSSL]").val($("input[name=UseSSL]").val()[0].toUpperCase() + $("input[name=UseSSL]").val().substring(1));
                    }
                </script>
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
