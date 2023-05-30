<div class="row px-3 py-4 mx-0">
    <div class="col-7">
        <h3>Title Icons <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
        <p class="text-secondary mb-0">Content icon manager extension to manage post and comment title icons.</p>
    </div>
    <div class="col-5">
        <div class="input-group mt-2 justify-content-lg-end px-1" role="group">
            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editOperation" data-action="{{ route('title-icons.admin.store') }}"><i class="bi bi-plus-circle-dotted"></i> {{ __('FsLang::panel.button_add') }}</button>
            <a class="btn btn-outline-secondary" href="https://github.com/fresns/extensions/tree/release/TitleIcons" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
        </div>
    </div>
</div>
