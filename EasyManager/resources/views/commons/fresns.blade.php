<!doctype html>
<html lang="{{ App::setLocale($locale) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('EasyManager::fresns.name') }}</title>
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
        @include('EasyManager::commons.header')
    </header>

    <main class="bg-body">
        @yield('content')
    </main>

    <footer>
        @include('EasyManager::commons.footer')
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

    <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ @asset('/static/js/jquery.min.js') }}"></script>
    <script>
        /* Tooltips */
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // set timeout toast hide
        const setTimeoutToastHide = () => {
            $('.toast.show').each((k, v) => {
                setTimeout(function () {
                    $(v).hide();
                }, 1500);
            });
        };
        setTimeoutToastHide();

        // tips
        window.tips = function (message, code = 200) {
            let html = `<div aria-live="polite" aria-atomic="true" class="position-fixed top-50 start-50 translate-middle" style="z-index:99">
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                            <img src="/static/images/icon.png" width="20px" height="20px" class="rounded me-2" alt="Fresns">
                            <strong class="me-auto">Fresns</strong>
                            <small>${code}</small>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    <div class="toast-body">${message}</div>
                </div>
            </div>`;
            $('div.fresns-tips').prepend(html);
            setTimeoutToastHide();
        };

        // SearchBox
        var select = document.getElementById('inputNameSelect');
        var button = document.getElementById('inputNameSelectBtn');
        var input = document.getElementById('inputName');

        select.addEventListener('click', function(event) {
            var target = event.target;
            if (target.tagName === 'A') {
                input.name = target.getAttribute('data-value');
                button.innerHTML = target.getAttribute('data-name');
            }
        });

        $('.delete-button').click(function () {
            $('#deleteConfirm').data('button', $(this));
            $('#deleteConfirm').modal('show');
            return false;
        });

        $('#deleteSubmit').click(function () {
            let button = $('#deleteConfirm').data('button');
            button.parent('form').submit();
            $('#deleteConfirm').modal('hide');
        });
    </script>
    @stack('script')
</body>
</html>
