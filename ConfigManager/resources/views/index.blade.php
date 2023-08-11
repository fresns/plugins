@extends('ConfigManager::commons.fresns')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col">
                <form action="{{ route('config-manager.index') }}">
                    <div class="input-group">
                        <span class="input-group-text">Key</span>
                        <input type="text" class="form-control" placeholder="Key" name="key" value="{{ request('key') }}">
                        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="col">
                <form action="{{ route('config-manager.index') }}">
                    <div class="input-group">
                        <span class="input-group-text">Tag</span>
                        <input type="text" class="form-control" placeholder="Tag" name="tag" value="{{ request('tag') }}">
                        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info align-middle">
                    <th scope="col">Key</th>
                    <th scope="col" class="w-25">Value</th>
                    <th scope="col">Type</th>
                    <th scope="col">Tag</th>
                    <th scope="col">{{ __('ConfigManager::fresns.multilingual') }}</th>
                    <th scope="col">{{ __('ConfigManager::fresns.api') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($configs as $config)
                    <tr>
                        <th scope="row" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ __('ConfigManager::fresns.created_time').': '.$config->created_at }}">{{ $config->item_key }}</th>
                        <td>
                            @if (in_array($config->item_type, ['array', 'object', 'plugins']))
                                {{ Str::limit(strip_tags(json_encode($config->item_value)), 50) }}
                            @else
                                {{ Str::limit(strip_tags($config->item_value), 50) }}
                            @endif
                        </td>
                        <td>{{ $config->item_type }}</td>
                        <td>{{ $config->item_tag }}</td>
                        <td>{!! $config->is_multilingual ? '<i class="bi bi-check-lg text-primary"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                        <td>{!! $config->is_api ? '<i class="bi bi-check-lg text-primary"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                        <td class="d-flex">
                            <button type="button" class="btn btn-outline-primary btn-sm edit-config" data-info="{{ json_encode($config) }}" data-optional-languages="{{ json_encode($optionalLanguages) }}">
                                {{ __('FsLang::panel.button_edit') }}
                            </button>

                            @if ($config->is_custom)
                                <button type="button" class="btn btn-link link-danger ms-2 fresns-link fs-7 del-config-btn" data-info="{{ $config->toJson() }}" data-target="#deleteConfirmModal">
                                    {{ __('FsLang::panel.button_delete') }}
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="py-3 d-flex justify-content-center">
        {{ $configs->withQueryString()->links() }}
    </div>

    <form class="ajax-form" action="{{ route('api.config-manager.index') }}" method="post">
        @csrf
        @method('post')
        <input type="hidden" class="form-control" name="id" value="">
        <input type="hidden" class="form-control" name="update_value" value="">

        {{-- modal --}}
        <div class="modal fade" id="editModal" aria-hidden="true" aria-labelledby="editModalLabel" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editModalLabel">Config Manager</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="key" value="" required>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Type</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="type" required>
                                    <option value="string">String</option>
                                    <option value="number">Number</option>
                                    <option value="boolean">Boolean</option>
                                    <option value="array">Array</option>
                                    <option value="object">Object</option>
                                    <option value="file">File</option>
                                    <option value="plugin">Plugin</option>
                                    <option value="plugins">Plugins</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Tag</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="tag" value="" required>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">{{ __('ConfigManager::fresns.multilingual') }}</label>
                            <div class="col-sm-9 pt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="multilingual" id="multilingual_false" value="0" checked data-bs-toggle="collapse" data-bs-target=".value-single:not(.show)">
                                    <label class="form-check-label" for="multilingual_false">{{ __('FsLang::panel.option_no') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="multilingual" id="multilingual_true" value="1" data-bs-toggle="collapse" data-bs-target=".value-multilingual:not(.show)">
                                    <label class="form-check-label" for="multilingual_true">{{ __('FsLang::panel.option_yes') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="value">
                            <div class="mb-3 row collapse value-single show" data-bs-parent=".value">
                                <label class="col-sm-3 col-form-label">Value</label>
                                <div class="col-sm-9">
                                    <textarea type="text" class="form-control" name="value" value="" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="mb-3 row collapse value-multilingual" data-bs-parent=".value">
                                <label class="col-sm-3 col-form-label">Value</label>
                                <div class="col-sm-9">
                                    <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start value-button" data-parent="#editModal" data-bs-toggle="modal" data-bs-target="#valueMultilingualModal">{{ __('FsLang::panel.button_edit') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">{{ __('ConfigManager::fresns.api') }}</label>
                            <div class="col-sm-9 pt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="api" id="api_false" value="0" checked>
                                    <label class="form-check-label" for="api_false">{{ __('FsLang::panel.option_no') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="api" id="api_true" value="1">
                                    <label class="form-check-label" for="api_true">{{ __('FsLang::panel.option_yes') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-9"><button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Multilingual modal --}}
        <div class="modal fade value-lang-modal" id="valueMultilingualModal" tabindex="-1" aria-labelledby="valueMultilingualModal" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('FsLang::panel.table_name') }}</h5>
                        <button type="button" class="btn-close" aria-label="Close" data-bs-toggle="modal" data-bs-target="#editModal"></button>
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
                                            <td><input type="text" name="values[{{ $lang['langTag'] }}]" class="form-control value-input" value=""></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!--button_confirm-->
                        <div class="text-center">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editModal">{{ __('FsLang::panel.button_confirm') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade value-lang-modal" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('FsLang::panel.delete_desc') }}</h5>
                    <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('config-manager.index') }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" class="form-control" name="item_key" value="">
                    <input type="hidden" class="form-control" name="item_tag" value="">

                    <div class="modal-body">
                        <p>Key: <span class="config-item_key text-danger"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">{{ __('FsLang::panel.button_confirm_delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            $('.del-config-btn').click(function () {
                let data = $(this).data('info')
                let target = $(this).data('target')

                $('.config-item_key').text(data['item_key'])

                $(target).find('[name="item_key"]').val(data['item_key'])
                $(target).find('[name="item_tag"]').val(data['item_tag'])

                $(target).modal('show')
            });

            $(document).on('show.bs.modal', '.value-lang-modal', function (e) {
                if ($(this).data('is_back')) {
                    return;
                }

                let button = $(e.relatedTarget);
                var parent = button.data('parent');
                if (!parent) {
                    return;
                }

                var $this = $(this);
                $(document).on('hide.bs.modal', '.value-lang-modal', function (e) {
                    if (parent) {
                        let defaultName = $(this).find('.text-primary').closest('tr').find('.value-input').val();
                        if (!defaultName) {
                            defaultName = $(this)
                                .find('.value-input')
                                .filter(function () {
                                    return $(this).val() != '';
                                })
                                .first()
                                .val();
                        }

                        $(parent).find('[name="value"]').val('')
                        $(parent).find('.value-button').text(defaultName);

                        $(parent).data('is_back', true);
                        $this.parent('form').find('[name=update_value]').val(1);
                        $(parent).modal('show');
                    }

                    let button = $(e.relatedTarget);
                    var parent = button.data('parent');
                    if (!parent) {
                        return;
                    }

                    var $this = $(this);
                    $(this).on('hide.bs.modal', function (e) {
                        if (parent) {
                            let defaultName = $(this).find('.text-primary').closest('tr').find('.value-input').val();
                            if (!defaultName) {
                                defaultName = $(this)
                                    .find('.value-input')
                                    .filter(function () {
                                        return $(this).val() != '';
                                    })
                                    .first()
                                    .val();
                            }

                            $(parent).find('[name="value"]').val('')
                            $(parent).find('.value-button').text(defaultName);

                            $(parent).data('is_back', true);
                            $this.parent('form').find('[name=update_value]').val(1);
                            $(parent).modal('show');
                        }
                    });
                });
            });

            $(document).on('click', '.edit-config', function() {
                let data = $(this).data('info')
                let optionalLanguages = $(this).data('optional-languages')
                let form = $('#editModal').parent('form')

                if (!data) {
                    data = {}
                    data['id'] = ''
                    data['update_value'] = ''
                    data['item_key'] = ''
                    data['item_type'] = 'string'
                    data['item_tag'] = ''
                    data['is_multilingual'] = 0
                    data['is_api'] = 0
                    data['languages'] = []

                    $('.value-button').text("{{ __('FsLang::panel.button_edit') }}")

                    form.find('[name="key"]').removeAttr('readonly').removeClass('bg-secondary-subtle');
                } else {
                    form.find('[name="_method"]').val('put')

                    $('.value-button').text("{{ __('FsLang::panel.button_edit') }}")

                    form.find('[name="key"]').prop('readonly', true).addClass('bg-secondary-subtle');
                }

                let value = data['item_value']
                if (['object', 'array'].includes(data['item_type'])) {
                    try {
                        value = JSON.stringify(data['item_value'])
                    } catch (e) {
                        value = ''
                        console.error(e)
                    }
                }

                form.parent('form').attr('action', "{{ route('api.config-manager.index') }}")

                form.find('[name="id"]').val(data['id'])
                form.find('[name="update_value"]').val(data['update_value'])
                form.find('[name="key"]').val(data['item_key'])
                form.find('[name="type"]').val(data['item_type'])
                form.find('[name="value"]').val(value)
                form.find('[name="tag"]').val(data['item_tag'])

                // Multilingual Config
                form.find('[name="multilingual"]').removeAttr('checked')
                form.find('[name="api"]').removeAttr('checked')

                form.find(`[name="multilingual"][value="${data['is_multilingual']}"]`).prop('checked', true)
                form.find(`[name="api"][value="${data['is_api']}"]`).prop('checked', true)

                $('form button[type="submit"]').prop('disabled', false);

                if (data['is_multilingual']) {
                    $('.value-single').removeClass('show')
                    $('.value-multilingual').addClass('show')
                } else {
                    $('.value-single').addClass('show')
                    $('.value-multilingual').removeClass('show')
                }

                if (data['languages'].length) {
                    data['languages'].map((item) => {
                        form.find("[name='values[" + item.lang_tag + "]'").val(item.lang_content);
                    });
                } else {
                    optionalLanguages.map((item) => {
                        form.find("[name='values[" + item['langTag'] + "]'").val('');
                    });
                }

                $('#editModal').modal('show')
            });
        })
    </script>
@endpush
