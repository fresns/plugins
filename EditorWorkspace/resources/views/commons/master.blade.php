<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="author" content="Fresns" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}">
        <title>Editor Workspace</title>
        <link rel="stylesheet" href="/static/css/bootstrap.min.css">
        <link rel="stylesheet" href="/static/css/bootstrap-icons.min.css">
        @stack('css')
    </head>

    <body>
        <main>
            @yield('content')
        </main>

        <div class="fresns-tips"></div>

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
        </script>
        @stack('script')
    </body>
</html>
