<!doctype html>
<html lang="{{ App::setLocale($locale) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns Email</title>
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
                    <div class="col-7">
                        <h3>@lang('SmtpEmail::fresns.name') <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
                        <p class="text-secondary">@lang('SmtpEmail::fresns.description')</p>
                    </div>
                    <div class="col-5">
                        <div class="input-group mt-2 mb-4 justify-content-lg-end px-1" role="group">
                            <a class="btn btn-outline-secondary" href="https://github.com/fresns/extensions/tree/release/SmtpEmail" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
                        </div>
                    </div>
                </div>
                <!-- Menu -->
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-settings-tab" data-bs-toggle="tab" data-bs-target="#nav-settings" type="button" role="tab" aria-controls="nav-settings" aria-selected="true">@lang('SmtpEmail::fresns.menuConfig')</button>
                        <button class="nav-link" id="nav-test-tab" data-bs-toggle="tab" data-bs-target="#nav-test" type="button" role="tab" aria-controls="nav-test" aria-selected="false">@lang('SmtpEmail::fresns.menuTest')</button>
                        <button class="nav-link" id="nav-variable-tab" data-bs-toggle="tab" data-bs-target="#nav-variable" type="button" role="tab" aria-controls="nav-variable" aria-selected="false">@lang('SmtpEmail::fresns.menuVariable')</button>
                    </div>
                </nav>
                <!-- tabContent -->
                <div class="tab-content" id="nav-tabContent">
                    <!-- Setting -->
                    <div class="tab-pane fade show active" id="nav-settings" role="tabpanel" aria-labelledby="nav-settings-tab">
                        <form class="mt-4" action="{{ route('fresnsemail.settings.store') }}" method="post">
                            @csrf
                            {{-- Send Config --}}
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">{{ __('FsLang::panel.sidebar_send') }}:</label>
                                <div class="col-lg-5 pt-1">
                                    <a class="btn btn-outline-secondary btn-sm px-4 me-2" href="{{ route('panel.send.index') }}" target="_blank" role="button">{{ __('FsLang::panel.button_config') }}</a>
                                    <a href="{{ $marketUrl.'/detail/SmtpEmail' }}" target="_blank" class="link-primary fs-7">{{ __('FsLang::panel.button_support') }}</a>
                                </div>
                            </div>
                            {{-- Email Config --}}
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('SmtpEmail::fresns.smtpHost'):</label>
                                <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_host" placeholder="smtp.example.com" value="{{ old("fresnsemail_smtp_host", $content['fresnsemail_smtp_host'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('SmtpEmail::fresns.smtpHostIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('SmtpEmail::fresns.smtpPort'):</label>
                                <div class="col-lg-5"><input type="number" class="form-control" name="fresnsemail_smtp_port" placeholder="25" value="{{ old("fresnsemail_smtp_port", $content['fresnsemail_smtp_port'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('SmtpEmail::fresns.smtpPortIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('SmtpEmail::fresns.smtpUser'):</label>
                                <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_username" placeholder="name@example.com" value="{{ old("fresnsemail_smtp_username", $content['fresnsemail_smtp_username'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('SmtpEmail::fresns.smtpUserIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('SmtpEmail::fresns.smtpPassword'):</label>
                                <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_password" placeholder="Password" value="{{ old("fresnsemail_smtp_password", $content['fresnsemail_smtp_password'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('SmtpEmail::fresns.smtpPasswordIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('SmtpEmail::fresns.smtpVerifyType'):</label>
                                <div class="col-lg-5">
                                    <select class="form-select" name="fresnsemail_verify_type">
                                        <option value="" @if($content['fresnsemail_verify_type'] == '') selected @endif>Null</option>
                                        <option value="tls" @if($content['fresnsemail_verify_type'] == 'tls') selected @endif>tls</option>
                                        <option value="ssl" @if($content['fresnsemail_verify_type'] == 'ssl') selected @endif>ssl</option>
                                    </select>
                                </div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('SmtpEmail::fresns.smtpVerifyTypeIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('SmtpEmail::fresns.smtpFromMail'):</label>
                                <div class="col-lg-5"><input type="email" class="form-control" name="fresnsemail_from_mail" placeholder="name@example.com" value="{{ old("fresnsemail_from_mail", $content['fresnsemail_from_mail'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('SmtpEmail::fresns.smtpFromMailIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('SmtpEmail::fresns.smtpFromName'):</label>
                                <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_from_name" placeholder="Fresns" value="{{ old("fresnsemail_from_name", $content['fresnsemail_from_name'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('SmtpEmail::fresns.smtpFromNameIntro')</div>
                            </div>
                            {{-- Save --}}
                            <div class="row mb-4">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-10">
                                    <button type="submit" class="btn btn-primary">@lang('SmtpEmail::fresns.settingButton')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Test -->
                    <div class="tab-pane fade" id="nav-test" role="tabpanel" aria-labelledby="nav-test-tab">
                        <div class="alert alert-warning mt-4" role="alert">@lang('SmtpEmail::fresns.testMailDesc')</div>
                        <div class="input-group mt-3">
                            <span class="input-group-text" id="inputGroup-sizing-default">Email</span>
                            <input type="email" class="form-control" id="testEmail" placeholder="name@example.com">
                            <button type="button" class="btn btn-primary" id="testUrl" data-url="{{ route('fresnsemail.settings.test') }}" onclick="send_mail_test()">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                                @lang('SmtpEmail::fresns.testMailSend')
                            </button>
                        </div>
                    </div>
                    <!-- Variable -->
                    <div class="tab-pane fade" id="nav-variable" role="tabpanel" aria-labelledby="nav-variable-tab">
                        <div class="alert alert-info mt-4" role="alert">
                            @lang('FsLang::panel.menu_systems') > @lang('FsLang::panel.sidebar_send') > @lang('FsLang::panel.sidebar_send_tab_templates')
                        </div>
                        <div class="input-group mt-3">
                            <span class="input-group-text">@lang('FsLang::panel.site_name')</span>
                            <span class="form-control">{sitename}</span>
                        </div>
                        <div class="input-group mt-3">
                            <span class="input-group-text">@lang('SmtpEmail::fresns.variableCode')</span>
                            <span class="form-control">{code}</span>
                        </div>
                        <div class="input-group mt-3">
                            <span class="input-group-text">@lang('SmtpEmail::fresns.variableTime')</span>
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

    <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ @asset('/static/js/jquery.min.js') }}"></script>

    <script>
        function send_mail_test(){
            var email = $('#testEmail').val();
            var url  = $('#testUrl').data('url');
            if(email == ''){
                alert("please input email value.");
                return false;
            }
            $('#testUrl').attr('disabled',"true");
            $('#testUrl').find(".spinner-border").show();

            $.ajax({
                type: "POST",
                url: url,
                data: {
                    email: email,
                    _token: '{{ csrf_token() }}',
                },
                cache: false,
                dataType: "json",
                success: function(json) {
                    if(json.code == '000000'){
                        $('#testUrl').removeAttr("disabled");
                        $('#testUrl').find(".spinner-border").hide();
                        alert('Send successfully, please check');
                    }else{
                        $('#testUrl').removeAttr("disabled");
                        $('#testUrl').find(".spinner-border").hide();
                        alert('Send failed, please check the configuration');
                    }
                },
                error: function() {
                    $('#testUrl').removeAttr("disabled");
                    $('#testUrl').find(".spinner-border").hide();
                    alert('Service error, please confirm e-mail support SMTP');
                }
            });
        }
    </script>

</body>
</html>
