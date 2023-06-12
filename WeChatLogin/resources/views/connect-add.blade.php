@extends('WeChatLogin::layout')

@section('content')
    <div class="m-4">
        @if ($isWeChat)
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="mt-3 text-center">
                <span class="badge rounded-pill text-bg-success fs-6 fw-normal px-4 py-3">微信授权中...</span>
            </div>
        @else
            <div class="position-relative text-center">
                <img src="{{ $wechatQrCode }}" class="img-thumbnail w-75" id="qrcode-img">

                <div class="position-absolute top-50 start-50 translate-middle">
                    <span class="badge rounded-pill text-bg-warning fs-6 fw-normal p-2 d-none" id="qrcode-tip">
                        <i class="bi bi-check2-circle"></i> 扫码成功，等待授权
                    </span>

                    <span class="badge rounded-pill text-bg-success fs-6 fw-normal p-2 d-none" id="qrcode-success">
                        <i class="bi bi-check2-circle"></i> 绑定成功
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

        if (isWeChat) {
            console.log('oauthUrl', oauthUrl);

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

                            document.getElementById('qrcode-img').classList.add('opacity-25');
                            document.getElementById('qrcode-tip').classList.add('d-none');
                            document.getElementById('qrcode-success').classList.remove('d-none');

                            const fresnsCallbackMessage = {
                                code: 0,
                                message: 'ok',
                                action: {
                                    postMessageKey: postMessageKey,
                                    windowClose: true, // 是否关闭窗口或弹出层(modal)
                                    reloadData: true, // 是否重载数据
                                    redirectUrl: '', // 是否重定向新页面
                                },
                                data: '',
                            }

                            parent.postMessage(JSON.stringify(fresnsCallbackMessage), '*');
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
