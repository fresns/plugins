<!doctype html>
<html lang="{{ $langTag }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="author" content="Fresns" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}">
        <title>Cloudinary</title>
        <link rel="stylesheet" href="/static/css/bootstrap.min.css">
        @stack('style')
    </head>

    <body>
        @yield('content')

        <script src="/static/js/bootstrap.bundle.min.js"></script>
        <script src="/static/js/jquery.min.js"></script>
        <script src="/assets/Cloudinary/js/lodash.min.js"></script>
        @stack('script')
    </body>
</html>
