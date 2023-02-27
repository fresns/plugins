@extends('FileStorage::layout')

@section('content')
    <form action="{{ route('file-storage.admin.upload.file') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('post')

        <div class="input-group">
            <select class="form-select" name="type">
                <option value="image">{{ __('FsLang::panel.editor_image') }}</option>
                <option value="video">{{ __('FsLang::panel.editor_video') }}</option>
                <option value="audio">{{ __('FsLang::panel.editor_audio') }}</option>
                <option value="document">{{ __('FsLang::panel.editor_document') }}</option>
            </select>
            <input type="file" class="form-control w-50" name="file">
            <button class="btn btn-outline-secondary" type="submit">{{ __('FsLang::panel.button_confirm') }}</button>
        </div>
    </form>

    <div class="table-responsive mt-3">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">FID</th>
                    <th scope="col">{{ __('FsLang::panel.table_type') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_name') }}</th>
                    <th scope="col">{{ __('FsLang::panel.button_view') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($files as $file)
                    <tr>
                        <th scope="row">{{ $file['fid'] }}</th>
                        <td>
                            <span class="badge text-bg-light">
                                @switch($file['type'])
                                    @case(1)
                                        {{ __('FsLang::panel.editor_image') }}
                                    @break

                                    @case(2)
                                        {{ __('FsLang::panel.editor_video') }}
                                    @break

                                    @case(3)
                                        {{ __('FsLang::panel.editor_audio') }}
                                    @break

                                    @case(4)
                                        {{ __('FsLang::panel.editor_document') }}
                                    @break

                                    @default
                                        -
                                @endswitch
                            </span>
                        </td>
                        <td>{{ $file['name'] }}</td>
                        <td>
                            @switch($file['type'])
                                @case(1)
                                    <img src="{{ $file['imageSquareUrl'] }}" width="100" height="100">
                                @break

                                @case(2)
                                    <video controls preload="metadata" controls="true" controlslist="nodownload" poster="{{ $file['videoPosterUrl'] }}" width="200" height="100">
                                        <source src="{{ $file['videoUrl'] }}" type="{{ $file['mime'] }}">
                                        <div class="alert alert-warning my-2" role="alert">Your browser does not support the video element.</div>
                                    </video>
                                @break

                                @case(3)
                                    <audio class="w-100" src="{{ $file['audioUrl'] }}" controls="controls" preload="metadata" controlsList="nodownload" oncontextmenu="return false">
                                        <div class="alert alert-warning my-2" role="alert">Your browser does not support the audio element.</div>
                                    </audio>
                                @break

                                @case(4)
                                    <a href="{{ $file['documentUrl'] }}" target="_blank">{{ $file['name'] }}</a>
                                @break

                                @default
                                    -
                            @endswitch
                        </td>
                        <td>
                            <form action="{{ route('file-storage.admin.delete.file', ['type' => $file['type'], 'fid' => $file['fid']]) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('FsLang::panel.button_delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
