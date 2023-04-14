@extends('FileStorage::layout')

@section('content')
    <form action="{{ route('file-storage.admin.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')

        <input type="hidden" name="type" value="image">

        {{-- Driver --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('FileStorage::fresns.driver') }}:</label>
            <div class="col-lg-5">
                <select class="form-select" name="driver">
                    <option value="local" {{ $imageDriver == 'local' ? 'selected' : '' }}>Local</option>
                    <option value="ftp" {{ $imageDriver == 'ftp' ? 'selected' : '' }}>FTP</option>
                    <option value="sftp" {{ $imageDriver == 'sftp' ? 'selected' : '' }}>SFTP</option>
                </select>
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.driverIntro') }}</div>
        </div>
        {{-- Storage Service Config --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('FsLang::panel.storage_service_config') }}:</label>
            <div class="col-lg-5 pt-1">
                <a class="btn btn-outline-secondary btn-sm px-4 me-2" href="{{ route('panel.storage.image.index') }}" target="_blank" role="button">{{ __('FsLang::panel.button_config') }}</a>
                <a href="{{ $marketUrl.'/detail/FileStorage' }}" target="_blank" class="link-primary fs-7">{{ __('FsLang::panel.button_support') }}</a>
            </div>
        </div>

        {{-- SSH Config --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">Private Key:</label>
            <div class="col-lg-5">
                <textarea class="form-control" id="privateKey" name="privateKey" rows="5">{{ $imagePrivateKey }}</textarea>
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.privateKeyIntro') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">SSH Passphrase:</label>
            <div class="col-lg-5">
                <input type="text" class="form-control" id="passphrase" name="passphrase" value="{{ $imagePassphrase }}">
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.passphraseIntro') }}</div>
        </div>
        <div class="row mb-4">
            <label class="col-lg-3 col-form-label text-lg-end">Host Fingerprint:</label>
            <div class="col-lg-5">
                <input type="text" class="form-control" id="hostFingerprint" name="hostFingerprint" value="{{ $imageHostFingerprint }}">
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.hostFingerprintIntro') }}</div>
        </div>

        {{-- Image Config --}}
        <div class="row mb-4">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('FsLang::panel.storage_function_image_config') }}:</label>
            <div class="col-lg-9">
                {{-- imageProcessingStatus --}}
                <div class="row mb-2">
                    <div class="col-lg-7">
                        <div class="input-group">
                            <label class="input-group-text">{{ __('FileStorage::fresns.imageProcessingStatus') }}</label>
                            <select class="form-select" name="imageProcessingStatus">
                                <option value="close" {{ $imageProcessingStatus == 'close' ? 'selected' : '' }}>{{ __('FsLang::panel.option_close') }}</option>
                                <option value="open" {{ $imageProcessingStatus == 'open' ? 'selected' : '' }}>{{ __('FsLang::panel.option_open') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-5 form-text pt-1">
                        <i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.imageProcessingLibraryIntro') }}
                    </div>
                </div>

                {{-- imageProcessingLibrary --}}
                <div class="row mb-2">
                    <div class="col-lg-7">
                        <div class="input-group">
                            <label class="input-group-text">{{ __('FileStorage::fresns.imageProcessingLibrary') }}</label>
                            <select class="form-select" name="imageProcessingLibrary">
                                <option value="gd" {{ $imageProcessingLibrary == 'gd' ? 'selected' : '' }}>GD Library</option>
                                <option value="imagick" {{ $imageProcessingLibrary == 'imagick' ? 'selected' : '' }}>Imagick</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-5 form-text pt-1">
                        <i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.imageProcessingLibraryIntro') }}
                    </div>
                </div>

                {{-- storage_image_thumb_config --}}
                <div class="row mb-2">
                    <div class="col-lg-7">
                        <div class="input-group">
                            <label class="input-group-text">{{ __('FsLang::panel.storage_image_thumb_config') }}</label>
                            <input type="text" class="form-control" name="imageProcessingParams[config]" value="{{ $imageProcessingParams['config'] ?? 400 }}">
                        </div>
                    </div>
                    <div class="col-lg-5 form-text pt-1">
                        <i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.imageMaxWidth') }}
                    </div>
                </div>

                {{-- storage_image_thumb_ratio --}}
                <div class="row mb-2">
                    <div class="col-lg-7">
                        <div class="input-group">
                            <label class="input-group-text">{{ __('FsLang::panel.storage_image_thumb_ratio') }}</label>
                            <input type="text" class="form-control" name="imageProcessingParams[ratio]" value="{{ $imageProcessingParams['ratio'] ?? 400 }}">
                        </div>
                    </div>
                    <div class="col-lg-5 form-text pt-1">
                        <i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.imageMaxWidth') }}
                    </div>
                </div>

                {{-- storage_image_thumb_square --}}
                <div class="row mb-2">
                    <div class="col-lg-7">
                        <div class="input-group">
                            <label class="input-group-text">{{ __('FsLang::panel.storage_image_thumb_square') }}</label>
                            <input type="text" class="form-control" name="imageProcessingParams[square]" value="{{ $imageProcessingParams['square'] ?? 200 }}">
                        </div>
                    </div>
                    <div class="col-lg-5 form-text pt-1">
                        <i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.imageSquareSize') }}
                    </div>
                </div>

                {{-- storage_image_thumb_big --}}
                <div class="row mb-2">
                    <div class="col-lg-7">
                        <div class="input-group">
                            <label class="input-group-text">{{ __('FsLang::panel.storage_image_thumb_big') }}</label>
                            <input type="text" class="form-control" name="imageProcessingParams[big]" value="{{ $imageProcessingParams['big'] ?? 1500 }}">
                        </div>
                    </div>
                    <div class="col-lg-5 form-text pt-1">
                        <i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.imageMaxWidth') }}
                    </div>
                </div>

                {{-- imageWatermarkFile --}}
                <div class="row mb-2">
                    <div class="col-lg-7">
                        <div class="input-group">
                            <label class="input-group-text">{{ __('FileStorage::fresns.imageWatermarkFile') }}</label>
                            <input type="file" class="form-control" name="imageWatermarkFile" accept="image/png">
                            <!--Preview-->
                            @if ($watermarkFileUrl)
                                <button class="btn btn-outline-secondary preview-image" type="button" data-url="{{ $watermarkFileUrl }}">{{ __('FsLang::panel.button_view') }}</button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7">
                        <div class="input-group">
                            <label class="input-group-text">{{ __('FileStorage::fresns.imageWatermarkConfig') }}</label>
                            <select class="form-select" name="imageWatermarkConfig[status]">
                                <option value="close" {{ $imageWatermarkConfig['status'] == 'close' ? 'selected' : '' }}>{{ __('FsLang::panel.option_close') }}</option>
                                <option value="open" {{ $imageWatermarkConfig['status'] == 'open' ? 'selected' : '' }}>{{ __('FsLang::panel.option_open') }}</option>
                            </select>
                            <select class="form-select" name="imageWatermarkConfig[position]">
                                <option value="top-left" {{ $imageWatermarkConfig['position'] == 'top-left' ? 'selected' : '' }}>top-left</option>
                                <option value="top" {{ $imageWatermarkConfig['position'] == 'top' ? 'selected' : '' }}>top</option>
                                <option value="top-right" {{ $imageWatermarkConfig['position'] == 'top-right' ? 'selected' : '' }}>top-right</option>
                                <option value="left" {{ $imageWatermarkConfig['position'] == 'left' ? 'selected' : '' }}>left</option>
                                <option value="center" {{ $imageWatermarkConfig['position'] == 'center' ? 'selected' : '' }}>center</option>
                                <option value="right" {{ $imageWatermarkConfig['position'] == 'right' ? 'selected' : '' }}>right</option>
                                <option value="bottom-left" {{ $imageWatermarkConfig['position'] == 'bottom-left' ? 'selected' : '' }}>bottom-left</option>
                                <option value="bottom" {{ $imageWatermarkConfig['position'] == 'bottom' ? 'selected' : '' }}>bottom</option>
                                <option value="bottom-right" {{ $imageWatermarkConfig['position'] == 'bottom-right' ? 'selected' : '' }}>bottom-right</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="row mb-4">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">
                <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
            </div>
        </div>
    </form>

    @if ($fileUsages->isNotEmpty())
        <div class="table-responsive mt-5">
            <table class="table table-hover align-middle text-nowrap">
                <thead>
                    <tr class="table-info">
                        <th scope="col">{{ __('FileStorage::fresns.imageWatermarkFile') }}</th>
                        <th scope="col">{{ __('FsLang::panel.table_description') }}</th>
                        <th scope="col">{{ __('FsLang::panel.table_status') }}</th>
                        <th scope="col">{{ __('FsLang::panel.table_options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fileUsages as $fileUsage)
                        <tr>
                            <td><a href="#" class="preview-image" data-url="{{ Storage::url($fileUsage?->file?->path) }}">{{ $fileUsage?->file?->path }}</a></td>
                            <td>{{ $fileUsage?->file?->created_at }}</td>
                            <td>{!! ($fileUsage?->file?->id == $watermarkFile?->id) ? '<i class="bi bi-check-lg text-success"></i>' : '' !!}</td>
                            <td>
                                <form action="{{ route('file-storage.admin.delete.file', ['type' => $fileUsage->file_type, 'fid' => $fileUsage?->file?->fid]) }}" method="post">
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
    @endif

    <!--imageZoom-->
    <div class="modal fade image-zoom" id="imageZoom" tabindex="-1" aria-labelledby="imageZoomLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="position-relative image-box">
                <img class="img-fluid" src="">
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // preview image
        $('.preview-image').click(function () {
            let url = $(this).data('url');
            if (url) {
                $('#imageZoom').find('img').attr('src', url);
                $('#imageZoom').modal('show');
            }
        });
    </script>
@endpush
