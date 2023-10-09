<div class="row px-3 py-4 mx-0">
    <div class="col-7">
        <h3>{{ __('EditorWorkspace::fresns.name') }} <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
        <p class="text-secondary mb-0">{{ __('EditorWorkspace::fresns.description') }}</p>
    </div>
    <div class="col-5">
        <div class="input-group mt-2 justify-content-lg-end px-1" role="group">
            @if (Route::is('editor-workspace.admin.index'))
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addAccount"><i class="bi bi-plus-circle-dotted"></i> {{ __('FsLang::panel.button_add') }}</button>
            @endif
            <a class="btn btn-outline-secondary" href="{{ route('panel.user-feature.index') }}" target="_blank">{{ __('EditorWorkspace::fresns.config_user_feature') }}</a>
            <a class="btn btn-outline-secondary" href="https://github.com/fresns/plugins/tree/3.x/EditorWorkspace" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
        </div>
    </div>
</div>
