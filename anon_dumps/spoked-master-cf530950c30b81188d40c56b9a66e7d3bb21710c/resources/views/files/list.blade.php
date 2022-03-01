@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Files</li>
                </ol>

                <table class="table table-condensed">
                    <thead class="table-head">
                        <tr>
                            <td style="width: 1px;">Files</td>
                            <td>Name</td>
                            <td>
                                <a class="btn btn-success pull-right btn-xs" href="{{ route('files_list_edit') }}">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>
                                    <a href="{{ route('files_list', ['id' => $file->id]) }}" class="btn btn-xs btn-primary">
                                        <span class="badge">{{ $file->files->count() }}</span>
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </td>
                                <td>
                                    {{ $file->name }}
                                </td>
                                <td>
                                    {{ Form::open() }}
                                        <div class="btn-group btn-group-xs pull-right">
                                            <a href="{{ route('files_list_edit', ['id' => $file->id]) }}" class="btn btn-success" title="Edit">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                            {{ Form::hidden('del', $file->id) }}
                                            <button href="#" class="btn btn-danger" value="{{ $file->id }}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </button>
                                        </div>
                                    {{ Form::close() }}
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($files->toArray()))
                            <tr>
                                <td colspan="3" class="no-records">
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
