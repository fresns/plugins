@extends('TitleIcons::commons.fresns')

@section('content')
    @php
        use \App\Helpers\FileHelper;
    @endphp
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">{{ __('FsLang::panel.table_name') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_description') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_icon') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_status') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_options') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($operations as $operation)
                    <tr>
                        <th scope="row">{{ $operation->id }}</th>
                        <td>{{ $operation->getLangName($defaultLanguage) }}</td>
                        <td>{{ $operation->getLangDescription($defaultLanguage) }}</td>
                        <td>
                            @php
                                $imageUrl = FileHelper::fresnsFileUrlByTableColumn($operation->image_file_id, $operation->image_file_url);
                            @endphp
                            <img src="{{ $imageUrl }}" style="max-height: 40px">
                        </td>
                        <td>{!! $operation->is_enabled ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-sm float-start me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editOperation"
                                data-name="{{ $operation->getLangName($defaultLanguage) }}"
                                data-description="{{ $operation->getLangDescription($defaultLanguage) }}"
                                data-action="{{ route('title-icons.admin.update', $operation) }}"
                                data-params="{{ $operation->toJson() }}">{{ __('FsLang::panel.button_edit') }}</button>

                            <form action="{{ route('title-icons.admin.destroy', $operation->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-outline-danger btn-sm delete-button">{{ __('FsLang::panel.button_delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="py-3 d-flex justify-content-center">
        {{ $operations->links() }}
    </div>

    <!--Modal-->
    <form action="" method="post" class="check-names" enctype="multipart/form-data">
        @csrf
        @method('put')

        <input type="hidden" name="update_name">
        <input type="hidden" name="update_description">

        <!-- Modal -->
        <div class="modal fade" id="editOperation" tabindex="-1" aria-labelledby="editOperation" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('FsLang::panel.table_icon') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-md-2 col-form-label">{{ __('FsLang::panel.table_name') }}</label>
                            <div class="col-sm-9 col-md-10">
                                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start name-button" data-bs-toggle="modal" data-parent="#editOperation" data-bs-target="#langModal">{{ __('FsLang::panel.table_name') }}</button>
                                <div class="invalid-feedback">{{ __('FsLang::tips.required_group_name') }}</div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-md-2 col-form-label">{{ __('FsLang::panel.table_description') }}</label>
                            <div class="col-sm-9 col-md-10">
                                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start desc-button" data-bs-toggle="modal" data-parent="#editOperation" data-bs-target="#langDescModal">{{ __('FsLang::panel.table_description') }}</button>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-md-2 col-form-label">{{ __('FsLang::panel.table_icon') }}</label>
                            <div class="col-sm-9 col-md-10">
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="showIcon">{{ __('FsLang::panel.button_image_upload') }}</button>
                                    <ul class="dropdown-menu selectInputType">
                                        <li data-name="inputFile"><a class="dropdown-item" href="#">{{ __('FsLang::panel.button_image_upload') }}</a></li>
                                        <li data-name="inputUrl"><a class="dropdown-item" href="#">{{ __('FsLang::panel.button_image_input') }}</a></li>
                                    </ul>
                                    <input type="file" class="form-control inputFile" name="image_file">
                                    <input type="text" class="form-control inputUrl" name="image_file_url" style="display:none;">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-md-2 col-form-label">{{ __('FsLang::panel.table_status') }}</label>
                            <div class="col-sm-9 col-md-10 pt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_enabled" id="status_true" value="1" checked>
                                    <label class="form-check-label" for="status_true">{{ __('FsLang::panel.option_activate') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_enabled" id="status_false" value="0">
                                    <label class="form-check-label" for="status_false">{{ __('FsLang::panel.option_deactivate') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-md-2 col-form-label"></label>
                            <div class="col-sm-9 col-md-10">
                                <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Name Language Modal -->
        <div class="modal fade name-lang-modal" id="langModal" tabindex="-1" aria-labelledby="langModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('FsLang::panel.table_name') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-nowrap">
                                <thead>
                                    <tr class="table-info">
                                        <th scope="col" class="w-25">{{ __('FsLang::panel.table_lang_tag') }}</th>
                                        <th scope="col" class="w-25">{{ __('FsLang::panel.table_lang_name') }}</th>
                                        <th scope="col" class="w-50">{{ __('FsLang::panel.table_content') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($optionalLanguages as $lang)
                                        <tr>
                                            <td>
                                                {{ $lang['langTag'] }}
                                                @if ($lang['langTag'] == $defaultLanguage)
                                                    <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('FsLang::panel.default_language') }}" data-bs-original-title="{{ __('FsLang::panel.default_language') }}" aria-label="{{ __('FsLang::panel.default_language') }}"></i>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $lang['langName'] }}
                                                @if ($lang['areaName'])
                                                    {{ '('.$lang['areaName'].')' }}
                                                @endif
                                            </td>
                                            <td><input type="text" name="names[{{ $lang['langTag'] }}]" class="form-control name-input" value=""></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!--button_confirm-->
                        <div class="text-center">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">{{ __('FsLang::panel.button_confirm') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desc Language Modal -->
        <div class="modal fade description-lang-modal" id="langDescModal" tabindex="-1" aria-labelledby="langDescModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('FsLang::panel.table_description') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-nowrap">
                                <thead>
                                    <tr class="table-info">
                                        <th scope="col" class="w-25">{{ __('FsLang::panel.table_lang_tag') }}</th>
                                        <th scope="col" class="w-25">{{ __('FsLang::panel.table_lang_name') }}</th>
                                        <th scope="col" class="w-50">{{ __('FsLang::panel.table_content') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($optionalLanguages as $lang)
                                        <tr>
                                            <td>
                                                {{ $lang['langTag'] }}
                                                @if ($lang['langTag'] == $defaultLanguage)
                                                    <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('FsLang::panel.default_language') }}" data-bs-original-title="{{ __('FsLang::panel.default_language') }}" aria-label="{{ __('FsLang::panel.default_language') }}"></i>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $lang['langName'] }}
                                                @if ($lang['areaName'])
                                                    {{ '('.$lang['areaName'].')' }}
                                                @endif
                                            </td>
                                            <td><textarea class="form-control desc-input" name="descriptions[{{ $lang['langTag'] }}]" rows="3"></textarea></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!--button_confirm-->
                        <div class="text-center">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">{{ __('FsLang::panel.button_confirm') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
@endsection

@push('script')
    <script>
        // FsLang trans
        function trans(key, replace = {}) {
            // parent window
            if (parent) {
                return parent.trans(key, replace)
            }

            let translation = key.split('.').reduce((t, i) => {
                if (!t.hasOwnProperty(i)) {
                    return key;
                }
                return t[i];
            }, window.translations || []);

            for (var placeholder in replace) {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            }

            return translation;
        }

        // selectInputType
        $('.selectInputType li').click(function () {
            let inputName = $(this).data('name');

            $(this).parent().siblings('.showSelectTypeName').text($(this).text());
            $(this).parent().siblings('input').css('display', 'none');
            $(this)
                .parent()
                .siblings('.' + inputName)
                .removeAttr('style');
        });

        $('.name-lang-modal').on('show.bs.modal', function (e) {
            if ($(this).data('is_back')) {
                return;
            }

            let button = $(e.relatedTarget);
            var parent = button.data('parent');
            if (!parent) {
                return;
            }

            $(this).on('hide.bs.modal', function (e) {
                if (parent) {
                    let defaultName = $(this).find('.text-primary').closest('tr').find('.name-input').val();
                    if (!defaultName) {
                        defaultName = $(this)
                            .find('.name-input')
                            .filter(function () {
                                return $(this).val() != '';
                            })
                            .first()
                            .val();
                    }

                    $(parent).find('.name-button').text(defaultName);
                    $(parent).data('is_back', true);
                    $(parent).parent('form').find('input[name=update_name]').val(1);
                    $(parent).modal('show');
                }
            });
        });

        $('.description-lang-modal').on('show.bs.modal', function (e) {
            if ($(this).data('is_back')) {
                return;
            }

            $(this).on('hide.bs.modal', function (e) {
                $(this).data('is_back', false)
            });

            let button = $(e.relatedTarget);
            var parent = button.data('parent');
            if (!parent) {
                return;
            }

            $(this).on('hide.bs.modal', function (e) {
                if (parent) {
                    let defaultName = $(this).find('.text-primary').closest('tr').find('.desc-input').val();
                    if (!defaultName) {
                        defaultName = $(this)
                            .find('.desc-input')
                            .filter(function () {
                                return $(this).val() != '';
                            })
                            .first()
                            .val();
                    }

                    $(parent).find('.desc-button').text(defaultName);
                    $(parent).data('is_back', true);
                    $(parent).parent('form').find('input[name=update_description]').val(1);
                    $(parent).modal('show');
                }
            });
        });

        $('#editOperation').on('show.bs.modal', function (e) {
            if ($(this).data('is_back')) {
                return;
            }

            $(this).on('hide.bs.modal', function (e) {
                $(this).data('is_back', false)
            });

            let button = $(e.relatedTarget);
            let name = button.data('name');
            let description = button.data('description');
            let action = button.data('action');
            let params = button.data('params');
            let form = $(this).parents('form');

            form.attr('action', action);
            form.find('input[name=_method]').val(params ? 'put' : 'post');

            form.trigger('reset');
            form.find('.name-button').text(name || trans('panel.table_name')); //FsLang
            form.find('.desc-button').text(description || trans('panel.table_description')); //FsLang

            $(this).parent('form').trigger('reset');

            //reset default
            $('.showSelectTypeName').text(trans('panel.button_image_upload')); //FsLang
            $('.inputUrl').hide();
            $('.inputFile').show();

            if (! params) {
                return;
            }

            if (params.names) {
                params.names.map((name, index) => {
                    $(this)
                        .parent('form')
                        .find("input[name='names[" + name.lang_tag + "]'")
                        .val(name.lang_content);
                });
            }

            if (params.descriptions) {
                params.descriptions.map((description, index) => {
                    $(this)
                        .parent('form')
                        .find("textarea[name='descriptions[" + description.lang_tag + "]'")
                        .val(description.lang_content);
                });
            }

            if (params.image_file_url) {
                form.find('.inputUrl').val(params.image_file_url);
                form.find('.inputUrl').removeAttr('style');
                $('.showSelectTypeName').text(trans('panel.button_image_input')); //FsLang
                $('.inputFile').css('display', 'none');
            } else {
                form.find('.inputUrl').val('');
            }

            form
                .find('input:radio[name=is_enabled][value="' + params.is_enabled + '"]')
                .prop('checked', true)
                .click();
        });
    </script>
@endpush
