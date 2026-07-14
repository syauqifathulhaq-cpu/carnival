<aside class="dash-sidebar">
  <a href="{{ route('promotor.dashboard') }}" class="logo">CARNIVAL<span style="color:var(--accent-2)">.</span>PROMOTOR</a>
  <nav class="dash-nav">
    <a href="{{ route('promotor.dashboard') }}" class="{{ Route::is('promotor.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('promotor.events.index') }}" class="{{ Route::is('promotor.events.*') ? 'active' : '' }}">Kelola Event</a>
    <a href="{{ route('promotor.transactions') }}" class="{{ Route::is('promotor.transactions') ? 'active' : '' }}">Transaksi</a>
    <a href="{{ route('promotor.scanner') }}" class="{{ Route::is('promotor.scanner') ? 'active' : '' }}">Scanner</a>
    <a href="{{ route('promotor.payouts') }}" class="{{ Route::is('promotor.payouts') ? 'active' : '' }}">Tarik Dana</a>
    <a href="{{ route('promotor.report') }}" class="{{ Route::is('promotor.report') ? 'active' : '' }}">Laporan</a>
  </nav>
</aside>
