@extends('TitleIcons::commons.layout')

@section('content')
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-50 start-50 translate-middle">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="/static/images/icon.png" width="20px" height="20px" class="rounded me-2" alt="Fresns">
                <strong class="me-auto">Fresns</strong>
                <small>{{ $code }}</small>
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

        if (code == 0) {
            const fresnsCallbackMessage = {
                code: 0,
                message: 'ok',
                action: {
                    postMessageKey: 'reload',
                    windowClose: true,
                    reloadData: true,
                    redirectUrl: '',
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

                case (userAgent.indexOf('miniprogram') > -1 && wx && wx.miniProgram):
                    // WeChat Mini Program
                    wx.miniProgram.postMessage({ data: messageString });
                    break;

                // Web
                default:
                    parent.postMessage(messageString, '*');
            }
        }
    </script>
    <script src="//res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
@endpush
