@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info">
                    <th scope="col">Account ID</th>
                    <th scope="col">Connect Platform</th>
                    <th scope="col">Connect Account</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_username') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_nickname') }}</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_avatar') }}</th>
                    <th scope="col">Plugin Fskey</th>
                    <th scope="col">{{ __('EasyManager::fresns.table_status') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($connects as $connect)
                    <tr>
                        <th scope="row">{{ $connect->account_id }}</th>
                        <td>{{ $connect->connect_platform_id }}</td>
                        <td>{{ $connect->connect_account_id }}</td>
                        <td>{{ $connect->connect_username }}</td>
                        <td>{{ $connect->connect_nickname }}</td>
                        <td>
                            @if ($connect->connect_avatar)
                                <img src="{{ $connect->connect_avatar }}" class="rounded-circle" width="20" height="20">
                            @endif
                        </td>
                        <td><a href="https://marketplace.fresns.com/detail/{{ $connect->app_fskey }}" target="_blank">{{ $connect->app_fskey }}</a></td>
                        <td>{!! $connect->is_enabled ? '<i class="bi bi-check-lg text-success"></i>' : '<i class="bi bi-dash-lg text-secondary"></i>' !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
