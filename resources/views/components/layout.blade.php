<!DOCTYPE html>
<html lang="en">
<head>
    <x-head />
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary {{ Route::currentRouteName() == 'showGenerateForm'?'sidebar-collapse':'' }}">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <meta name="csrf-token" content="{{ csrf_token() }}">

      <x-header  />
      <x-sidebar />
        <!--begin::App Main-->
        <main class="app-main">
        {{ $slot }}

        <x-modal />
        </main>
        <!--end::App Main-->
        <x-footer />
      </div>
      <!--end::App Wrapper-->


</body>
</html>