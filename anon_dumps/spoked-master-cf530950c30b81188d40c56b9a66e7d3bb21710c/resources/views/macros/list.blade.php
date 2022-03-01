@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb" style="margin-bottom: 10px;">
                    <li class="active">Dictionaries</li>
                </ol>

                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>
                                <a class="btn btn-success pull-right btn-xs" href="{{ route('macros_edit') }}">
                                    <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td>{{ $record->name }}</td>
                                <td>
                                    {{ Form::open() }}
                                        <div class="btn-group btn-group-xs pull-right">
                                            {{ Form::hidden('del', $record->id) }}
                                            <button href="#" class="btn btn-danger" value="{{ $record->id }}">
                                                <span class="glyphicon glyphicon-trash"></span>
                                            </button>
                                            <a class="btn btn-success" href="{{ route('macros_edit', ['id' => $record->id]) }}">
                                                <span class="glyphicon glyphicon-edit"></span>
                                            </a>
                                        </div>
                                    {{ Form::close() }}
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($records->toArray()))
                            <tr>
                                <td colspan="4" class="no-records">
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
