<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>微信登录支持</title>
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
                    <div class="col-8">
                        <h3>WeChatLogin <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
                        <p class="text-secondary">Fresns 官方开发的「微信登录」插件，支持网站、小程序、App 等各端的微信登录。</p>
                    </div>
                    <div class="col-4">
                        <div class="input-group mt-2 mb-4 justify-content-lg-end px-1" role="group">
                            <a class="btn btn-outline-secondary" href="https://github.com/fresns/extensions/tree/release/WeChatLogin" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
                        </div>
                    </div>
                </div>
                <!-- Menu -->
                <div class="mb-3">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="officialAccount-tab" data-bs-toggle="tab" data-bs-target="#officialAccount-tab-pane" type="button" role="tab" aria-controls="officialAccount-tab-pane" aria-selected="true">公众号</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="miniProgram-tab" data-bs-toggle="tab" data-bs-target="#miniProgram-tab-pane" type="button" role="tab" aria-controls="miniProgram-tab-pane" aria-selected="false">小程序</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="openPlatform-tab" data-bs-toggle="tab" data-bs-target="#openPlatform-tab-pane" type="button" role="tab" aria-controls="openPlatform-tab-pane" aria-selected="false">开放平台</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" role="tab" href="{{ route('panel.user.index') }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> 配置互联支持</a>
                        </li>
                    </ul>
                </div>

                <!-- Setting -->
                <form method="post" action="#" id="WeChatLoginForm">
                    <div class="tab-content" id="myTabContent">
                            <!-- 公众号 -->
                            <div class="tab-pane fade show active" id="officialAccount-tab-pane" role="tabpanel" aria-labelledby="officialAccount-tab" tabindex="0">
                                <div class="alert alert-warning" role="alert">仅支持微信认证的服务号，订阅号没有网页授权权限。</div>

                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">开发者ID(AppID):</label>
                                    <div class="col-lg-4"><input type="text" class="form-control" name="officialAccount[appId]" value="{{ $officialAccount['appId'] ?? '' }}"></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 开发者 ID 是公众号开发识别码</div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">开发者密码(AppSecret):</label>
                                    <div class="col-lg-4"><input type="text" class="form-control" name="officialAccount[appSecret]" value="{{ $officialAccount['appSecret'] ?? '' }}"></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 开发者密码是校验公众号开发者身份的密码</div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">网页授权域名:</label>
                                    <div class="col-lg-4"><input type="text" class="form-control bg-light" value="{{ config('app.url') }}" readonly></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 用户在网页授权页同意授权给公众号后，微信会将授权数据传给授权域名的回调页面</div>
                                </div>
                            </div>

                            <!-- 小程序 -->
                            <div class="tab-pane fade" id="miniProgram-tab-pane" role="tabpanel" aria-labelledby="miniProgram-tab" tabindex="0">
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">AppID(小程序ID):</label>
                                    <div class="col-lg-4"><input type="text" class="form-control" name="miniProgram[appId]" value="{{ $miniProgram['appId'] ?? '' }}"></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 小程序 ID</div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">AppSecret(小程序密钥):</label>
                                    <div class="col-lg-4"><input type="text" class="form-control" name="miniProgram[appSecret]" value="{{ $miniProgram['appSecret'] ?? '' }}"></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 小程序密钥</div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">要打开的小程序版本:</label>
                                    @php
                                        $envVersion = $miniProgram['envVersion'] ?? null;
                                    @endphp
                                    <div class="col-lg-4">
                                        <select class="form-select" name="miniProgram[envVersion]">
                                            <option value="release" {{ $envVersion == 'release' ? 'selected' : '' }}>正式版</option>
                                            <option value="trial" {{ $envVersion == 'trial' ? 'selected' : '' }}>体验版</option>
                                            <option value="develop" {{ $envVersion == 'develop' ? 'selected' : '' }}>开发版</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 授权网页登录时生成的小程序码要打开哪个小程序版本</div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">业务域名:</label>
                                    <div class="col-lg-4"><input type="text" class="form-control bg-light" value="{{ config('app.url') }}" readonly></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 配置为业务域名后，可使用 Fresns 扩展插件</div>
                                </div>
                            </div>

                            <!-- 开放平台 -->
                            <div class="tab-pane fade" id="openPlatform-tab-pane" role="tabpanel" aria-labelledby="openPlatform-tab" tabindex="0">
                                <div class="row mb-1">
                                    <label class="col-lg-3 col-form-label text-lg-end">网站应用 AppID:</label>
                                    <div class="col-lg-4"><input type="text" class="form-control" name="openPlatform[website][appId]" value="{{ $openPlatform['website']['appId'] ?? '' }}"></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 微信开放平台网站应用 AppID</div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">网站应用 AppSecret:</label>
                                    <div class="col-lg-4"><input type="text" class="form-control" name="openPlatform[website][appSecret]" value="{{ $openPlatform['website']['appSecret'] ?? '' }}"></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 微信开放平台网站应用 AppSecret</div>
                                </div>
                                <div class="row mb-1">
                                    <label class="col-lg-3 col-form-label text-lg-end">移动应用 AppID:</label>
                                    <div class="col-lg-4"><input type="text" class="form-control" name="openPlatform[mobile][appId]" value="{{ $openPlatform['mobile']['appId'] ?? '' }}"></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 微信开放平台移动应用 AppID</div>
                                </div>
                                <div class="row mb-4">
                                    <label class="col-lg-3 col-form-label text-lg-end">移动应用 AppSecret:</label>
                                    <div class="col-lg-4"><input type="text" class="form-control" name="openPlatform[mobile][appSecret]" value="{{ $openPlatform['mobile']['appSecret'] ?? '' }}"></div>
                                    <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 微信开放平台移动应用 AppSecret</div>
                                </div>
                            </div>

                            <!--保存按钮-->
                            <div class="row mb-4">
                                <div class="col-lg-3"></div>
                                <div class="col-lg-9">
                                    <button type="submit" class="btn btn-primary" id="saveButton">保存</button>
                                </div>
                            </div>
                    </div>
                </form>
                <!-- end -->
            </div>
        </div>
    </main>

    <footer>
        <div class="copyright text-center">
            <p class="mt-5 mb-5 text-muted">&copy; <span class="copyright-year"></span> Fresns</p>
        </div>
    </footer>

    <!--Toast-->
    <div id="fresnsToast" class="toast align-items-center position-absolute top-50 start-50 translate-middle" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="/static/images/icon.png" width="20px" height="20px" class="rounded me-2" alt="Fresns">
                <strong class="me-auto">Fresns</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="saveMsg">操作结果信息</div>
        </div>
    </div>

    <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ @asset('/static/js/jquery.min.js') }}"></script>

    <script>
        // copyright-year
        var yearElement = document.querySelector('.copyright-year');
        var currentDate = new Date();
        var currentYear = currentDate.getFullYear();
        yearElement.textContent = currentYear;

        $(document).ready(function () {
            $("#saveButton").click(function (event) {
                //stop submit the form, we will post it manually.
                event.preventDefault();

                // Get form
                var form = $('#WeChatLoginForm')[0];
                var data = new FormData(form);

                $("#saveButton").prop("disabled", true);
                $.ajax({
                    url: "{{ route('wechat-login.admin.update') }}",
                    type: 'post',
                    enctype: 'multipart/form-data',
                    data: data,
                    processData: false,  // Important!
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                    },
                    success: function (res) {
                        console.log("success ", res)
                        $("#saveButton").prop("disabled", false);
                        $("#fresnsToast").show().delay(2000).fadeOut();
                        $("#saveMsg").text("保存成功");
                        $("#fresnsToast").addClass("show");
                    },
                    error: function (e){
                        $("#fresnsToast").show().delay(3000).fadeOut();
                        $("#saveMsg").text("保存失败");
                        $("#fresnsToast").addClass("show");
                        console.log("error", e)
                    }
                });
            });
        });
    </script>

</body>
</html>
