<!DOCTYPE html>
<html lang="en">
<head>
    <x-head />
</head>
<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="login-logo mb-3 text-center">
            <b>MBizInvent</b>
        </div>
        {{ $slot }}
    </div>
    <script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/bootstrap.min.js') }}"></script>
</body>
</html>
