<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Spoked</title>
        <link rel="shortcut icon" type="image/png" href="{{ asset('/images/favicon.png') }}"/>
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    </head>
    <body>
        @yield('content')
    </body>
</html>
