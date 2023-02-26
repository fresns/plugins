@extends('EasyManager::commons.fresns')

@section('content')
    <div class="row mx-0 p-3 p-lg-5 pb-0">
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.account') }}</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['accountCount'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.user') }}</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['userCount'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.group') }}</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['groupCount'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.hashtag') }}</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['hashtagCount'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.post') }}</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['postCount'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.post') }}: Digest 1</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['postDigest1Count'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.post') }}: Digest 2</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['postDigest2Count'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.comment') }}</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['commentCount'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.comment') }}: Digest 1</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['commentDigest1Count'] }}</div>
            </div>
        </div>
        <div class="col-3 mb-5 pe-4">
            <div class="position-relative bg-light rounded shadow border py-5">
                <div class="position-absolute top-0 start-0 mt-3 ms-3 fs-7">{{ __('EasyManager::fresns.comment') }}: Digest 2</div>
                <div class="text-center fs-1 fw-bolder">{{ $overview['commentDigest2Count'] }}</div>
            </div>
        </div>
    </div>
@endsection
