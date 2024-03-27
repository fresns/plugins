@extends('EasyManager::commons.fresns')

@section('content')
    @if (! $isSupportTags)
        <div class="alert alert-warning rounded-0 mb-0" role="alert">
            <p>{{ __('EasyManager::fresns.cache_description') }}</p>
            <p class="mb-0">{{ __('EasyManager::fresns.cache_driver') }}: <span class="badge text-bg-light">{{ $cacheDriver }}</span></p>
        </div>
    @endif
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info align-middle">
                    <th scope="col" class="dropdown">
                        {{ __('EasyManager::fresns.cache_tag') }}
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @empty(request('type')) {{ __('EasyManager::fresns.option_all') }} @endempty
                            @if (request('type')) {{ request('type') }} @endif
                        </button>
                        <ul class="dropdown-menu" style="z-index: 2000">
                            <li><a class="dropdown-item" href="{{ route('easy-manager.cache.index') }}">{{ __('EasyManager::fresns.option_all') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'System']) }}">System</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Config']) }}">Config</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Model']) }}">Model</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Account']) }}">Account</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'User']) }}">User</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Group']) }}">Group</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Hashtag']) }}">Hashtag</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Geotag']) }}">Geotag</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Post']) }}">Post</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Comment']) }}">Comment</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Extension']) }}">Extension</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Plugin']) }}">Plugin</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 'Web']) }}">Web</a></li>
                        </ul>
                    </th>
                    <th scope="col">{{ __('EasyManager::fresns.cache_name') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.cache_last_update_time') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cacheTags as $tag)
                    <tr>
                        <th scope="row">{{ $tag['tag'] }}</th>
                        <td>{{ $tag['name'] }}</td>
                        <td>{{ $tag['datetime'] }}</td>
                        <td>
                            @if ($isSupportTags)
                                <form action="{{ route('easy-manager.cache.destroy') }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <input type="hidden" name="tag" value="{{ $tag['tag'] }}"/>
                                    <button class="btn btn-outline-success btn-sm" type="submit">{{ __('FsLang::panel.button_clear_cache') }}</button>
                                </form>
                            @else
                                <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="{{ __('EasyManager::fresns.cache_not_tag') }}">
                                    <button class="btn btn-outline-success btn-sm" type="button" disabled>{{ __('FsLang::panel.button_clear_cache') }}</button>
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
