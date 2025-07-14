    <div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
      <div class="sidebar-header border-bottom">
        <div class="sidebar-brand">
          Alisha {{ ucfirst(auth()->user()->getRoleNames()->first())}}
        </div>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
      </div>
      <ul class="sidebar-nav" style="font-size: 14px" data-coreui="navigation" data-simplebar>
        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">
            <svg class="nav-icon">
              {{-- Use Laravel's asset() helper to point to the public path --}}
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-speedometer') }}"></use>
            </svg> Dashboard<span class="badge badge-sm bg-info ms-auto">NEW</span></a>
        </li>
        @can('view business location')
        <li class="nav-title">Location Management</li>
        <li class="nav-item"><a class="nav-link" href="{{ route('business-locations.index') }}">
            <svg class="nav-icon">
              {{-- Use Laravel's asset() helper to point to the public path --}}
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-location-pin') }}"></use>
            </svg> Tempat Usaha</a>
        </li>
        @endcan
        <li class="nav-title">User Management</li>
        <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-user') }}"></use>
            </svg> Akun</a></li>
        @can('view role')
        <li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-lock-locked') }}"></use>
            </svg> Role & Permission</a></li>
        @endcan
        <li class="nav-title">Product Management</li>
        @can('view product')
        <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">
          <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-tags') }}"></use>
          </svg>Produk</a>
        </li>
        @endcan
        @can('view store product')
        <li class="nav-item"><a class="nav-link" href="{{ route('products.store.index') }}">
          <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-tags') }}"></use>
          </svg>Produk Toko</a>
        </li>
        @endcan
        @can('view store product')
        <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">
          <svg class="nav-icon">
            <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-tags') }}"></use>
          </svg>Produk Online</a>
        </li>
        @endcan
        <li class="nav-title">Stock Management</li>
        @can('view stock request')
        <li class="nav-item"><a class="nav-link text-truncate" href="{{ route('stock-out-requests.index') }}">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-tags') }}"></use>
            </svg>Permintaan Stok Keluar</a></li>
        @endcan
        @can('view product stock history')
        <li class="nav-item"><a class="nav-link text-truncate" href="{{ route('stock-history.index') }}">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-tags') }}"></use>
            </svg>Histori Stok Produk</a></li>
        @endcan
        @can('scan barcode in')
        <li class="nav-item"><a class="nav-link text-truncate" href="#">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-tags') }}"></use>
            </svg>Scan Stok Masuk</a></li>
        @endcan
        @can('scan barcode out')
        <li class="nav-item"><a class="nav-link text-truncate" href="#">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-tags') }}"></use>
            </svg>Scan Stok Keluar</a></li>
        @endcan
        {{-- @can('scan barcode')
        <li class="nav-item"><a class="nav-link text-truncate" href="{{ route('products.scan_barcode') }}">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-tags') }}"></use>
            </svg>Stock Opname</a></li>
        @endcan --}}
        {{-- <li class="nav-group"><a class="nav-link nav-group-toggle" href="#">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('assets/coreui/icons/free.svg#cil-puzzle') }}"></use>
            </svg> Base</a>
          <ul class="nav-group-items compact">
            <li class="nav-item"><a class="nav-link" href="base/accordion.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Accordion</a></li>
            <li class="nav-item"><a class="nav-link" href="base/breadcrumb.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Breadcrumb</a></li>
            <li class="nav-item"><a class="nav-link" href="https://coreui.io/bootstrap/docs/components/calendar/" target="_blank"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Calendar
                <svg class="icon icon-sm ms-2">
                  <use xlink:href="assets/coreui/icons/free.svg#cil-external-link"></use>
                </svg><span class="badge badge-sm bg-danger ms-auto">PRO</span></a></li>
            <li class="nav-item"><a class="nav-link" href="base/cards.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Cards</a></li>
            <li class="nav-item"><a class="nav-link" href="base/carousel.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Carousel</a></li>
            <li class="nav-item"><a class="nav-link" href="base/collapse.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Collapse</a></li>
            <li class="nav-item"><a class="nav-link" href="base/list-group.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> List group</a></li>
            <li class="nav-item"><a class="nav-link" href="base/navs-tabs.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Navs &amp; Tabs</a></li>
            <li class="nav-item"><a class="nav-link" href="base/pagination.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Pagination</a></li>
            <li class="nav-item"><a class="nav-link" href="base/placeholders.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Placeholders</a></li>
            <li class="nav-item"><a class="nav-link" href="base/popovers.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Popovers</a></li>
            <li class="nav-item"><a class="nav-link" href="base/progress.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Progress</a></li>
            <li class="nav-item"><a class="nav-link" href="base/spinners.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Spinners</a></li>
            <li class="nav-item"><a class="nav-link" href="base/tables.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Tables</a></li>
            <li class="nav-item"><a class="nav-link" href="base/tooltips.html"><span class="nav-icon"><span class="nav-icon-bullet"></span></span> Tooltips</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link text-primary fw-semibold" href="https://coreui.io/product/bootstrap-dashboard-template/" target="_top">
            <svg class="nav-icon text-primary">
              <use xlink:href="assets/coreui/icons/free.svg#cil-layers"></use>
            </svg> Try CoreUI PRO</a></li> --}}
      </ul>
      <div class="sidebar-footer border-top d-none d-md-flex">
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
      </div>
    </div>