@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">FID</th>
                    <th scope="col" class="dropdown">
                        <a class="dropdown-toggle text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @empty(request('type')) {{ __('EasyManager::fresns.table_type') }} @endempty
                            @if (request('type') == 1) {{ __('EasyManager::fresns.image') }} @endif
                            @if (request('type') == 2) {{ __('EasyManager::fresns.video') }} @endif
                            @if (request('type') == 3) {{ __('EasyManager::fresns.audio') }} @endif
                            @if (request('type') == 4) {{ __('EasyManager::fresns.document') }} @endif
                        </a>

                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 1]) }}">{{ __('EasyManager::fresns.image') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 2]) }}">{{ __('EasyManager::fresns.video') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 3]) }}">{{ __('EasyManager::fresns.audio') }}</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => 4]) }}">{{ __('EasyManager::fresns.document') }}</a></li>
                        </ul>
                    </th>
                    <th scope="col">{{ __('EasyManager::fresns.table_name') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_file_size') }}</th>
                    <th scope="col">md5</th>
                    <th scope="col">sha</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_file_sha_type') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_file_parameters') }}</th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_file_upload_time') }}
                        @if (request('orderBy') === 'oldest')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'latest']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'oldest']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @endif
                    </th>
                    <th scope="col">{{ __('EasyManager::fresns.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($files as $file)
                    <tr>
                        <th scope="row">{{ $file->id }}</th>
                        <td>{{ $file->fid }}</td>
                        <td>
                            <span class="badge text-bg-light">
                                @switch($file->type)
                                    @case(1)
                                        {{ __('EasyManager::fresns.image') }}
                                    @break

                                    @case(2)
                                        {{ __('EasyManager::fresns.video') }}
                                    @break

                                    @case(3)
                                        {{ __('EasyManager::fresns.audio') }}
                                    @break

                                    @case(4)
                                        {{ __('EasyManager::fresns.document') }}
                                    @break

                                    @default
                                        -
                                @endswitch
                            </span>
                        </td>
                        <td>{{ $file->name }}</td>
                        <td>{{ $file->size }}</td>
                        <td>{{ $file->md5 }}</td>
                        <td>{{ $file->sha }}</td>
                        <td>{{ $file->sha_type }}</td>
                        <td>
                            @switch($file->type)
                                @case(1)
                                    {{ __('EasyManager::fresns.table_image_size') }}: {{ $file->image_width }}x{{ $file->image_height }}
                                @break

                                @case(2)
                                    {{ __('EasyManager::fresns.table_video_time') }}: {{ $file->video_time }} {{ __('EasyManager::fresns.unit_second') }} | {{ __('EasyManager::fresns.table_transcoding_state') }}: {{ $file->transcoding_state }}
                                @break

                                @case(3)
                                    {{ __('EasyManager::fresns.table_audio_time') }}: {{ $file->audio_time }} {{ __('EasyManager::fresns.unit_second') }} | {{ __('EasyManager::fresns.table_transcoding_state') }}: {{ $file->transcoding_state }}
                                @break

                                @default
                                    -
                            @endswitch
                        </td>
                        <td>{{ $file->created_at }}</td>
                        <td>
                            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-bs-title="{{ __('EasyManager::fresns.under_development') }}">
                                <button class="btn btn-outline-primary btn-sm me-2" type="button" disabled>{{ __('EasyManager::fresns.button_edit') }}</button>
                                <button class="btn btn-outline-danger btn-sm" type="button" disabled>{{ __('EasyManager::fresns.button_delete') }}</button>
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="py-3 d-flex justify-content-center">
        {{ $files->appends(request()->all())->links() }}
    </div>
@endsection
