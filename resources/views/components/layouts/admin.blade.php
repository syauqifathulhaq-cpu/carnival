<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard | Carnival')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Form Enhancement Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
</head>
<body>
    <nav class="kit-nav">
        <div class="wrap" style="width: 100%; max-width: 100%; display: flex; align-items: center; justify-content: space-between;">
            <div style="display:flex; align-items:center; gap:16px;">
                <button class="mobile-nav-toggle" onclick="document.querySelector('.dash-sidebar').classList.add('open'); document.querySelector('.sidebar-overlay').classList.add('open');">☰</button>
                <a href="{{ route('pembeli.home') }}" class="kit-logo" style="text-decoration:none;">CARNIVAL<span>.</span></a>
            </div>

            <div style="display:flex; align-items:center; gap:16px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="text-align:right;">
                        <div style="color:var(--text); font-weight:700; font-size:14px; font-family:var(--body);">Admin</div>
                        <div style="color:var(--text-muted); font-size:11px; font-family:var(--mono); text-transform:uppercase;">System</div>
                    </div>
                    <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg, var(--accent-3), var(--accent-2)); color:#0A0A0F; display:flex; align-items:center; justify-content:center; font-weight:800; font-family:var(--display); font-size:18px;">
                        A
                    </div>
                </div>
                <form action="{{ route('auth.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm" style="padding: 6px 12px; font-size:12px;">Keluar</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="page active" id="admin">
        <div class="dash-shell">
            <div class="sidebar-overlay" onclick="document.querySelector('.dash-sidebar').classList.remove('open'); document.querySelector('.sidebar-overlay').classList.remove('open');"></div>
            @include('components.admin-sidebar')
            <main class="dash-main">
                <div class="dash-topbar" style="margin-bottom: 24px;">
                    <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">@yield('page_title', 'Ringkasan')</h2>
                </div>
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Form Enhancement Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize Choices.js
            const selects = document.querySelectorAll('select');
            selects.forEach(select => {
                new Choices(select, {
                    searchEnabled: false,
                    itemSelectText: '',
                    shouldSort: false,
                });
            });

            // Initialize Flatpickr for datetime
            flatpickr("input[type='datetime-local']", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true
            });

            // Initialize Flatpickr for date
            flatpickr("input[type='date']", {
                dateFormat: "Y-m-d"
            });
        });
    </script>
</body>
</html>
