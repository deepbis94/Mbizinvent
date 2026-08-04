        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
          <!--begin::Sidebar Brand-->
          <div class="sidebar-brand">
            <!--begin::Brand Link-->
            <a href="{{ route('home') }}" class="brand-link">
              <!--begin::Brand Image-->
              <img
                src="{{ asset('public/assets/img/AdminLTELogo.png')}}"
                alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow"
              />
              <!--end::Brand Image-->
              <!--begin::Brand Text-->
              <span class="brand-text fw-light">MBizInvent</span>
              <!--end::Brand Text-->
            </a>
            <!--end::Brand Link-->
          </div>
          <!--end::Sidebar Brand-->
          <!--begin::Sidebar Wrapper-->
          <div class="sidebar-wrapper">
            <nav class="mt-2">
              <!--begin::Sidebar Menu-->
              <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="menu"
                data-accordion="false"
              >
                <li class="nav-item">
                  <a href="{{ route('home') }}" class="nav-link {{ Route::currentRouteName() == 'home'?'active':'' }}">
                    <i class="nav-icon bi bi-palette"></i>
                    <p>Dashboard</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('customers.index') }}" class="nav-link {{ Route::currentRouteName() == 'customers.index'?'active':'' }}">
                    <i class="nav-icon bi bi-people-fill"></i>
                    <p>Manage Customers</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('products.index') }}" class="nav-link {{ Route::currentRouteName() == 'products.index'?'active':'' }}">
                    <i class="nav-icon bi bi-basket-fill"></i>
                    <p>Manage Products</p>
                  </a>
                </li>                
                <li class="nav-item">
                  <a href="{{ route('inventory.index') }}" class="nav-link {{ Route::currentRouteName() == 'inventory.index'?'active':'' }}">
                    <i class="nav-icon bi bi-list-nested"></i>
                    <p>Manage Inventory</p>
                  </a>
                </li>                                
                <li class="nav-item">
                  <a href="{{ route('inventoryHistory') }}" class="nav-link {{ Route::currentRouteName() == 'inventoryHistory'?'active':'' }}">
                    <i class="nav-icon bi bi-table"></i>
                    <p>Inventory History</p>
                  </a>
                </li>                                                
                <li class="nav-item">
                  <a href="{{ route('viewSettings') }}" class="nav-link {{ Route::currentRouteName() == 'viewSettings'?'active':'' }}">
                    <i class="nav-icon bi bi-gear"></i>
                    <p>Manage Settings</p>
                  </a>
                </li>                                                
                <li class="nav-item">
                  <a href="{{ route('showGenerateForm') }}" class="nav-link {{ Route::currentRouteName() == 'showGenerateForm'?'active':'' }}">
                    <i class="nav-icon bi bi-clipboard2-plus"></i>
                    <p>Generate Invoice</p>
                  </a>
                </li>                                                
                <li class="nav-item">
                  <a href="{{ route('invoiceList') }}" class="nav-link {{ Route::currentRouteName() == 'invoiceList'?'active':'' }}">
                    <i class="nav-icon bi bi-clipboard-data"></i>
                    <p>Invoice List</p>
                  </a>
                </li>                                                

              </ul>
              <!--end::Sidebar Menu-->
            </nav>
          </div>
          <!--end::Sidebar Wrapper-->
        </aside>
        <!--end::Sidebar-->
