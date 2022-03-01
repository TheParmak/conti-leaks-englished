@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Settings</div>

                    <div class="panel-body">
                        @if($md5_error)
                            <div class="alert alert-danger">
                                Settings file edited! If you submit form, it will remove all changes. Pls contact with administrator.
                            </div>
                        @endif
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        {{ Form::open() }}
                        <table class="table table-condensed">
                            <tr>
                                <td>
                                    <div class="form-group" style="margin-bottom: 0">
                                        <label class="col-sm-2 control-label">TaskSize</label>
                                        <div class="col-sm-10">
                                            {{ Form::text('task_size', $data['task_size'],  [
                                                'class' => 'form-control',
                                                'placeholder' => '100',
                                            ]) }}
                                        </div>
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
