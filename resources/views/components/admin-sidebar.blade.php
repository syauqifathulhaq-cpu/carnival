<aside class="dash-sidebar">
  <a href="{{ route('admin.dashboard') }}" class="logo">CARNIVAL<span style="color:var(--accent)">.</span>ADMIN</a>
  <nav class="dash-nav">
    <a href="{{ route('admin.dashboard') }}" class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('admin.users') }}" class="{{ Route::is('admin.users') ? 'active' : '' }}">Kelola Pengguna</a>
    <a href="{{ route('admin.promotors') }}" class="{{ Route::is('admin.promotors') ? 'active' : '' }}">Kelola Promotor</a>
    <a href="{{ route('admin.events') }}" class="{{ Route::is('admin.events') ? 'active' : '' }}">Kelola Event</a>
    <a href="{{ route('admin.finance') }}" class="{{ Route::is('admin.finance') ? 'active' : '' }}">Transaksi</a>
    <a href="{{ route('admin.payouts') }}" class="{{ Route::is('admin.payouts') ? 'active' : '' }}">Pencairan Dana</a>
    <a href="{{ route('admin.reports') }}" class="{{ Route::is('admin.reports') ? 'active' : '' }}">Laporan</a>
  </nav>
</aside>
