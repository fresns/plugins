<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>阿 Q 短信插件</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/bootstrap-icons.css">
    <link rel="stylesheet" href="/static/css/console.css">
</head>

<body>

    <main>
        <div class="container-lg p-0 p-lg-3">
            <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                <!-- top -->
                <div class="row mb-2">
                    <div class="col-7">
                        <h3>阿 Q 短信插件 <span class="badge bg-secondary fs-9">v1.1.0</span></h3>
                        <p class="text-secondary">Fresns 官方开发的「阿里云」和「腾讯云」二合一短信服务插件。</p>
                    </div>
                    <div class="col-5 text-end"></div>
                </div>
                <!-- Menu -->
                <div class="mb-3">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <button class="nav-link active">短信服务商配置</button>
                        </li>
                    </ul>
                </div>
                <!-- Setting -->
                <div class="tab-content">
                    <form method="post" action="#" class="mt-4" id="aqsms_form">
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">服务商:</label>
                            <div class="col-lg-5">
                                <select class="form-select" id="aqsms_type" name="aqsms_type">
                                    <option value="1" {{$aqsms_type == 1 ? 'selected' : ''}}>阿里云</option>
                                    <option value="2" {{$aqsms_type == 2 ? 'selected' : ''}}>腾讯云</option>
                                </select>
                            </div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 选择服务商填写对应的 Key 配置</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">Key ID:</label>
                            <div class="col-lg-5"><input type="text" class="form-control" id="aqsms_keyid" name="aqsms_keyid" placeholder="Key ID" value="{{$key_id}}"></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> Access Key ID 或 Secret ID</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">Key Secret:</label>
                            <div class="col-lg-5"><input type="text" class="form-control" id="aqsms_keysecret" name="aqsms_keysecret" placeholder="Key Secret" value="{{$key_secret}}"></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> Access Key Secret 或 Secret Key</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">SDK App ID:</label>
                            <div class="col-lg-5"><input type="text" class="form-control" id="aqsms_sdk_appid" name="aqsms_sdk_appid" placeholder="Sdk AppId" value="{{$sdk_appid}}"></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> 仅腾讯云使用，阿里云忽略</div>
                        </div>
                        <div class="row mb-4">
                            <label class="col-lg-2 col-form-label text-lg-end">匹配验证码模板:</label>
                            <div class="col-lg-5"><textarea class="form-control" id="aqsms_linked" name="aqsms_linked" rows="5">{{$sms_linked}}</textarea></div>
                            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> JSON Object 格式，以国际区号匹配验证码语言标签模板<br><code class="ms-3">{"国际区号":"验证码模板语言标签","other":"其他区号使用该模板"}</code><br><code class="ms-3">{"86":"zh-Hans","other":"en"}</code></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-2"></div>
                            <div class="col-lg-10">
                                <button type="submit" class="btn btn-primary" id="save_btn">保存</button>
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

    <!--Toast-->
    <div id="fresnsToast" class="toast align-items-center position-absolute top-50 start-50 translate-middle" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="save_msg">操作结果信息</div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    <script src="/static/js/jquery-3.6.0.min.js"></script>
    <script src="/static/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#save_btn").click(function (event) {
                //stop submit the form, we will post it manually.
                event.preventDefault();

                // Get form
                var form = $('#aqsms_form')[0];
                console.log(form)
                var data = new FormData(form);
                data.append("custom1", "custom test");

                $("#save_btn").prop("disabled", true);
                $.ajax({
                    url: '/aqsms/saveSetting',
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
                        $("#save_btn").prop("disabled", false);
                        // 显示 toast
                        // $("#fresnsToast").show().delay(3000).fadeOut();
                        $("#save_msg").text("保存成功");
                        $("#fresnsToast").addClass("show");
                    },
                    error: function (e){
                        $("#save_msg").text("保存失败");
                        $("#fresnsToast").addClass("show");
                        console.log("error", e)
                    }
                });
            });
        });
    </script>

</body>
</html>