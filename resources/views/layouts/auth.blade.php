<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Система управления объектами') }}</title>

    <link rel="icon" href="https://st-ing.com/wp-content/uploads/2020/05/cropped-STI_logo_2020_512x512-32x32.png" sizes="32x32" />
    <link rel="icon" href="https://st-ing.com/wp-content/uploads/2020/05/cropped-STI_logo_2020_512x512-192x192.png" sizes="192x192" />
    <link rel="apple-touch-icon" href="https://st-ing.com/wp-content/uploads/2020/05/cropped-STI_logo_2020_512x512-180x180.png" />
    <meta name="msapplication-TileImage" content="https://st-ing.com/wp-content/uploads/2020/05/cropped-STI_logo_2020_512x512-270x270.png" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/plugins.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body id="kt_body" class="bg-body">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed">
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <a href="/" class="mb-12">
                    <img alt="Logo" src="https://st-ing.com/wp-content/themes/sti/img/logo.png" class="h-45px" />
                </a>

                <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    @if (session()->has('status'))
                        <div class="alert alert-dismissible bg-light-success border border-dashed border-success d-flex flex-column flex-sm-row p-5 mb-10">
                            <div class="d-flex flex-column pe-0 pe-sm-10">
                                <h5 class="mb-1">Успех</h5>
                                <span class="text-gray-700 fs-6">
                                    @if (session('status') === 'verification-link-sent')
                                        Новая ссылка для подтверждения была отправлена на указанный вами адрес электронной почты.
                                    @else
                                        {{ session('status') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            <div class="d-flex flex-center flex-column-auto p-10">
                <div class="d-flex align-items-center fw-bold fs-6">
                    <span class="text-muted px-2">Copyright &copy; <?php echo date('Y'); ?> STI OMS. All rights reserved.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/plugins.bundle.js') }}"></script>
    <script src="{{ asset('js/scripts.bundle.js') }}"></script>
    @stack('scripts')
</body>
</html>
