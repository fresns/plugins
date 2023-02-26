@extends('EasyManager::commons.fresns')

@section('content')
    <div class="mx-4 pt-3 pb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('easy-manager.home') }}">{{ __('EasyManager::fresns.name') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('easy-manager.group.index') }}">{{ __('EasyManager::fresns.group') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('FsLang::panel.button_config_permission') }}<span class="badge bg-secondary ms-2">{{ $group->getLangName($defaultLanguage) }}</span></li>
            </ol>
        </nav>
    </div>

    <!--customize_config-->
    <form action="{{ route('easy-manager.group.update.permissions', $group->id) }}" method="post">
        @csrf
        @method('put')
        <!--options-->
        <div class="table-responsive">
            <table class="table table-hover align-middle text-nowrap">
                <thead>
                    <tr class="table-info">
                        <th scope="col" class="w-25">{{ __('FsLang::panel.role_perm_table_name') }}</th>
                        <th scope="col">{{ __('FsLang::panel.role_perm_table_value') }}</th>
                        <th scope="col" style="width:6rem;">{{ __('FsLang::panel.table_options') }}</th>
                    </tr>
                </thead>
                <tbody id="customPermBox">
                    @foreach ($permissions as $permission)
                        <tr>
                            <td><input type="text" class="form-control bg-light" name="editPermissions[permKey][]" value="{{ $permission['permKey'] }}" readonly></td>
                            <td><input type="text" class="form-control {{ $permission['isCustom'] ? '' : 'bg-light' }}" name="editPermissions[permValue][]" value="{{ $permission['permValue'] }}" {{ $permission['isCustom'] ? '' : 'readonly' }}></td>
                            <td>
                                @if ($permission['isCustom'])
                                    <button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7 delete-custom-perm">{{ __('FsLang::panel.button_delete') }}</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr id="addCustomPermTr">
                        <td colspan="3" class="text-center">
                            <button class="btn btn-outline-success btn-sm px-3" id="addCustomPerm" type="button">
                                <i class="bi bi-plus-circle-dotted"></i> {{ __('FsLang::panel.button_add') }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!--button_save-->
        <div class="text-center pt-3 pb-5">
            <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
        </div>
        <!--options end-->
    </form>

    <template id="customPerm">
        <tr>
            <td><input type="text" class="form-control" required name="editPermissions[permKey][]"></td>
            <td><input type="text" class="form-control" required name="editPermissions[permValue][]"></td>
            <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7 delete-custom-perm">{{ __('FsLang::panel.button_delete') }}</button></td>
        </tr>
    </template>
@endsection

@push('script')
    <script>
        $('#addCustomPerm').click(function () {
            let template = $('#customPerm').clone();
            $('#addCustomPermTr').before(template.contents());
        });

        $(document).on('click', '.delete-custom-perm', function () {
            $(this).closest('tr').remove();
        });
    </script>
@endpush
