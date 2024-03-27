<div class="row px-3 py-4 mx-0">
    <div class="col-7">
        <h3>{{ __('ConfigManager::fresns.name') }} <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
        <p class="text-secondary mb-0">{{ __('ConfigManager::fresns.description') }}</p>
    </div>
    <div class="col-5">
        <div class="input-group mt-2 justify-content-lg-end px-1" role="group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal"><i class="bi bi-plus-circle-dotted"></i> {{ __('FsLang::panel.button_add') }}</button>
        </div>
    </div>
</div>
