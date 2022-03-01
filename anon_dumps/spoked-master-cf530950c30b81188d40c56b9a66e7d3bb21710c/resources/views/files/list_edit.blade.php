@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10;">
                    <li><a href="{{ route('files_list_index') }}">Files</a></li>
                    @if($list->exists)
                        <li class="active">Edit</li>
                    @else
                        <li class="active">New</li>
                    @endif
                </ol>
            </div>

            <div class="col-md-offset-3 col-lg-6 well">
                {{ Form::open() }}
                    <div class="input-group">
                        {{ Form::text('name', trim($list->name), [
                            'class' => 'form-control',
                            'placeholder' => 'List name..',
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
    </div>
@endsection
