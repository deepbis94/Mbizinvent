<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MBizInvent</title>
<!--begin::Primary Meta Tags-->
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="title" content="MBizInvent" />
<meta name="author" content="MBizInvent" />
<link rel="shortcut icon" href="{{ asset('public/assets/img/AdminLTELogo.png')}}" type="image/x-icon">
<meta
  name="description"
  content="MBizInvent inventory and GST invoicing."
/>
<meta
  name="keywords"
  content="inventory, gst, invoice, mbizinvent"
/>
<!--end::Primary Meta Tags-->
<!--begin::Fonts-->
<link
  rel="stylesheet"
  href="{{ asset('public/assets/css/index.css')}}"
/>
<!--end::Fonts-->
<!--begin::Third Party Plugin(OverlayScrollbars)-->
<link
  rel="stylesheet"
  href="{{ asset('public/assets/css/overlayscrollbars.min.css')}}"
/>
<!--end::Third Party Plugin(OverlayScrollbars)-->
<!--begin::Third Party Plugin(Bootstrap Icons)-->
<link
  rel="stylesheet"
  href="{{ asset('public/assets/css/bootstrap-icons.min.css')}}"
/>
<!--end::Third Party Plugin(Bootstrap Icons)-->
<!--begin::Required Plugin(AdminLTE)-->
<link rel="stylesheet" href="{{ asset('public/assets/css/toastr.min.css') }}">
<link rel="stylesheet" href="{{asset('public/assets/css/adminlte.css')}}" />
<!--end::Required Plugin(AdminLTE)-->
<!-- apexcharts -->
<link
  rel="stylesheet"
  href="{{asset('public/assets/css/apexcharts.css')}}"
/>

<link rel="stylesheet" href="{{ asset('public/assets/css/jquery-ui.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
<style>
  /* HTML: <div class="loader"></div> */
  .loader {
    width: 50px;
    aspect-ratio: 1;
    display: grid;
    border: 4px solid #0000;
    border-radius: 50%;
    border-color: #ccc #0000;
    animation: l16 1s infinite linear;
  }
  .loader::before,
  .loader::after {    
    content: "";
    grid-area: 1/1;
    margin: 2px;
    border: inherit;
    border-radius: 50%;
  }
  .loader::before {
    border-color: #f03355 #0000;
    animation: inherit; 
    animation-duration: .5s;
    animation-direction: reverse;
  }
  .loader::after {
    margin: 8px;
  }
  @keyframes l16 { 
    100%{transform: rotate(1turn)}
  }        
      </style>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(function(){
      $(".sel2input").select2()   

    });
  </script>