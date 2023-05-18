@extends('WeChatLogin::layout')

@section('content')
    <div class="container">
        <div class="alert alert-primary mt-4" role="alert">
            您已经使用 <span class="text-success">{{ $wechatInfo['nickname'] }}</span> 授权成功，但是本站并未查询到对应的账号。
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-success mt-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTip" aria-expanded="false" aria-controls="collapseTip" id="collapse">我有账号，我要关联绑定</button>

            <div class="collapse" id="collapseTip">
                <div class="card card-body">请先使用「密码」或者「验证码」登录账号，登录后在用户中心的账号设置中绑定关联。</div>
            </div>

            <a class="btn btn-success mt-3 py-2" href="{{ $createAccountUrl }}" role="button">我没有账号，帮我生成新账号</a>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.getElementById('collapse').addEventListener('click', () => {
            var collapseBtn = document.getElementById('collapse');
            collapseBtn.classList.remove('btn-success');
            collapseBtn.classList.add('btn-outline-success');
            collapseBtn.setAttribute('disabled', 'disabled');
        });
    </script>
@endpush
