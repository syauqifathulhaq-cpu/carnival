@extends('components.layouts.app')

@section('title', 'Katalog Konser | Carnival')

@section('content')
<div class="page active" id="landing">
  <header class="hero">
    <div class="wrap hero-grid">
      <div>
        <span class="eyebrow">Platform Resmi Penjualan Tiket</span>
        <h1>Temukan<br>Konser <em>Impianmu.</em></h1>
        <p class="lead">Pesan tiket konser, festival, dan acara hiburan terbaik dengan mudah, aman, dan tanpa calo.</p>
        <div class="hero-cta">
          <form action="#" style="display:flex; gap:10px; width:100%; max-width:400px;">
            <input type="text" placeholder="Cari event..." style="flex:1; padding:12px 16px; border-radius:999px; border:1px solid var(--border); background:var(--bg-elevated); color:var(--text); font-family:var(--body);">
            <button class="btn btn-primary" style="padding:10px 24px;" type="submit">CARI</button>
          </form>
        </div>
      </div>
      <div class="hero-art">
        <div class="bars">
          <i style="height:40%"></i><i style="height:70%"></i><i style="height:50%"></i><i style="height:90%"></i>
          <i style="height:60%"></i><i style="height:80%"></i><i style="height:45%"></i><i style="height:65%"></i>
        </div>
      </div>
    </div>
  </header>

  <div class="marquee" style="border-top: 1px solid rgba(244,243,240,.15); border-bottom: 1px solid rgba(244,243,240,.15); padding-bottom: 18px; display: block !important;">
    <span style="animation-play-state: running !important;">
        @if($events->count() > 0)
            @foreach($events as $event)
                {{ strtoupper($event->event_name) }} &nbsp;•&nbsp; {{ strtoupper($event->city) }} &nbsp;•&nbsp; 
            @endforeach
            @foreach($events as $event)
                {{ strtoupper($event->event_name) }} &nbsp;•&nbsp; {{ strtoupper($event->city) }} &nbsp;•&nbsp; 
            @endforeach
        @else
            BELUM ADA EVENT &nbsp;•&nbsp; NANTIKAN KEJUTAN KAMI &nbsp;•&nbsp;
        @endif
    </span>
  </div>

  <section class="wrap" style="padding-top: 60px; padding-bottom: 80px;">
    <div class="section-head" style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:36px; gap:20px; flex-wrap:wrap;">
      <h2>Event Mendatang</h2>
    </div>

    @if($events->isEmpty())
        <div style="text-align: center; padding: 5rem 0; opacity: 0.7;">
            <p>Belum ada event yang aktif saat ini. Silakan kembali lagi nanti.</p>
        </div>
    @else
        <style>
            #eventCarousel::-webkit-scrollbar { display: none; }
            .carousel-btn { transition: transform 0.2s, background 0.2s; }
            .carousel-btn:hover { transform: scale(1.1); background: var(--text); }
        </style>
        <div style="position: relative; display: flex; align-items: center;">
            <button class="carousel-btn" onclick="document.getElementById('eventCarousel').scrollBy({left: -320, behavior: 'smooth'})" style="position:absolute; left:-20px; z-index:10; background:#0A0A0F; color:#FFFFFF; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border:2px solid var(--border); box-shadow:0 4px 12px rgba(0,0,0,0.5); cursor:pointer;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            
            <div id="eventCarousel" class="lineup-grid" style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; gap: 16px; width: 100%; padding-bottom: 20px;">
                @foreach($events as $event)
                    <a href="{{ route('pembeli.event', $event->id) }}" class="artist-card" style="min-width: 260px; max-width: 280px; scroll-snap-align: start; flex-shrink: 0;">
                        @if($event->banner_image_path)
                            <div class="artist-avatar" style="background-image: url('{{ Storage::url($event->banner_image_path) }}');"></div>
                        @else
                            <div class="artist-avatar" style="display:flex; align-items:center; justify-content:center; color:#0A0A0F; font-family:var(--mono); font-weight:700;">TANPA POSTER</div>
                        @endif
                        <h3>{{ $event->event_name }}</h3>
                        <div class="stage">{{ date('d M Y', strtotime($event->event_date)) }} · {{ $event->city }}</div>
                    </a>
                @endforeach
            </div>

            <button class="carousel-btn" onclick="document.getElementById('eventCarousel').scrollBy({left: 320, behavior: 'smooth'})" style="position:absolute; right:-20px; z-index:10; background:#0A0A0F; color:#FFFFFF; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border:2px solid var(--border); box-shadow:0 4px 12px rgba(0,0,0,0.5); cursor:pointer;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>
    @endif
  </section>
</div>
@endsection
