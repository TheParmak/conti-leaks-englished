@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <table class="table table-condensed">
                    <thead>
                    <tr>
                        <td>Type</td>
                    </tr>
                    </thead>
                    <tbody id="all_types">
                    @foreach($types as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
