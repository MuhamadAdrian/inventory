<header class="header header-sticky p-0 mb-4">
  <div class="container-fluid border-bottom px-4">
    <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px;">
      <svg class="icon icon-lg">
        <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-menu') }}"></use>
      </svg>
    </button>
    <ul class="header-nav ms-auto">
      {{-- <li class="nav-item"><a class="nav-link" href="#">
          <svg class="icon icon-lg">
            <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-bell') }}"></use>
          </svg></a></li> --}}
          @include('layouts.notification')
    </ul>
    <ul class="header-nav">
      <li class="nav-item py-1">
        <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
      </li>
      <li class="nav-item dropdown">
        <button class="btn btn-link nav-link py-2 px-2 d-flex align-items-center" type="button" aria-expanded="false" data-coreui-toggle="dropdown">
          <svg class="icon icon-lg theme-icon-active">
            <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-contrast') }}"></use>
          </svg>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
          <li>
            <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="light">
              <svg class="icon icon-lg me-3">
                <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-sun') }}"></use>
              </svg>Light
            </button>
          </li>
          <li>
            <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="dark">
              <svg class="icon icon-lg me-3">
                <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-moon') }}"></use>
              </svg>Dark
            </button>
          </li>
          <li>
            <button class="dropdown-item d-flex align-items-center active" type="button" data-coreui-theme-value="auto">
              <svg class="icon icon-lg me-3">
                <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-contrast') }}"></use>
              </svg>Auto
            </button>
          </li>
        </ul>
      </li>
      <li class="nav-item py-1">
        <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link py-0 pe-0" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <div class="d-flex gap-2">
            <div class="d-flex flex-column align-items-end">
              <p class="m-0">{{ Auth::user()->name }}</p>
              <p class="m-0" style="font-size: 12px">{{ Auth::user()->email }}</p>
            </div>
            <div class="avatar avatar-md">
              <img class="avatar-img" src="{{ asset('assets/images/avatar.webp') }}" alt="{{ Auth::user()->name }}">
            </div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end pt-0">
          <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">Account</div>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item"
            href="{{ route('logout') }}"
            onclick="event.preventDefault();
              document.getElementById('logout-form').submit();">
            <svg class="icon me-2">
              <use xlink:href="assets/coreui/icons/free.svg#cil-account-logout"></use>
            </svg>
            Logout
          </a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </div>
      </li>
    </ul>
  </div>
  <div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb my-0">
        <li class="breadcrumb-item active">
          <i class="bi bi-house-door-fill me-2"></i>
          @if(config('site.breadcrumbs') && count(config('site.breadcrumbs')) > 0)
          <a href="{{ route('dashboard.index') }}">Home</a>
          @else
          <span>Home</span>
          @endif
        </li>
        @foreach (config('site.breadcrumbs', []) as $breadcrumb)
        <li class="breadcrumb-item">
          @if($loop->last)
          <span>{{ $breadcrumb['name'] }}</span>
          @else
          <a href="{{ $breadcrumb['url'] ?? '#' }}">
            {{ $breadcrumb['name'] }}
          </a>
          @endif
          @endforeach
      </ol>
    </nav>
  </div>
</header>