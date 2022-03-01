@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li><a href="{{ route('files_list_index') }}">Files</a></li>
                    <li class="active">{{ $list->name }}</li>
                </ol>

                <table class="table table-condensed">
                    <thead class="table-head">
                        <tr>
                            <td>Name</td>
                            <td>
                                <a class="btn btn-success pull-right btn-xs" href="{{ route('files_add', ['id' => $list->id]) }}">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list->files as $file)
                            <tr>
                                <td>
                                    {{ $file->name }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs pull-right">
                                        {{ Form::open() }}
                                        <div class="btn-group btn-group-xs pull-right">
                                            {{ Form::hidden('del', $file->id) }}
                                            <button href="#" class="btn btn-danger" value="{{ $file->id }}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </button>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($list->files->toArray()))
                            <tr>
                                <td colspan="2" class="no-records">
                                    Records not found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
