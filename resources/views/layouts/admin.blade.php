<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CoreUI Admin')</title>

    @vite('resources/scss/style.scss')
</head>
<body>
    @include('layouts.sidebar')
    <div class="wrapper d-flex flex-column min-vh-100">
      @include('layouts.header')
      <div class="body flex-grow-1">
        @yield('content')
      </div>
      @include('layouts.footer')
    </div>

    @vite('resources/js/app.js')

    {{-- If you have specific scripts for pages, you can yield them here --}}
    @stack('scripts')
</body>
</html>