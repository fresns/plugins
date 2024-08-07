@extends('GitHubSignIn::layout')

@section('content')
    <div class="my-4">
        <div class="text-center">
            @if ($connectInfo->connect_avatar)
                <img src="{{ $connectInfo->connect_avatar }}" height="88" width="88" class="rounded-circle">
            @endif
        </div>
        <div class="mt-3 text-center">
            <span class="badge rounded-pill text-bg-dark fs-6 fw-normal py-2 px-3">{{ $connectInfo->connect_nickname }}</span>
        </div>
    </div>

    <div class="my-5 text-center">
        <a class="btn btn-danger py-2" href="{{ route('github-signin.connect.disconnect.result', [
            'authUlid' => $authUlid,
            'langTag' => $langTag,
        ]) }}" role="button">{{ $btnName }}</a>
    </div>
@endsection
