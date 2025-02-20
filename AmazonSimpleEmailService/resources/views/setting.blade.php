<!doctype html>
<html lang="{{ App::setLocale($locale) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amazon SES</title>
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
                    <div class="col-7">
                        <h3>Amazon SES <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
                        <p class="text-secondary">Email Sending Service Based on Amazon Simple Email Service (SES)</p>
                    </div>
                    <div class="col-5">
                        <div class="input-group mt-2 mb-4 justify-content-lg-end px-1" role="group">
                            <a class="btn btn-outline-secondary" href="https://github.com/fresns/plugins/tree/3.x/AmazonSimpleEmailService" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
                        </div>
                    </div>
                </div>
                <!-- Menu -->
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-settings-tab" data-bs-toggle="tab" data-bs-target="#nav-settings" type="button" role="tab" aria-controls="nav-settings" aria-selected="true">@lang('AmazonSimpleEmailService::fresns.menuConfig')</button>
                        <button class="nav-link" id="nav-test-tab" data-bs-toggle="tab" data-bs-target="#nav-test" type="button" role="tab" aria-controls="nav-test" aria-selected="false">@lang('AmazonSimpleEmailService::fresns.menuTest')</button>
                        <button class="nav-link" id="nav-variable-tab" data-bs-toggle="tab" data-bs-target="#nav-variable" type="button" role="tab" aria-controls="nav-variable" aria-selected="false">@lang('AmazonSimpleEmailService::fresns.menuVariable')</button>
                    </div>
                </nav>
                <!-- tabContent -->
                <div class="tab-content" id="nav-tabContent">
                    <!-- Setting -->
                    <div class="tab-pane fade show active" id="nav-settings" role="tabpanel" aria-labelledby="nav-settings-tab">
                        <form class="mt-4" action="{{ route('amazon-ses.settings.store') }}" method="post">
                            @csrf
                            {{-- Send Config --}}
                            <div class="row mb-4">
                                <label class="col-lg-3 col-form-label text-lg-end">{{ __('FsLang::panel.sidebar_send') }}:</label>
                                <div class="col-lg-4 pt-1">
                                    <a class="btn btn-outline-secondary btn-sm px-4 me-2" href="{{ route('panel.send.index') }}" target="_blank" role="button">{{ __('FsLang::panel.button_config') }}</a>
                                    <a href="{{ $marketUrl.'/detail/AmazonSimpleEmailService' }}" target="_blank" class="link-primary fs-7">{{ __('FsLang::panel.button_support') }}</a>
                                </div>
                            </div>
                            {{-- Email Config --}}
                            <div class="row mb-4">
                                <label class="col-lg-3 col-form-label text-lg-end">AWS ACCESS KEY ID:</label>
                                <div class="col-lg-4"><input type="text" class="form-control" name="amazon_ses_access_key_id" value="{{ old("amazon_ses_access_key_id", $content['amazon_ses_access_key_id'] ?? '') }}" ></div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-3 col-form-label text-lg-end">AWS SECRET ACCESS KEY:</label>
                                <div class="col-lg-4"><input type="text" class="form-control" name="amazon_ses_secret_access_key" value="{{ old("amazon_ses_secret_access_key", $content['amazon_ses_secret_access_key'] ?? '') }}" ></div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-3 col-form-label text-lg-end">AWS REGION:</label>
                                <div class="col-lg-4"><input type="text" class="form-control" name="amazon_ses_region" value="{{ old("amazon_ses_region", $content['amazon_ses_region'] ?? '') }}" ></div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-3 col-form-label text-lg-end">@lang('AmazonSimpleEmailService::fresns.smtpFromMail'):</label>
                                <div class="col-lg-4"><input type="email" class="form-control" name="amazon_ses_from_mail" placeholder="name@example.com" value="{{ old("amazon_ses_from_mail", $content['amazon_ses_from_mail'] ?? '') }}" ></div>
                                <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('AmazonSimpleEmailService::fresns.smtpFromMailIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-3 col-form-label text-lg-end">@lang('AmazonSimpleEmailService::fresns.smtpFromName'):</label>
                                <div class="col-lg-4"><input type="text" class="form-control" name="amazon_ses_from_name" placeholder="Fresns" value="{{ old("amazon_ses_from_name", $content['amazon_ses_from_name'] ?? '') }}" ></div>
                                <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('AmazonSimpleEmailService::fresns.smtpFromNameIntro')</div>
                            </div>
                            {{-- Save --}}
                            <div class="row mb-4">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-9">
                                    <button type="submit" class="btn btn-primary">@lang('AmazonSimpleEmailService::fresns.settingButton')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Test -->
                    <div class="tab-pane fade" id="nav-test" role="tabpanel" aria-labelledby="nav-test-tab">
                        <div class="alert alert-warning mt-4" role="alert">@lang('AmazonSimpleEmailService::fresns.testMailDesc')</div>
                        <div class="input-group mt-3">
                            <span class="input-group-text" id="inputGroup-sizing-default">Email</span>
                            <input type="email" class="form-control" id="testEmail" placeholder="name@example.com">
                            <button type="button" class="btn btn-primary" id="testSend" data-url="{{ route('amazon-ses.settings.test') }}" onclick="send_mail_test()">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                                @lang('AmazonSimpleEmailService::fresns.testMailSend')
                            </button>
                        </div>
                    </div>
                    <!-- Variable -->
                    <div class="tab-pane fade" id="nav-variable" role="tabpanel" aria-labelledby="nav-variable-tab">
                        <div class="alert alert-info mt-4" role="alert">
                            @lang('FsLang::panel.menu_systems') > @lang('FsLang::panel.sidebar_send') > @lang('FsLang::panel.sidebar_send_tab_templates')
                        </div>
                        <div class="input-group mt-3">
                            <span class="input-group-text">@lang('FsLang::panel.site_logo')</span>
                            <span class="form-control">{logo}</span>
                        </div>
                        <div class="input-group mt-3">
                            <span class="input-group-text">@lang('FsLang::panel.site_logo')</span>
                            <span class="form-control">{icon}</span>
                        </div>
                        <div class="input-group mt-3">
                            <span class="input-group-text">@lang('FsLang::panel.site_name')</span>
                            <span class="form-control">{name}</span>
                        </div>
                        <div class="input-group mt-3">
                            <span class="input-group-text">@lang('AmazonSimpleEmailService::fresns.variableCode')</span>
                            <span class="form-control">{code}</span>
                        </div>
                        <div class="input-group mt-3">
                            <span class="input-group-text">@lang('AmazonSimpleEmailService::fresns.variableTime')</span>
                            <span class="form-control">{time}</span>
                        </div>
                    </div>
                </div>
                <!-- end -->
            </div>
        </div>
    </main>

    <footer>
        <div class="copyright text-center">
            <p class="mt-5 mb-5 text-muted">&copy; 2021-Present Fresns</p>
        </div>
    </footer>

    <div class="fresns-tips">
        @include('FsView::commons.tips')
    </div>

    <script src="/static/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/jquery.min.js"></script>

    <script>
        // set timeout toast hide
        const setTimeoutToastHide = () => {
            $('.toast.show').each((k, v) => {
                setTimeout(function () {
                    $(v).hide();
                }, 1500);
            });
        };
        setTimeoutToastHide();

        // send_mail_test
        function send_mail_test(){
            var email = $('#testEmail').val();
            var url  = $('#testSend').data('url');

            if(email == ''){
                alert("please input email value.");
                return false;
            }

            $('#testSend').prop('disabled', true);
            $('#testSend').find('.spinner-border').show();

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    email: email,
                    _token: '{{ csrf_token() }}',
                },
                cache: false,
                dataType: 'json',
                success: function(json) {
                    if (json.code == 0){
                        alert('Send Successfully');
                        return;
                    }

                    alert('Send failed, please check the configuration');
                },
                error: function() {
                    alert('Send failed, please check the configuration');
                },
                complete: function () {
                    $('#testSend').prop('disabled', false);
                    $('#testSend').find(".spinner-border").hide();
                },
            });
        }
    </script>

</body>
</html>
