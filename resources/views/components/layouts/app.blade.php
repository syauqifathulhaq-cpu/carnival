<!DOCTYPE html>
<html lang="id" style="color-scheme: dark;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <title>@yield('title', 'Carnival | Your Ticketing Partner')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .user-dropdown {
            position: relative;
            display: inline-block;
        }
        .user-dropdown-btn {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            margin-right: 12px;
            cursor: pointer;
            padding: 8px;
        }
        .user-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 12px;
            background-color: rgba(26, 26, 36, 0.95);
            backdrop-filter: blur(12px);
            min-width: 180px;
            box-shadow: 0 10px 30px rgba(212, 255, 63, 0.15);
            z-index: 100;
            border-radius: 14px;
            overflow: hidden;
            /* border removed */
        }
        .user-dropdown.show .user-dropdown-content {
            display: block;
            animation: dropdownFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-8px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .user-dropdown-content a, .user-dropdown-content button {
            color: var(--text);
            padding: 12px 18px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            font-weight: 600;
            transition: 0.15s;
        }
        .user-dropdown-content a:hover, .user-dropdown-content button:hover {
            background-color: rgba(212, 255, 63, 0.1);
            color: var(--accent);
            padding-left: 24px;
        }
    </style>
</head>
<body>

<nav class="kit-nav">
  <div class="wrap">
    <a href="{{ route('pembeli.home') }}" class="kit-logo" style="text-decoration:none;">CARNIVAL<span>.</span></a>
    <div style="display: flex; align-items: center; gap: 16px; flex: 1; justify-content: flex-end;">
      <div class="kit-tabs" style="flex: 1; justify-content: flex-end;">
        <a href="{{ route('pembeli.home') }}" class="kit-tab {{ request()->routeIs('pembeli.home') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('pembeli.explore') }}" class="kit-tab {{ request()->routeIs('pembeli.explore') ? 'active' : '' }}">Jelajah</a>
      </div>
      
      <div class="auth-section" style="display: flex; align-items: center;">
        @auth
            <div class="user-dropdown" id="userDropdown">
                <span class="user-dropdown-btn" onclick="document.getElementById('userDropdown').classList.toggle('show')">Halo, {{ explode(' ', Auth::user()->name)[0] }}! ▼</span>
                <div class="user-dropdown-content">
                    @if(Auth::user()->role === 'buyer')
                        <a href="{{ route('pembeli.settings') }}">Pengaturan</a>
                        <a href="{{ route('pembeli.tickets') }}">Tiket Saya</a>
                        <a href="{{ route('pembeli.history') }}">Riwayat</a>
                    @elseif(Auth::user()->role === 'promotor')
                        <a href="{{ route('promotor.dashboard') }}" style="color: var(--accent);">Dashboard Promotor</a>
                    @elseif(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" style="color: var(--accent);">Dashboard Admin</a>
                    @endif
                    <form action="{{ route('auth.logout') }}" method="POST" style="margin: 0; padding: 0;">
                        @csrf
                        <button type="submit" style="width:100%; text-align:left; background:none; border:none; cursor:pointer; color:inherit; font-family:inherit;">Keluar</button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('promotor.dashboard') }}" class="btn btn-outline btn-sm" style="margin-right: 8px; padding: 6px 12px;">Promotor</a>
            <a href="{{ route('auth.login') }}" class="btn btn-primary btn-sm" style="padding: 6px 12px;">Masuk</a>
        @endauth
      </div>
    </div>
  </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="footer">
  <div class="wrap">
    <div>© 2026 Carnival. Hak Cipta Dilindungi.</div>
    <div>Your Professional Ticketing Partner · info@carnival.id</div>
  </div>
</footer>

<script>
    // Menutup dropdown jika diklik di luar elemen
    document.addEventListener('click', function(event) {
        var dropdown = document.getElementById('userDropdown');
        if (dropdown && !dropdown.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
</script>

</body>
</html>
