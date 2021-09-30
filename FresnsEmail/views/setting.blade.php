<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns Email</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/bootstrap-icons.css">
    <link rel="stylesheet" href="/static/css/backend.css">
</head>

<body>

    <main>
        <div class="container-lg p-0 p-lg-3">
            <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                <!-- top -->
                <div class="row mb-2">
                    <div class="col-7">
                        <h3>@lang('FresnsEmail/fresns.name') <span class="badge bg-secondary fs-9">v1.0</span></h3>
                        <p class="text-secondary">@lang('FresnsEmail/fresns.description')</p>
                    </div>
                    <div class="col-5 text-end"></div>
                </div>
                <!-- Menu -->
                <div class="mb-3">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <button class="nav-link active">@lang('FresnsEmail/fresns.menu')</button>
                        </li>
                    </ul>
                </div>
                <!-- Setting -->
                <div class="tab-content">
                    <form class="mt-4" action="{{ route('fresnsemail.settings.store') }}" method="post">
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail/fresns.smtpHost'):</label>
                            <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_host" placeholder="smtp.example.com" value="{{ old("fresnsemail_smtp_host", $content['fresnsemail_smtp_host'] ?? '') }}" ></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail/fresns.smtpHostIntro')</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail/fresns.smtpPort'):</label>
                            <div class="col-lg-5"><input type="number" class="form-control" name="fresnsemail_smtp_port" placeholder="25" value="{{ old("fresnsemail_smtp_port", $content['fresnsemail_smtp_port'] ?? '') }}" ></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail/fresns.smtpPortIntro')</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail/fresns.smtpUser'):</label>
                            <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_user" placeholder="name@example.com" value="{{ old("fresnsemail_smtp_user", $content['fresnsemail_smtp_user'] ?? '') }}" ></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail/fresns.smtpUserIntro')</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail/fresns.smtpPassword'):</label>
                            <div class="col-lg-5"><input type="text" class="form-control" name="fresnsemail_smtp_password" placeholder="Password" value="{{ old("fresnsemail_smtp_password", $content['fresnsemail_smtp_password'] ?? '') }}" ></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail/fresns.smtpPasswordIntro')</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail/fresns.smtpVerifyType'):</label>
                            <div class="col-lg-5">
                                <select class="form-select" name="fresnsemail_verify_type">
                                    <option value="" @if($content['fresnsemail_verify_type'] == '') selected @endif>Null</option>
                                    <option value="tls" @if($content['fresnsemail_verify_type'] == 'TLS') selected @endif>TLS</option>
                                    <option value="ssl" @if($content['fresnsemail_verify_type'] == 'SSL') selected @endif>SSL</option>
                                </select>
                            </div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail/fresns.smtpVerifyTypeIntro')</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail/fresns.smtpFromMail'):</label>
                            <div class="col-lg-5"><input type="email" class="form-control" name="send_email_from_mail" placeholder="name@example.com" value="{{ old("send_email_from_mail", $content['send_email_from_mail'] ?? '') }}" ></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail/fresns.smtpFromMailIntro')</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEmail/fresns.smtpFromName'):</label>
                            <div class="col-lg-5"><input type="text" class="form-control" name="send_email_from_name" placeholder="Fresns" value="{{ old("send_email_from_name", $content['send_email_from_name'] ?? '') }}" ></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEmail/fresns.smtpFromNameIntro')</div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-10">
                                <button type="submit" class="btn btn-primary">@lang('FresnsEmail/fresns.settingButton')</button>
                                <button type="button" class="btn btn-outline-primary ms-3" data-bs-toggle="modal" data-bs-target="#testMail">@lang('FresnsEmail/fresns.testMailModal')</button>
                            </div>
                        </div>
                    </form>
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

    <!-- Test Mail Modal -->
    <div class="modal fade" id="testMail" tabindex="-1" aria-labelledby="testMail" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <!-- header -->
                <div class="modal-header">
                    <h5 class="modal-title">@lang('FresnsEmail/fresns.testMailTitle')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- body -->
                <div class="modal-body">
                    <input type="email" class="form-control" id="testEmail" placeholder="name@example.com">
                </div>
                <!-- footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('FresnsEmail/fresns.testMailClose')</button>
                    <button type="button" class="btn btn-primary" id="testUrl" data-url="{{ route('fresnsemail.settings.test') }}" onclick="send_mail_test()">@lang('FresnsEmail/fresns.testMailSend')</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/static/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/jquery-3.6.0.min.js"></script>

    <script>
        function send_mail_test(){
            var email = $('#testEmail').val();
            var url  = $('#testUrl').data('url');
            if(email == ''){
                alert("please input email value.");
                return false;
            }
            $.ajax({
                type: "POST",
                url: url,
                data: {email:email},
                cache: false,
                dataType: "json",
                success: function(json) {
                    if(json.code == '000000'){
                        alert('send success');
                    }else{
                        alert('send fail');
                    }
                },
                error: function() {
                    alert('server error');
                }
            });
        }
    </script>
</body>
</html>
