<!doctype html>
<html lang="zh-Hans">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="author" content="Fresns" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}">
        <title>WeChat Login</title>
        <link rel="stylesheet" href="/static/css/bootstrap.min.css">
        <link rel="stylesheet" href="/static/css/bootstrap-icons.min.css">
        @stack('css')
    </head>

    <body>
        @yield('content')

        <script src="/static/js/bootstrap.bundle.min.js"></script>
        <script src="/static/js/jquery.min.js"></script>
        <script src="/static/js/iframeResizer.contentWindow.min.js"></script>
        <script src="//res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
        @stack('script')
    </body>
</html>
