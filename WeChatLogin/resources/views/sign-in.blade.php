@extends('WeChatLogin::layout')

@section('content')
    <div class="m-4">
        @if ($isWeChat && $oauthUrl)
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="mt-3 text-center">
                <span class="badge rounded-pill text-bg-success fs-6 fw-normal px-4 py-3">微信登录中...</span>
            </div>
        @else
            <div class="position-relative text-center">
                <img src="{{ $wechatQrCode }}" class="img-thumbnail w-75" id="qrcode-img">

                <div class="position-absolute top-50 start-50 translate-middle">
                    <span class="badge rounded-pill text-bg-warning fs-6 fw-normal p-2 d-none" id="qrcode-tip">
                        <i class="bi bi-check2-circle"></i> 扫码成功，等待授权
                    </span>

                    <button type="button" class="btn btn-primary btn-sm mt-2 d-none" id="rescan">重新扫码</button>
                </div>
            </div>

            <div class="mt-3 text-center" id="sign-tip">
                <span class="badge rounded-pill text-bg-success fs-6 fw-normal px-4 py-3">请使用微信扫一扫登录</span>
            </div>
        @endif
    </div>
@endsection

@push('script')
    <script>
        const isWeChat = {{ $isWeChat ? '1' : '0' }};
        const postMessageKey = '{{ $postMessageKey }}';
        const oauthUrl = '{!! $oauthUrl !!}';

        if (isWeChat && oauthUrl) {
            window.top.location.href = oauthUrl;
        } else {
            $(document).ready(function () {
                let counter = 0;
                let maxCounter = 20;
                let firstTime32206 = true;

                function makeRequest() {
                    // 超过最大计数器值时的操作
                    console.log('callback counter', counter, firstTime32206);
                    if (counter >= maxCounter) {
                        document.getElementById('qrcode-img').classList.add('opacity-25');
                        document.getElementById('rescan').classList.remove('d-none');
                        return;
                    }

                    // 请求回调数据
                    $.get('{{ route("wechat-login.api.common.callback") }}', {
                        fskey: 'WeChatLogin',
                        ulid: '{{ $authUlid }}',
                    }).done(function (data) {
                        if (data.code == 0) {
                            counter = maxCounter;
                            const accountData = data.data.content;

                            if (accountData) {
                                const fresnsCallbackMessage = {
                                    code: accountData.code,
                                    message: accountData.message,
                                    action: {
                                        postMessageKey: postMessageKey,
                                        windowClose: true, // 是否关闭窗口或弹出层(modal)
                                        redirectUrl: '', // 是否重定向新页面
                                        dataHandler: 'reload', // 是否处理数据: add, remove, reload
                                    },
                                    data: accountData.data,
                                }

                                const messageString = JSON.stringify(fresnsCallbackMessage);
                                const userAgent = navigator.userAgent.toLowerCase();

                                switch (true) {
                                    case (window.Android !== undefined):
                                        // Android (addJavascriptInterface)
                                        window.Android.receiveMessage(messageString);
                                        break;

                                    case (window.webkit && window.webkit.messageHandlers.iOSHandler !== undefined):
                                        // iOS (WKScriptMessageHandler)
                                        window.webkit.messageHandlers.iOSHandler.postMessage(messageString);
                                        break;

                                    case (window.FresnsJavascriptChannel !== undefined):
                                        // Flutter
                                        window.FresnsJavascriptChannel.postMessage(messageString);
                                        break;

                                    case (window.ReactNativeWebView !== undefined):
                                        // React Native WebView
                                        window.ReactNativeWebView.postMessage(messageString);
                                        break;

                                    case (userAgent.indexOf('miniprogram') > -1):
                                        // WeChat Mini Program
                                        wx.miniProgram.postMessage({ data: messageString });
                                        break;

                                    // Web
                                    default:
                                        parent.postMessage(messageString, '*');
                                }
                            }
                        }

                        if (data.code == 32206) {
                            if (firstTime32206) {
                                counter = 0;
                                firstTime32206 = false;
                            }
                            document.getElementById('qrcode-img').classList.add('opacity-25');
                            document.getElementById('qrcode-tip').classList.remove('d-none');
                        }

                        counter++;

                        setTimeout(makeRequest, 3000);
                    });
                }

                makeRequest();

                // 重新开始
                document.getElementById('rescan').addEventListener('click', () => {
                    $.post('{{ route("wechat-login.api.common.recallback") }}', {
                        fskey: 'WeChatLogin',
                        ulid: '{{ $authUlid }}',
                    });

                    counter = 0;
                    firstTime32206 = true;
                    document.getElementById('qrcode-img').classList.remove('opacity-25');
                    document.getElementById('qrcode-tip').classList.add('d-none');
                    document.getElementById('rescan').classList.add('d-none');

                    makeRequest();
                });
            });
        }
    </script>
@endpush
