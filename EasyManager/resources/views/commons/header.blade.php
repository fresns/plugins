<div class="row px-3 py-4 mx-0">
    <div class="col-7">
        <h3>{{ __('EasyManager::fresns.name') }} <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
        <p class="text-secondary mb-0">{{ __('EasyManager::fresns.description') }}</p>
    </div>
    <div class="col-5">
        <div class="input-group mt-2 justify-content-lg-end px-1" role="group">
            <a class="btn btn-outline-secondary" href="{{ request()->fullUrl() }}" target="_blank" role="button"><i class="bi bi-box-arrow-up-right"></i> {{ __('EasyManager::fresns.new_window') }}</a>
            <a class="btn btn-outline-secondary" href="https://github.com/fresns/extensions/tree/release/EasyManager" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.home') ? 'active' : ''}}" href="{{ route('easy-manager.home') }}">{{ __('EasyManager::fresns.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.account.*') ? 'active' : ''}}" href="{{ route('easy-manager.account.index') }}">{{ __('EasyManager::fresns.account') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.user.*') ? 'active' : ''}}" href="{{ route('easy-manager.user.index') }}">{{ __('EasyManager::fresns.user') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.group.*') ? 'active' : ''}}" href="{{ route('easy-manager.group.index') }}">{{ __('EasyManager::fresns.group') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.hashtag.*') ? 'active' : ''}}" href="{{ route('easy-manager.hashtag.index') }}">{{ __('EasyManager::fresns.hashtag') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.post.*') ? 'active' : ''}}" href="{{ route('easy-manager.post.index') }}">{{ __('EasyManager::fresns.post') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.comment.*') ? 'active' : ''}}" href="{{ route('easy-manager.comment.index') }}">{{ __('EasyManager::fresns.comment') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.file.*') ? 'active' : ''}}" href="{{ route('easy-manager.file.index') }}">{{ __('EasyManager::fresns.file') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('easy-manager.cache.*') ? 'active' : ''}}" href="{{ route('easy-manager.cache.index') }}">{{ __('EasyManager::fresns.cache') }}</a>
                </li>
            </ul>

            @if ($search['status'])
                <form class="d-flex" role="search" action="{{ $search['action'] }}" method="get">
                    <div class="input-group" id="inputNameSelect">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="inputNameSelectBtn">{{ $search['defaultSelect']['name'] }}</button>
                        <ul class="dropdown-menu">
                            @foreach ($search['selects'] as $select)
                                <li><a class="dropdown-item" href="#" data-value="{{ $select['value'] }}" data-name="{{ $select['name'] }}">{{ $select['name'] }}</a></li>
                            @endforeach
                        </ul>
                        <input class="form-control" type="search" placeholder="Search" aria-label="Search" id="inputName" name="{{ $search['defaultSelect']['value'] }}">
                        <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</nav>
