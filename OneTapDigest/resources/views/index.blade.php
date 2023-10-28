@extends('OneTapDigest::layouts.master')

@section('content')
    <div aria-live="polite" aria-atomic="true" class="position-fixed top-50 start-50 translate-middle @if ($code == 10000) d-none @endif" id="tip">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <img src="/static/images/icon.png" width="20px" height="20px" class="rounded me-2" alt="Fresns">
                <strong class="me-auto">Fresns</strong>
                <small id="code">{{ $code ? $code : '' }}</small>
            </div>
            <div class="toast-body" id="message">
                @if ($code == 0) {{ $fsLang['setting'].': ' }} @endif {{ $message }}
            </div>
        </div>
    </div>
    @if ($code == 10000)
        <div class="my-5 text-center" id="update">
            <div class="text-secondary mb-4">{{ $fsLang['settingAlready'] }}</div>

            <form action="{{ route('one-tap-digest.update') }}" method="post">
                @csrf
                @method('put')

                <input type="hidden" name="langTag" value="{{ $langTag }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="primaryId" value="{{ $primaryId }}">
                <input type="hidden" name="authUlid" value="{{ $authUlid }}">

                <button type="submit" class="btn btn-danger py-2 px-5">{{ $fsLang['recall'] }}</button>
            </form>
        </div>
    @endif
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();

                // Spinner
                var btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true);

                if (0 === btn.children('.spinner-border').length) {
                    btn.prepend('<span class="spinner-border spinner-border-sm mg-r-5" role="status" aria-hidden="true"></span> ');
                }
                btn.children('.spinner-border').removeClass('d-none');

                // AJAX
                $.ajax({
                    type: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log(response);

                        $('#code').text(response.code);
                        $('#message').text(response.message);

                        $('#tip').removeClass('d-none');
                        $('#update').addClass('d-none');
                    },
                    error: function(error) {
                        console.log(error);
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        btn.children('.spinner-border').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush
