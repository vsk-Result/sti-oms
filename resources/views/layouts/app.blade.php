<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Система управления объектами') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/plugins.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled">
<div class="d-flex flex-column flex-root">
    <div class="page d-flex flex-row flex-column-fluid">

        @include('headers.main')

        <div id="kt_content_container" class="d-flex flex-column-fluid align-items-stretch container-fluid">

            @include('sidebars.main')

            <div class="wrapper d-flex flex-column flex-row-fluid mt-5 mt-lg-10" id="kt_wrapper">
                <div class="content flex-column-fluid" id="kt_content">

                    @include('toolbars.main')

                    @yield('content')
                </div>

                @include('footers.main')
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('js/plugins.bundle.js') }}"></script>
<script src="{{ asset('js/scripts.bundle.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')

</body>
</html>
