<!doctype html>
<html lang="{{ App::setLocale($locale) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nearby content days limit</title>
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/fresns-panel.css') }}">
</head>

<body>
    <main>
        <div class="container-lg p-0 p-lg-3">
            <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                <!-- top -->
                <div class="row mb-2">
                    <div class="col-9">
                        <h3>Nearby content days limit <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
                        <p class="text-secondary">When viewing nearby content, only content within the specified number of days is displayed.</p>
                    </div>
                </div>

                <!-- Setting -->
                <div>
                    <form action="{{ route('nearby-days-limit.update') }}" method="post">
                        @csrf
                        @method('put')
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">{{ __('FsLang::panel.unit_day') }}</span>
                                <input type="number" class="form-control" name="days" value="{{ $days }}">
                                <a class="btn btn-outline-secondary" href="{{ route('panel.content-handler.index') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> {{ __('FsLang::panel.button_config') }}</a>
                            </div>
                        </div>

                        <!--button_save-->
                        <div class="text-center pt-3 pb-5">
                            <button type="submit" id="save" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
                        </div>
                    </form>
                </div>
                <!-- Setting end -->
            </div>
        </div>
    </main>

    <footer>
        <div class="copyright text-center">
            <p class="mt-5 mb-5 text-muted">&copy; <span class="copyright-year"></span> Fresns</p>
        </div>
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
</body>

</html>
