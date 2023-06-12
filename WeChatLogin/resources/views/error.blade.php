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
                        reloadData: true, // 是否重载数据
                        redirectUrl: '', // 是否重定向新页面
                    },
                    data: '',
                }

                parent.postMessage(JSON.stringify(fresnsCallbackMessage), '*');
            }, 1500);
        }
    </script>
@endpush
