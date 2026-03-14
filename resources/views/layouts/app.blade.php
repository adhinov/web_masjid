<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Website Masjid')</title>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom CSS (tanpa Vite) --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="@yield('body-class')">

@include('layouts.navbar')

<main class="py-4">
    @yield('content')
</main>

@include('layouts.footer')

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function () {
        const pingUrl = "{{ route('online.ping') }}";
        const leaveUrl = "{{ route('online.leave') }}";

        const ping = () => {
            fetch(pingUrl, {
                method: "POST",
                headers: { "Accept": "application/json" },
                keepalive: true
            }).catch(() => {});
        };

        const leave = () => {
            if (navigator.sendBeacon) {
                const blob = new Blob([""], { type: "text/plain" });
                navigator.sendBeacon(leaveUrl, blob);
                return;
            }
            fetch(leaveUrl, { method: "POST", keepalive: true }).catch(() => {});
        };

        ping();
        setInterval(ping, 5000);

        document.addEventListener("visibilitychange", () => {
            if (document.visibilityState === "hidden") {
                leave();
            } else {
                ping();
            }
        });

        window.addEventListener("pagehide", leave);
    })();
</script>

@yield('scripts')

</body>
</html>
