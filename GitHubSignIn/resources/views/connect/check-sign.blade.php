@extends('GitHubSignIn::layout')

@section('content')
    <div class="container p-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-4">
                <header class="text-center">
                    <p><img src="{{ $siteLogo }}" height="30"></p>
                </header>

                <div class="alert alert-primary mt-4" role="alert">
                    {{ $fresnsLang['accountConnectEmpty'] }}
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary mt-3 py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTip" aria-expanded="false" aria-controls="collapseTip" id="collapse">{{ $fresnsLang['accountConnectLinked'] }}</button>

                    <div class="collapse" id="collapseTip">
                        <div class="card card-body">{{ $fresnsLang['accountConnectLinkedTip'] }}</div>
                        <p class="text-center my-3"><a href="{{ route('account-center.login') }}" class="link-primary">{{ $fresnsLang['accountLoginGoTo'] }}</a></p>
                    </div>

                    <a class="btn btn-primary mt-3 py-2" href="{{ $createAccountUrl }}" role="button">{{ $fresnsLang['accountConnectCreateNew'] }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.getElementById('collapse').addEventListener('click', () => {
            var collapseBtn = document.getElementById('collapse');
            collapseBtn.classList.remove('btn-primary');
            collapseBtn.classList.add('btn-outline-primary');
            collapseBtn.setAttribute('disabled', 'disabled');
        });
    </script>
@endpush
