<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GitHub Sign in</title>
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/static/css/fresns-panel.css">
</head>

<body>
    <main class="container-lg p-0 p-lg-3">
        <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
            <!-- top -->
            <div class="row mb-2">
                <div class="col-8">
                    <h3>GitHub Sign in <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
                    <p class="text-secondary">Fresns official plugin for GitHub Sign in</p>
                </div>
                <div class="col-4">
                    <div class="input-group mt-2 mb-4 justify-content-lg-end px-1" role="group">
                        <a class="btn btn-outline-secondary" href="https://github.com/fresns/plugins/tree/3.x/GitHubSignIn" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
                    </div>
                </div>
            </div>
            <!-- Menu -->
            <div class="mb-3">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" type="button" role="tab">Settings</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" role="tab" href="{{ route('panel.account.index') }}" target="_blank">Add Account Connect <i class="bi bi-box-arrow-up-right"></i></a>
                    </li>
                </ul>
            </div>

            <!-- Setting -->
            <div class="tab-content pt-3">
                <form action="{{ route('github-signin.admin.update') }}" method="post">
                    @csrf
                    @method('put')

                    <div class="row mb-3">
                        <label class="col-lg-3 col-form-label text-lg-end">Client ID:</label>
                        <div class="col-lg-9"><input type="text" class="form-control" name="clientId" value="{{ $clientId }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-lg-3 col-form-label text-lg-end">Client Secret:</label>
                        <div class="col-lg-9"><input type="text" class="form-control" name="clientSecret" value="{{ $clientSecret }}"></div>
                    </div>
                    <div class="row mb-4">
                        <label class="col-lg-3 col-form-label text-lg-end">Authorization callback URL:</label>
                        <div class="col-lg-9"><input type="text" class="form-control bg-light" value="{{ config('app.url').'/github-signin/auth-callback' }}" readonly></div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-3"></div>
                        <div class="col-lg-9">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- end -->
        </div>
    </main>

    <footer>
        <div class="copyright text-center">
            <p class="mt-5 mb-5 text-muted">&copy; <span class="copyright-year"></span> Fresns</p>
        </div>
    </footer>

    <div class="fresns-tips">
        @include('FsView::commons.tips')
    </div>

    <script src="/static/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/jquery.min.js"></script>

    <script>
        // copyright-year
        var yearElement = document.querySelector('.copyright-year');
        var currentDate = new Date();
        var currentYear = currentDate.getFullYear();
        if (yearElement) {
            yearElement.textContent = currentYear;
        }

        // submit
        $(document).on('submit', 'form', function () {
            var btn = $(this).find('button[type="submit"]');

            btn.find('i').remove();

            btn.prop('disabled', true);
            if (btn.children('.spinner-border').length == 0) {
                btn.prepend('<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> ');
            }
            btn.children('.spinner-border').removeClass('d-none');
        });

        // set timeout toast hide
        const setTimeoutToastHide = () => {
            $('.toast.show').each((k, v) => {
                setTimeout(function () {
                    $(v).hide();
                }, 1500);
            });
        };
        setTimeoutToastHide();
    </script>
</body>
</html>
