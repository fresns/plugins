<!doctype html>
<html lang="{{ App::setLocale($locale) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="author" content="Fresns" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('SharePoster::fresns.name') }}</title>
        <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap-icons.min.css') }}">
        <link rel="stylesheet" href="{{ @asset('/static/css/fresns-panel.css') }}">
        @stack('css')
    </head>

    <body>
        <main>
            <div class="container-lg p-0 p-lg-3">
                <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                    <!-- top -->
                    <div class="row mb-2">
                        <div class="col-8">
                            <h3>{{ __('SharePoster::fresns.name') }} <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
                            <p class="text-secondary">{{ __('SharePoster::fresns.description') }}</p>
                        </div>
                        <div class="col-4">
                            <div class="input-group mt-2 mb-4 justify-content-lg-end px-1" role="group">
                                <a class="btn btn-outline-secondary" href="https://github.com/fresns/extensions/tree/release/SharePoster" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
                            </div>
                        </div>
                    </div>

                    <!-- Menu -->
                    <div class="mb-3">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if (Route::is('share-poster.admin.index')) active @endif" href="{{ route('share-poster.admin.index') }}" role="button">{{ __('FsLang::panel.user') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if (Route::is('share-poster.admin.group')) active @endif" href="{{ route('share-poster.admin.group') }}" role="button">{{ __('FsLang::panel.group') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if (Route::is('share-poster.admin.hashtag')) active @endif" href="{{ route('share-poster.admin.hashtag') }}" role="button">{{ __('FsLang::panel.hashtag') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if (Route::is('share-poster.admin.post')) active @endif" href="{{ route('share-poster.admin.post') }}" role="button">{{ __('FsLang::panel.post') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if (Route::is('share-poster.admin.comment')) active @endif" href="{{ route('share-poster.admin.comment') }}" role="button">{{ __('FsLang::panel.comment') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link @if (Route::is('share-poster.admin.font')) active @endif" href="{{ route('share-poster.admin.font') }}" role="button">{{ __('SharePoster::fresns.font') }}</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Setting -->
                    <div class="tab-content" id="myTabContent">
                        @yield('content')
                    </div>
                    <!-- end -->
                </div>
            </div>
        </main>

        <footer>
            <div class="copyright text-center">
                <p class="mt-5 mb-5 text-muted">&copy; <span class="copyright-year"></span> Fresns</p>
            </div>
        </footer>

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
            yearElement.textContent = currentYear;

            // set timeout toast hide
            const setTimeoutToastHide = () => {
                $('.toast.show').each((k, v) => {
                    setTimeout(function () {
                        $(v).hide();
                    }, 1500);
                });
            };
            setTimeoutToastHide();
        </script>
        @stack('script')
    </body>
</html>
