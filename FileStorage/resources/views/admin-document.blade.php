@extends('FileStorage::layout')

@section('content')
    <form action="{{ route('file-storage.admin.update') }}" method="post">
        @csrf
        @method('put')

        <input type="hidden" name="type" value="document">

        {{-- Driver --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('FileStorage::fresns.driver') }}:</label>
            <div class="col-lg-5">
                <select class="form-select" name="driver">
                    <option value="local" {{ $documentDriver == 'local' ? 'selected' : '' }}>Local</option>
                    <option value="ftp" {{ $documentDriver == 'ftp' ? 'selected' : '' }}>FTP</option>
                    <option value="sftp" {{ $documentDriver == 'sftp' ? 'selected' : '' }}>SFTP</option>
                </select>
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.driverIntro') }}</div>
        </div>
        {{-- Storage Service Config --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('FsLang::panel.storage_service_config') }}:</label>
            <div class="col-lg-5 pt-1">
                <a class="btn btn-outline-secondary btn-sm px-4 me-2" href="{{ route('panel.storage.document.index') }}" target="_blank" role="button">{{ __('FsLang::panel.button_config') }}</a>
                <a href="{{ $marketUrl.'/detail/FileStorage' }}" target="_blank" class="link-primary fs-7">{{ __('FsLang::panel.button_support') }}</a>
            </div>
        </div>

        {{-- SSH Config --}}
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">Private Key:</label>
            <div class="col-lg-5">
                <textarea class="form-control" id="privateKey" name="privateKey" rows="5">{{ $documentPrivateKey }}</textarea>
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.privateKeyIntro') }}</div>
        </div>
        <div class="row mb-2">
            <label class="col-lg-3 col-form-label text-lg-end">SSH Passphrase:</label>
            <div class="col-lg-5">
                <input type="text" class="form-control" id="passphrase" name="passphrase" value="{{ $documentPassphrase }}">
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.passphraseIntro') }}</div>
        </div>
        <div class="row mb-4">
            <label class="col-lg-3 col-form-label text-lg-end">Host Fingerprint:</label>
            <div class="col-lg-5">
                <input type="text" class="form-control" id="hostFingerprint" name="hostFingerprint" value="{{ $documentHostFingerprint }}">
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FileStorage::fresns.hostFingerprintIntro') }}</div>
        </div>

        {{-- Save Button --}}
        <div class="row mb-4">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">
                <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
            </div>
        </div>
    </form>
@endsection
