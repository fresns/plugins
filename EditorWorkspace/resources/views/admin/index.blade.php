@extends('EditorWorkspace::commons.fresns')

@section('content')
    <div class="alert alert-light mx-4" role="alert">
        {{ __('EditorWorkspace::fresns.account_desc') }}
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">AID</th>
                    <th scope="col">{{ __('FsLang::panel.table_type') }}</th>
                    <th scope="col">{{ __('FsLang::panel.admin_add_form_account') }}</th>
                    <th scope="col">{{ __('EditorWorkspace::fresns.table_users') }}</th>
                    <th scope="col">{{ __('FsLang::panel.table_options') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <th scope="row">{{ $account->id }}</th>
                        <td>{{ $account->aid }} {!! $account->is_verify ? '<i class="bi bi-patch-check-fill text-primary"></i>' : '' !!}</td>
                        <td>{{ $account->type }}</td>
                        <td>
                            <span class="badge bg-light text-dark"><i class="bi bi-envelope"></i>
                                @if ($account->email)
                                    {{ $account->secret_email }}
                                @else
                                    None
                                @endif
                            </span>
                            <span class="badge bg-light text-dark"><i class="bi bi-phone"></i>
                                @if ($account->pure_phone)
                                    +{{ $account->country_code }} {{ $account->secret_pure_phone }}
                                @else
                                    None
                                @endif
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('editor-workspace.admin.users', ['accountId' => $account->id]) }}"><span class="badge rounded-pill {{ count($account->users) ? 'text-bg-primary' : 'text-bg-light fw-normal' }}">{{ count($account->users) }}</span></a>
                            <button class="btn btn-outline-success btn-sm ms-3" type="button" data-bs-toggle="modal" data-bs-target="#generateUser" data-account-id="{{ $account->id }}" data-account-aid="{{ $account->aid }}">{{ __('FsLang::panel.button_add') }}</button>
                        </td>
                        <td>
                            <button class="btn btn-link btn-sm text-danger" type="button" data-bs-toggle="modal" data-bs-target="#removeAccount-{{ $account->id }}">{{ __('FsLang::panel.button_cancel') }}</button>

                            <div class="modal fade" id="removeAccount-{{ $account->id }}" tabindex="-1" aria-labelledby="removeAccount-{{ $account->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('FsLang::panel.button_cancel') }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-danger">
                                            {{ __('FsLang::panel.button_confirm') }}: {{ __('FsLang::panel.button_cancel') }}?
                                        </div>
                                        <div class="modal-footer">
                                            <form action="{{ route('editor-workspace.admin.account.remove') }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <input type="hidden" name="accountId" value="{{ $account->id }}"/>
                                                <button type="submit" class="btn btn-danger">{{ __('FsLang::panel.button_confirm') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccount" tabindex="-1" aria-labelledby="addAccount" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('FsLang::panel.button_add') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-add-tab" data-bs-toggle="tab" data-bs-target="#nav-add" type="button" role="tab" aria-controls="nav-add" aria-selected="true">{{ __('EditorWorkspace::fresns.account_add') }}</button>
                            <button class="nav-link" id="nav-generate-tab" data-bs-toggle="tab" data-bs-target="#nav-generate" type="button" role="tab" aria-controls="nav-generate" aria-selected="false">{{ __('EditorWorkspace::fresns.account_generate') }}</button>
                        </div>
                    </nav>

                    <div class="tab-content" id="nav-tabContent">
                        {{-- add --}}
                        <div class="tab-pane fade show active" id="nav-add" role="tabpanel" aria-labelledby="nav-add-tab" tabindex="0">
                            <form action="{{ route('editor-workspace.admin.account.add') }}" class="mt-3 mb-5" method="post">
                                @csrf
                                <div class="input-group">
                                    <span class="input-group-text">{{ __('FsLang::panel.admin_add_form_account') }}</span>
                                    <input type="text" name="accountName" class="form-control" placeholder="{{ __('FsLang::panel.admin_add_form_account_placeholder') }}" required>
                                    <button class="btn btn-outline-secondary" type="submit" id="folderInstall-button">{{ __('FsLang::panel.admin_add_form_account_btn') }}</button>
                                </div>
                                <div class="form-text"><i class="bi bi-info-circle"></i> {{ __('FsLang::panel.admin_add_form_account_desc') }}</div>
                            </form>
                        </div>

                        {{-- generate --}}
                        <div class="tab-pane fade" id="nav-generate" role="tabpanel" aria-labelledby="nav-generate-tab" tabindex="0">
                            <form action="{{ route('editor-workspace.admin.account.generate') }}" class="mt-3 mb-3" id="accordionAccount" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Account Type</span>
                                    <div class="form-control">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="account_email" value="email" data-bs-toggle="collapse" data-bs-target=".account_email:not(.show)" aria-expanded="true" aria-controls="account_email" checked>
                                            <label class="form-check-label" for="account_email">Email</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="type" id="account_phone" value="phone" data-bs-toggle="collapse" data-bs-target=".account_phone:not(.show)" aria-expanded="false" aria-controls="account_phone">
                                            <label class="form-check-label" for="account_phone">Phone Number</label>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="collapse account_email show" aria-labelledby="account_email" data-bs-parent="#accordionAccount">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text text-danger">* Email</span>
                                            <input type="email" name="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="collapse account_phone" aria-labelledby="account_phone" data-bs-parent="#accordionAccount">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text text-danger">* Phone</span>
                                            <select class="form-select" name="country_code">
                                                @foreach ($countryCode['send_sms_supported_codes'] as $code)
                                                    <option @if ($countryCode['send_sms_default_code'] == $code) selected @endif value="{{ $code }}">+{{ $code }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" name="phone" class="form-control w-50">
                                        </div>
                                    </div>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text">Username</span>
                                    <input type="text" name="username" class="form-control">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger">* Nickname</span>
                                    <input type="text" name="nickname" class="form-control" required>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Avatar</span>
                                    <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ __('FsLang::panel.button_image_upload') }}</button>
                                    <ul class="dropdown-menu selectInputType">
                                        <li data-name="inputFile"><a class="dropdown-item" href="#">{{ __('FsLang::panel.button_image_upload') }}</a></li>
                                        <li data-name="inputUrl"><a class="dropdown-item" href="#">{{ __('FsLang::panel.button_image_input') }}</a></li>
                                    </ul>
                                    <input type="file" class="form-control inputFile" name="avatar_file">
                                    <input type="text" class="form-control inputUrl" name="avatar_file_url" value="" style="display:none;">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Gender</span>
                                    <select class="form-select" name="gender">
                                        <option value="1">Unknown</option>
                                        <option value="2">Male</option>
                                        <option value="3">Female</option>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Bio</span>
                                    <textarea class="form-control" name="bio"></textarea>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Password</span>
                                    <input type="text" name="password" class="form-control">
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_confirm') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate User Modal -->
    <div class="modal fade" id="generateUser" tabindex="-1" aria-labelledby="generateUser" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('FsLang::panel.button_add') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('editor-workspace.admin.user.generate') }}" class="mt-3 mb-3" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group mb-3">
                            <span class="input-group-text">Account ID</span>
                            <input type="number" class="form-control" name="accountId" disabled>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Account AID</span>
                            <input type="text" class="form-control" name="accountAid" disabled>
                        </div>
                        <input type="hidden" name="aid" value=""/>

                        <div class="input-group mb-3">
                            <span class="input-group-text">Username</span>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text text-danger">* Nickname</span>
                            <input type="text" name="nickname" class="form-control" required>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Avatar</span>
                            <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ __('FsLang::panel.button_image_upload') }}</button>
                            <ul class="dropdown-menu selectInputType">
                                <li data-name="inputFile"><a class="dropdown-item" href="#">{{ __('FsLang::panel.button_image_upload') }}</a></li>
                                <li data-name="inputUrl"><a class="dropdown-item" href="#">{{ __('FsLang::panel.button_image_input') }}</a></li>
                            </ul>
                            <input type="file" class="form-control inputFile" name="avatar_file">
                            <input type="text" class="form-control inputUrl" name="avatar_file_url" value="" style="display:none;">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Gender</span>
                            <select class="form-select" name="gender">
                                <option value="1">Unknown</option>
                                <option value="2">Male</option>
                                <option value="3">Female</option>
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Bio</span>
                            <textarea class="form-control" name="bio"></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_confirm') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // selectInputType
        $('.selectInputType li').click(function () {
            let showClass = $(this).data('name');
            let hideClass = 'inputUrl';
            if (showClass == 'inputUrl') {
                hideClass = 'inputFile';
            }

            $(this).parent().siblings('.showSelectTypeName').text($(this).text());
            $(this).parent().siblings('.' + hideClass).hide();
            $(this).parent().siblings('.' + showClass).show();
        });

        $('#generateUser').on('show.bs.modal', function (e) {
            if ($(this).data('is_back')) {
                return;
            }

            let button = $(e.relatedTarget);
            let accountId = button.data('accountId');
            let accountAid = button.data('accountAid');

            $(this).parent('form').trigger('reset');

            $(this).find('input[name=accountId]').val(accountId);
            $(this).find('input[name=accountAid]').val(accountAid);
            $(this).find('input[name=aid]').val(accountAid);
        });
    </script>
@endpush
