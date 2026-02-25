<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicon icon-->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.ico') }}" />
    <meta property="og:image" content="{{ asset('assets/images/logos/railsnap-logo.png') }}" />

    <!-- Core Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons/tabler-icons.css') }}">

    <title>{{ config('app.name') }} - @yield('title')</title>

    @stack('style')
</head>

<body class="link-sidebar">
    <div id="main-wrapper">
        @include('layouts.sidebar')
        <div class="page-wrapper">
            @include('layouts.navbar')
            <div class="body-wrapper">
                @yield('content')
            </div>
        </div>
    </div>
    <div class="dark-transparent sidebartoggler"></div>
    
    <!-- Import Js Files -->
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
    <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
    <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme/sidebarmenu.js') }}"></script>

    @stack('scripts')
</body>

</html>
