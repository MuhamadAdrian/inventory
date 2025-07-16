<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Scanner Barcode QuaggaJS' }}</title>
    @vite('resources/scss/style.scss')
    @stack('styles')
</head>
<body>
    <div class="">
        @yield('content')
    </div>
    @vite('resources/js/app.js')
    @stack('scripts')
</body>
</html>