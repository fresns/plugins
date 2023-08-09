@extends('WeChatLogin::layout')

@section('content')
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-50 start-50 translate-middle">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="/static/images/icon.png" width="20px" height="20px" class="rounded me-2" alt="Fresns">
                <strong class="me-auto">Fresns</strong>
                <small>{{ $code ? $code : '' }}</small>
            </div>
            <div class="toast-body">
                {{ $message }}
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const code = {{ $code }};

        if (code != 34406) {
            setTimeout(function() {
                const fresnsCallbackMessage = {
                    code: 0,
                    message: 'ok',
                    action: {
                        postMessageKey: 'fresnsConnect',
                        windowClose: true, // 是否关闭窗口或弹出层(modal)
                        redirectUrl: '', // 是否重定向新页面
                        dataHandler: 'reload', // 是否处理数据: add, remove, reload
                    },
                    data: '',
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
            }, 1500);
        }
    </script>
@endpush
