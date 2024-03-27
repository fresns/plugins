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
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info align-middle">
                    <th scope="col">Key</th>
                    <th scope="col" class="w-25">Value</th>
                    <th scope="col">Type</th>
                    <th scope="col">{{ __('ConfigManager::fresns.multilingual') }}</th>
                    <th scope="col">{{ __('ConfigManager::fresns.api') }}</th>
                    <th scope="col">{{ __('ConfigManager::fresns.created_time') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_options') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($configs as $config)
                    <tr>
                        <th scope="row">{{ $config->item_key }}</th>
                        <td>
                            @if (in_array($config->item_type, ['array', 'object', 'plugins']))
                                {{ Str::limit(strip_tags(json_encode($config->item_value)), 50) }}
                            @elseif ($config->item_type == 'boolean')
                                {{ $config->item_value ? 'true' : 'false' }}
                            @else
                                {{ Str::limit(strip_tags($config->item_value), 50) }}
                            @endif
                        </td>
                        <td>{{ $config->item_type }}</td>
                        <td>{!! $config->is_multilingual ? '<i class="bi bi-check-lg text-primary"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                        <td>{!! $config->is_api ? '<i class="bi bi-check-lg text-primary"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                        <td>{{ $config->created_at }}</td>
                        <td class="d-flex">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-info="{{ $config->toJson() }}">
                                {{ __('FsLang::panel.button_edit') }}
                            </button>

                            @if ($config->is_custom)
                                <button type="button" class="btn btn-link link-danger ms-3 fresns-link fs-7" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-item-key="{{ $config->item_key }}">
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

    <form action="{{ route('config-manager.update') }}" method="post">
        @csrf
        @method('post')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="editModalLabel">Config Manager</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="id" value="">
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="item_key" value="" required>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Type</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="item_type" required>
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
                            <label class="col-sm-3 col-form-label">{{ __('ConfigManager::fresns.multilingual') }}</label>
                            <div class="col-sm-9 pt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_multilingual" id="multilingual_false" value="0" checked data-bs-toggle="collapse" data-bs-target=".value-single:not(.show)">
                                    <label class="form-check-label" for="multilingual_false">{{ __('FsLang::panel.option_no') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_multilingual" id="multilingual_true" value="1" data-bs-toggle="collapse" data-bs-target=".value-multilingual:not(.show)">
                                    <label class="form-check-label" for="multilingual_true">{{ __('FsLang::panel.option_yes') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="value">
                            <div class="mb-3 row collapse value-single show" data-bs-parent=".value">
                                <label class="col-sm-3 col-form-label">Value</label>
                                <div class="col-sm-9">
                                    <textarea type="text" class="form-control" name="item_value" rows="5"></textarea>
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
                                    <input class="form-check-input" type="radio" name="is_api" id="api_false" value="0" checked>
                                    <label class="form-check-label" for="api_false">{{ __('FsLang::panel.option_no') }}</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_api" id="api_true" value="1">
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
        <div class="modal fade" id="valueMultilingualModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="valueMultilingualModal" aria-hidden="true">
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
                                            <td><textarea class="form-control" name="langValues[{{ $lang['langTag']}}]" rows="3"></textarea></td>
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

    {{-- delete confirm modal --}}
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('FsLang::panel.delete_desc') }}</h5>
                    <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('config-manager.delete') }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" class="form-control" name="item_key" value="">

                    <div class="modal-body">
                        <p>Item Key: <span class="config-item_key text-danger"></span></p>
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
        $('#deleteConfirmModal').on('show.bs.modal', function (e) {
            let button = $(e.relatedTarget);
            let itemKey = button.data('item-key');

            $('.config-item_key').text(itemKey);

            $(this).find('[name="item_key"]').val(itemKey);
        });

        $('#editModal').on('show.bs.modal', function (e) {
            if ($(this).data('is_back')) {
                return;
            }

            let button = $(e.relatedTarget);
            let info = button.data('info');

            $(this).find('form').trigger('reset');

            $(this).find('input[name=id]').val('');
            $(this).find('input[name=item_key]').val('').prop('readonly', false);
            $(this).find('select[name=item_type]').val('string');
            $(this).find('textarea').val('');
            $(this).find('input:radio[name=is_multilingual][value="0"]').prop('checked', true);
            $(this).find('input:radio[name=is_api][value="0"]').prop('checked', true);

            $('.value-single').addClass('show');
            $('.value-multilingual').removeClass('show');

            $('#valueMultilingualModal').find('textarea').val('');

            if (!info) {
                return;
            }

            let id = info.id;
            let item_key = info.item_key;
            let item_type = info.item_type;
            let item_value = info.item_value;
            let is_multilingual = info.is_multilingual;
            let is_api = info.is_api;

            if (item_type == 'array' || item_type == 'plugins') {
                item_value = JSON.stringify(info.item_value);
            }

            if (!is_multilingual && item_type == 'object') {
                item_value = JSON.stringify(info.item_value);
            }

            console.log('info', id, item_key, item_type, item_value, is_multilingual, is_api);

            $(this).find('input[name=id]').val(id);
            $(this).find('input[name="item_key"]').val(item_key).prop('readonly', true);
            $(this).find('select[name=item_type]').val(item_type);
            $(this).find('textarea').val(item_value);
            $(this).find('input:radio[name=is_multilingual][value="' + is_multilingual + '"]').prop('checked', true);
            $(this).find('input:radio[name=is_api][value="' + is_api + '"]').prop('checked', true);

            if (is_multilingual) {
                $('.value-single').removeClass('show');
                $('.value-multilingual').addClass('show');
            }

            if (is_multilingual && item_value) {
                Object.entries(item_value).forEach(([langTag, value]) => {
                    $('#valueMultilingualModal').find("textarea[name='langValues[" + langTag + "]']").val(value);
                });
            }
        });

        $('#valueMultilingualModal').on('show.bs.modal', function (e) {
            if ($(this).data('is_back')) {
                return;
            }

            let button = $(e.relatedTarget);
            var parent = button.data('parent');

            if (!parent) {
                return;
            }

            var $this = $(this);
            $(this).on('hide.bs.modal', function (e) {
                if (parent) {
                    $(parent).data('is_back', true);
                }
            });
        });

        $('#editModal').on('hide.bs.modal', function (e) {
            $(this).data('is_back', false);
        });
    </script>
@endpush
