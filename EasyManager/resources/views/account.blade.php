@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">ID</th>
                    <th scope="col">AID</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_type') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_phone') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_email') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_wallet_balance') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_users') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_last_login_time') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_wait_delete') }}</th>
                    <th scope="col">
                        {{ __('EasyManager::fresns.table_register_time') }}
                        @if (request('orderBy') === 'oldest')
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'latest']) }}" class="link-dark"><i class="bi bi-caret-up-fill"></i></a>
                        @else
                            <a href="{{ request()->fullUrlWithQuery(['orderBy' => 'oldest']) }}" class="link-dark"><i class="bi bi-caret-down-fill"></i></a>
                        @endif
                    </th>
                    <th scope="col">{{ __('EasyManager::fresns.table_status') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_options') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <th scope="row">{{ $account->id }}</th>
                        <td>{{ $account->aid }} {!! $account->is_verify ? '<i class="bi bi-patch-check-fill text-primary"></i>' : '' !!}</td>
                        <td>{{ $account->type }}</td>
                        <td>{{ $account->country_code ? '+'.$account->country_code : '' }} {{ $account->pure_phone }}</td>
                        <td>{{ $account->email }}</td>
                        <td>{{ $account->wallet->balance }}</td>
                        <td><a href="{{ route('easy-manager.user.index', ['accountId' => $account->id]) }}"><span class="badge rounded-pill text-bg-primary">{{ count($account->users) }}</span></a></td>
                        <td>{{ $account->last_login_at }}</td>
                        <td>
                            @if ($account->wait_delete)
                                {{ __('EasyManager::fresns.option_yes') }} <span class="badge bg-secondary">{{ $account->wait_delete_at }}</span>
                            @else
                                {{ __('EasyManager::fresns.option_no') }}
                            @endif
                        </td>
                        <td>{{ $account->created_at }}</td>
                        <td>{!! $account->is_enabled ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                        <td>
                            <form action="{{ route('easy-manager.account.update', $account) }}" method="post">
                                @csrf
                                @method('put')
                                @if ($account->is_enabled)
                                    <input type="hidden" name="is_enabled" value="0"/>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">{{ __('EasyManager::fresns.button_deactivate') }}</button>
                                @else
                                    <input type="hidden" name="is_enabled" value="1"/>
                                    <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('EasyManager::fresns.button_activate') }}</button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="py-3 d-flex justify-content-center">
        {{ $accounts->appends(request()->all())->links() }}
    </div>
@endsection
