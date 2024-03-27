<!doctype html>
<html lang="{{ App::setLocale($locale) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('ConfigManager::fresns.name') }}</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/static/css/fresns-panel.css">
    @stack('style')
</head>

<body>
    <header class="bg-body">
        @include('ConfigManager::commons.header')
    </header>

    <main class="bg-body">
        @yield('content')
    </main>

    <footer>
        @include('ConfigManager::commons.footer')
    </footer>

    <!--fresns tips-->
    <div class="fresns-tips">
        @include('FsView::commons.tips')
    </div>

    <!--delete modal-->
    <div class="modal fade" id="deleteConfirm" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('FsLang::panel.button_delete') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('FsLang::panel.delete_desc') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('FsLang::panel.button_cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="deleteSubmit">{{ __('FsLang::panel.button_confirm_delete') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/static/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/jquery.min.js"></script>
    <script>
        // set timeout toast hide
        const setTimeoutToastHide = () => {
            $('.toast.show').each((k, v) => {
                setTimeout(function () {
                    $(v).hide();
                }, 2000);
            });
        };
        setTimeoutToastHide();
    </script>
    @stack('script')
</body>
</html>
