@if ($data['stickyPosts'])
    <div class="fs-list-group mb-3">
        <h4 class="fs-5 px-3 pb-1 pt-3">{{ $titleArr['sticky'] }}</h4>
        @foreach($data['stickyPosts'] as $sticky)
            <a href="{{ $urlArr['postDetail'].$sticky['pid'] }}" class="list-group-item list-group-item-action text-break px-3 py-2">
                <i class="fa-regular fa-circle-up me-1 text-danger"></i>
                {{ $sticky['title'] ?? Str::limit(strip_tags($sticky['content']), 80) }}
            </a>
        @endforeach
    </div>
@endif

<div class="fs-list-group mb-3">
    <div class="d-flex">
        <h4 class="fs-5 px-3 pb-1 pt-3 flex-grow-1">{{ $titleArr['hashtag'] }}</h4>

        @if ($urlArr['hashtag'])
            <div class="pt-3 pe-4">
                <a href="{{ $urlArr['hashtag'] }}" class="text-decoration-none">{{ $titleArr['more'] }}</a>
            </div>
        @endif
    </div>

    <div class="mx-3 py-2">
        @foreach($data['hashtags'] as $hashtag)
            <a href="{{ $urlArr['hashtagDetail'].$hashtag['hid'] }}" class="badge bg-primary-subtle border border-primary-subtle text-primary-emphasis rounded-pill text-decoration-none mb-3 me-2 fs-6">{{ $hashtag['hname'] }}</a>
        @endforeach
    </div>
</div>

<div class="fs-list-group mb-3">
    <div class="d-flex">
        <h4 class="fs-5 px-3 pb-1 pt-3 flex-grow-1">{{ $titleArr['user'] }}</h4>

        @if ($urlArr['user'])
            <div class="pt-3 pe-4">
                <a href="{{ $urlArr['user'] }}" class="text-decoration-none">{{ $titleArr['more'] }}</a>
            </div>
        @endif
    </div>

    <div class="d-flex flex-wrap mx-3 py-2">
        @foreach($data['users'] as $user)
            <a href="{{ $urlArr['userDetail'].$user['fsid'] }}" class="badge d-flex align-items-center p-1 pe-2 text-dark-emphasis bg-light-subtle border border-dark-subtle rounded-pill text-decoration-none mb-3 me-2 fs-6">
                <img class="rounded-circle me-1" width="24" height="24" src="{{ $user['avatar'] }}" alt="{{ $user['nickname'] }}">{{ $user['nickname'] }}
            </a>
        @endforeach
    </div>
</div>

<script src="{{ config('app.url') }}/static/js/masonry.pkgd.min.js"></script>

<div class="fs-list-group mb-3">
    <div class="d-flex">
        <h4 class="fs-5 px-3 pb-1 pt-3 flex-grow-1">{{ $titleArr['post'] }}</h4>

        @if ($urlArr['post'])
            <div class="pt-3 pe-4">
                <a href="{{ $urlArr['post'] }}" class="text-decoration-none">{{ $titleArr['more'] }}</a>
            </div>
        @endif
    </div>

    <div class="row mx-3 py-2" data-masonry='{"percentPosition": true }'>
        @foreach($data['posts'] as $post)
            <div class="col-6 col-lg-3 mb-3">
                <a href="{{ $urlArr['postDetail'].$post['pid'] }}" class="card text-decoration-none link-dark">
                    @if ($post['files']['images'])
                        <img class="bd-placeholder-img card-img-top" src="{{ $post['files']['images'][0]['imageRatioUrl'] }}">
                    @endif
                    <div class="card-body">
                        @if ($post['title'])
                            <h5 class="card-title">{{ $post['title'] }}</h5>
                        @endif
                        <p class="card-text">{{ Str::limit(strip_tags($post['content']), 140) }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
