<!doctype html>
<html lang="{{ $langTag }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="author" content="Fresns" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}">
        <title>Admin Menu</title>
        <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap-icons.css') }}">
        <link rel="stylesheet" href="{{ @asset('/static/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ @asset('/static/css/select2-bootstrap-5-theme.min.css') }}">
        <style>
            .fs-7 {
                font-size: 0.9rem;
            }
        </style>
        @stack('css')
    </head>

    <body>
        @yield('content')

        <div class="fresns-tips"></div>

        <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ @asset('/static/js/jquery.min.js') }}"></script>
        <script src="{{ @asset('/static/js/select2.min.js') }}"></script>
        @stack('script')
    </body>
</html>