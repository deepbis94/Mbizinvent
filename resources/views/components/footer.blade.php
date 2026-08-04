      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">MM ENTERPRISE</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; {{ date('Y') }}&nbsp;
          <a href="{{ route('home') }}" class="text-decoration-none">MBizInvent</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="{{ asset('public/assets/js/overlayscrollbars.browser.es6.min.js')}}"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="{{ asset('public/assets/js/popper.min.js')}}"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="{{ asset('public/assets/js/bootstrap.min.js')}}"
    ></script>
    <script src="{{ asset('public/assets/js/toastr.min.js')}}"></script>

    <script src="{{ asset('public/assets/js/jquery-ui.min.js') }}"></script>

    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('public/assets/js/adminlte.js')}}"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });

      function closeModal(id){
        $(`#${id}`).modal('hide');
      }

      function triggerSaveChanges(modalId){
        // Create a new custom event called 'myCustomEvent'
        $("#addEditForm").submit();
      }

      $( function() {
    $( ".datepicker").datepicker({
      dateFormat: "dd-mm-yy",        // <-- Add this line
      defaultDate: new Date() // optional
    }).datepicker("setDate", new Date());;
  } );

    </script>
    <!--end::OverlayScrollbars Configure-->
