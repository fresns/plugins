<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns Placeholder</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
</head>

<body>
    <table class="table table-hover">
        <thead>
            <tr class="table-primary">
                <th scope="col">Key</th>
                <th scope="col">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($params as $key => $value)
                <tr>
                    <th scope="row">{{ $key }}</th>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h5 class="mt-5 ms-2">Headers</h5>
    <table class="table table-hover">
        <thead>
            <tr class="table-primary">
                <th scope="col">Key</th>
                <th scope="col">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($headers as $key => $value)
                @if ($key == 'deviceInfo')
                    @continue;
                @endif

                <tr>
                    <th scope="row">{{ $key }}</th>
                    <td class="text-wrap text-break">{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h5 class="mt-5 ms-2">Device Info</h5>
    <table class="table table-hover">
        <thead>
            <tr class="table-primary">
                <th scope="col">Key</th>
                <th scope="col">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($headers['deviceInfo'] as $key => $value)
                <tr>
                    <th scope="row">{{ $key }}</th>
                    <td class="text-wrap text-break">{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script src="//res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
    <script>
        const fresnsCallbackMessage = {
            code: 0,
            message: 'ok',
            action: {
                postMessageKey: 'test',
                windowClose: false,
                reloadData: false,
                redirectUrl: '',
            },
            data: '',
        }

        const messageString = JSON.stringify(fresnsCallbackMessage);
        const userAgent = navigator.userAgent.toLowerCase();

        console.log('userAgent', userAgent);

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
    </script>
</body>

</html>
