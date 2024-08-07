@extends('GitHubSignIn::layout')

@section('content')
    <div class="m-4">
        <div class="d-flex justify-content-center mt-5">
            <div class="spinner-border text-dark" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="mt-5 text-center">
            <span class="badge rounded-pill text-bg-dark fs-6 fw-normal px-4 py-3">{{ $fresnsLang['inProgress'] }}</span>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const oauthUrl = '{!! $oauthUrl !!}';

        if (oauthUrl) {
            window.open(oauthUrl, '_top');
        }
    </script>
@endpush
