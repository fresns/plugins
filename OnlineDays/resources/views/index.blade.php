<!doctype html>
<html lang="{{ App::setLocale($locale) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Number of days online</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/static/css/fresns-panel.css">
</head>

<body>
    <main>
        <div class="container-lg p-0 p-lg-3">
            <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                <!-- top -->
                <div class="row mb-2">
                    <div class="col-9">
                        <h3>Number of days online <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
                        <p class="text-secondary">Counting the number of days users are online.</p>
                    </div>
                    <div class="col-3">
                        <div class="input-group mt-2 mb-4 justify-content-lg-end px-1" role="group">
                            <a class="btn btn-outline-secondary" href="https://github.com/fresns/plugins/tree/3.x/OnlineDays" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
                        </div>
                    </div>
                </div>

                <!-- Setting -->
                <div>
                    <form action="{{ route('online-days.update') }}" method="post">
                        @csrf
                        @method('put')
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text">{{ __('OnlineDays::fresns.assignee') }}</span>
                                <select class="form-select" id="extcreditsId" name="extcreditsId">
                                    <option value="" {{ !$extcreditsId ? 'selected' : '' }}>⛔️ {{ __('FsLang::panel.option_close') }}</option>
                                    <option value="1" {{ $extcreditsId == 1 ? 'selected' : '' }}>{{ $extcredits1Name }}{{ $extcredits1Unit ? " ({$extcredits1Unit})" : '' }}</option>
                                    <option value="2" {{ $extcreditsId == 2 ? 'selected' : '' }}>{{ $extcredits2Name }}{{ $extcredits2Unit ? " ({$extcredits2Unit})" : '' }}</option>
                                    <option value="3" {{ $extcreditsId == 3 ? 'selected' : '' }}>{{ $extcredits3Name }}{{ $extcredits3Unit ? " ({$extcredits3Unit})" : '' }}</option>
                                    <option value="4" {{ $extcreditsId == 4 ? 'selected' : '' }}>{{ $extcredits4Name }}{{ $extcredits4Unit ? " ({$extcredits4Unit})" : '' }}</option>
                                    <option value="5" {{ $extcreditsId == 5 ? 'selected' : '' }}>{{ $extcredits5Name }}{{ $extcredits5Unit ? " ({$extcredits5Unit})" : '' }}</option>
                                </select>
                                <a class="btn btn-outline-secondary" href="{{ route('panel.user.index') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> {{ __('FsLang::panel.button_config') }}</a>
                            </div>
                            <div class="form-text">{{ __('OnlineDays::fresns.assignee_desc') }}</div>
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

    <script src="/static/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/jquery.min.js"></script>
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
