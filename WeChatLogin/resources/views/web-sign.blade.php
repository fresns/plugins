@extends('WeChatLogin::layout')

@section('content')
    <div class="m-4">
        @if ($isWeChat)
            @if ($code)
                <div class="text-center fs-1">
                    <i class="bi bi-check-circle-fill text-success"></i>
                </div>
                <div class="mt-3 text-center">
                    <span class="badge rounded-pill text-bg-success fs-6 fw-normal px-4 py-3">授权成功</span>
                </div>
            @else
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <span class="badge rounded-pill text-bg-success fs-6 fw-normal px-4 py-3">微信登录中...</span>
                </div>
            @endif
        @else
            <div class="alert alert-warning" role="alert">请使用微信扫描二维码</div>
        @endif
    </div>
@endsection
