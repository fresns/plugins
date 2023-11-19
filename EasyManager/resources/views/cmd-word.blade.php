@extends('EasyManager::commons.fresns')

@section('content')
    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
            <thead>
                <tr class="table-info align-middle">
                    <th scope="col">fskey</th>
                    <th scope="col">{{ __('EasyManager::fresns.cmd_word') }}</th>
                    <th scope="col">Provider</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cmdWords as $word)
                    <tr>
                        <th scope="row">{{ $word['fskey'] }}</th>
                        <td>{{ $word['cmd_word'] }}</td>
                        <td>{{ $word['provider'][0] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
