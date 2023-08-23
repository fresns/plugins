<!doctype html>
<html lang="{{ App::setLocale($locale) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('EditorWorkspace::fresns.name') }}</title>
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/fresns-panel.css') }}">
    <style>
        .navbar-nav .nav-link.active, .navbar-nav .show>.nav-link {
            color: var(--bs-blue);
        }
    </style>
    @stack('style')
</head>

<body>
    <header class="bg-body">
        @include('EditorWorkspace::commons.header')
    </header>

    <main class="bg-body">
        @yield('content')
    </main>

    <footer>
        @include('EditorWorkspace::commons.footer')
    </footer>

    <!--fresns tips-->
    <div class="fresns-tips">
        @include('FsView::commons.tips')
    </div>

    <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ @asset('/static/js/jquery.min.js') }}"></script>
    <script>
        // copyright-year
        var yearElement = document.querySelector('.copyright-year');
        var currentDate = new Date();
        var currentYear = currentDate.getFullYear();
        if (yearElement) {
            yearElement.textContent = currentYear;
        }
    </script>
    @stack('script')
</body>
</html>
