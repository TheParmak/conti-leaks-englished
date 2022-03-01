@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10;">
                    <li><a href="{{ route('files_list_index') }}">Files</a></li>
                    <li><a href="{{ route('files_list', ['id' => $list->id]) }}">{{ $list->name }}</a></li>
                    <li class="active">Upload file</li>
                </ol>
            </div>
            <div class="col-md-offset-3 col-lg-6">
                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif
                <div class="well">
                    {{ Form::open(['enctype' => 'multipart/form-data']) }}
                        <div class="form-group">
                            {{ Form::button('<span class="glyphicon glyphicon-ok"></span>', [
                                'class' => 'btn btn-default btn-success pull-right',
                                'type' => 'submit',
                            ]) }}
                            {{ Form::file('file[]', [
                                'style' => 'color: gray;',
                                'multiple' => 'multiple',
                            ]) }}
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

    </div>
@endsection
