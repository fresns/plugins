@extends('SharePoster::layout')

@section('content')
    <form action="{{ route('share-poster.admin.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')

        {{-- font --}}
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label text-lg-end">{{ __('SharePoster::fresns.font') }}:</label>
            <div class="col-lg-4"><input type="file" class="form-control" accept=".ttf,.ttc,.fon,.otf" name="fontFile"></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('SharePoster::fresns.desc_font') }}</div>
        </div>

        <!-- button save -->
        <div class="row mb-5">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">
                <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
            </div>
        </div>
    </form>

    <form action="{{ route('share-poster.admin.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')

        <input type="hidden" name="resetFont" value="1">

        <div class="row mt-5 mb-4">
            <div class="col-lg-3"></div>
            <div class="col-lg-9">
                <button type="submit" class="btn btn-warning btn-sm">{{ __('SharePoster::fresns.button_reset_font') }}</button>
            </div>
        </div>
    </form>
@endsection
