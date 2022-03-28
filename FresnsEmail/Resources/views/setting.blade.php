<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns Email</title>
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/fresns-panel.css') }}">
</head>

<body>

    <main>
        <div class="container-lg p-0 p-lg-3">
            <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                <!-- top -->
                <div class="row mb-2">
                    <div class="col-7">
                        <h3>@lang('FresnsEmail::fresns.name') <span class="badge bg-secondary fs-9">v1.2.0</span></h3>
                        <p class="text-secondary">@lang('FresnsEmail::fresns.description')</p>
                    </div>
                    <div class="col-5">
                        <div class="input-group mt-2 mb-4 justify-content-lg-end px-1" role="group">
                            <a class="btn btn-outline-secondary" href="https://github.com/fresns/extensions/tree/main/FresnsEmail" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
                            <a class="btn btn-outline-secondary" href="https://gitee.com/fresns/extensions/tree/master/FresnsEmail" target="_blank" role="button"><i class="bi bi-git"></i> Gitee</a>
                        </div>
                    </div>
                </div>
                <!-- Menu -->
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-settings-tab" data-bs-toggle="tab" data-bs-target="#nav-settings" type="button" role="tab" aria-controls="nav-settings" aria-selected="true">@lang('FresnsEmail::fresns.menuConfig')</button>
                        <button class="nav-link" id="nav-test-tab" data-bs-toggle="tab" data-bs-target="#nav-test" type="button" role="tab" aria-controls="nav-test" aria-selected="false">@lang('FresnsEmail::fresns.menuTest')</button>
                    </div>
                </nav>
                <!-- tabContent -->
                <div class="tab-content" id="nav-tabContent">
                    <!-- Setting -->
                    <div class="tab-pane fade show active" id="nav-settings" role="tabpanel" aria-labelledby="nav-settings-tab">
                        <form class="mt-4" action="{{ route('fresnsemail.settings.store') }}" method="post">
                            @csrf
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail::fresns.smtpHost'):</label>
                                <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_host" placeholder="smtp.example.com" value="{{ old("fresnsemail_smtp_host", $content['fresnsemail_smtp_host'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail::fresns.smtpHostIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail::fresns.smtpPort'):</label>
                                <div class="col-lg-5"><input type="number" class="form-control" name="fresnsemail_smtp_port" placeholder="25" value="{{ old("fresnsemail_smtp_port", $content['fresnsemail_smtp_port'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail::fresns.smtpPortIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail::fresns.smtpUser'):</label>
                                <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_user" placeholder="name@example.com" value="{{ old("fresnsemail_smtp_user", $content['fresnsemail_smtp_user'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail::fresns.smtpUserIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail::fresns.smtpPassword'):</label>
                                <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_password" placeholder="Password" value="{{ old("fresnsemail_smtp_password", $content['fresnsemail_smtp_password'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail::fresns.smtpPasswordIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail::fresns.smtpVerifyType'):</label>
                                <div class="col-lg-5">
                                    <select class="form-select" name="fresnsemail_verify_type">
                                        <option value="" @if($content['fresnsemail_verify_type'] == '') selected @endif>Null</option>
                                        <option value="tls" @if($content['fresnsemail_verify_type'] == 'tls') selected @endif>tls</option>
                                        <option value="ssl" @if($content['fresnsemail_verify_type'] == 'ssl') selected @endif>ssl</option>
                                    </select>
                                </div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail::fresns.smtpVerifyTypeIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail::fresns.smtpFromMail'):</label>
                                <div class="col-lg-5"><input type="email" class="form-control" name="fresnsemail_from_mail" placeholder="name@example.com" value="{{ old("fresnsemail_from_mail", $content['fresnsemail_from_mail'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail::fresns.smtpFromMailIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail::fresns.smtpFromName'):</label>
                                <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_from_name" placeholder="Fresns" value="{{ old("fresnsemail_from_name", $content['fresnsemail_from_name'] ?? '') }}" ></div>
                                <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail::fresns.smtpFromNameIntro')</div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-lg-2"></div>
                                <div class="col-lg-10">
                                    <button type="submit" class="btn btn-primary">@lang('FresnsEmail::fresns.settingButton')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Test -->
                    <div class="tab-pane fade" id="nav-test" role="tabpanel" aria-labelledby="nav-test-tab">
                        <div class="alert alert-warning mt-4" role="alert">@lang('FresnsEmail::fresns.testMailDesc')</div>
                        <div class="input-group mt-3">
                            <span class="input-group-text" id="inputGroup-sizing-default">Email</span>
                            <input type="email" class="form-control" id="testEmail" placeholder="name@example.com">
                            <button type="button" class="btn btn-primary" id="testUrl" data-url="{{ route('fresnsemail.settings.test') }}" onclick="send_mail_test()">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display:none;"></span>
                                @lang('FresnsEmail::fresns.testMailSend')
                            </button>
                        </div>
                    </div>
                </div>
                <!-- end -->
            </div>
        </div>
    </main>

    <footer>
        <div class="copyright text-center">
            <p class="mt-5 mb-5 text-muted">&copy; 2021 Fresns</p>
        </div>
    </footer>

    <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ @asset('/static/js/jquery-3.6.0.min.js') }}"></script>

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
                data: {email:email,_token:'{{ csrf_token() }}'},
                cache: false,
                dataType: "json",
                success: function(json) {
                    if(json.code == '000000'){
                        $('#testUrl').removeAttr("disabled");
                        $('#testUrl').find(".spinner-border").hide();
                        alert('send success');
                    }else{
                        $('#testUrl').removeAttr("disabled");
                        $('#testUrl').find(".spinner-border").hide();
                        alert('send fail');
                    }
                },
                error: function() {
                    $('#testUrl').removeAttr("disabled");
                    $('#testUrl').find(".spinner-border").hide();
                    alert('server error');
                }
            });
        }
    </script>

</body>
</html>
