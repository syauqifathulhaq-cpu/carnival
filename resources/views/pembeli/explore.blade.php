@extends('components.layouts.app')

@section('title', 'Jelajah Event | Carnival')

@section('content')
<div class="wrap" style="padding-top: 40px; padding-bottom: 80px; min-height: 80vh;">
    
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-family: var(--display); font-size: 36px; text-transform: uppercase; margin-bottom: 16px;">Eksplorasi Event</h1>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 24px auto;">Cari dan temukan konser atau acara hiburan favorit Anda dari seluruh penjuru kota.</p>
        
        <form action="{{ route('pembeli.explore') }}" method="GET" style="display:flex; justify-content:center; gap:10px; max-width:500px; margin:0 auto;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama konser atau kota..." style="flex:1; padding:12px 20px; border-radius:999px; border:1px solid var(--border); background:var(--bg-elevated); color:var(--text); font-family:var(--body); font-size:16px;">
            <button class="btn btn-primary" type="submit" style="padding:12px 28px; border-radius:999px;">CARI</button>
        </form>
    </div>

    @if($events->isEmpty())
        <div style="text-align: center; padding: 4rem 0; opacity: 0.7; background: var(--bg-elevated); border-radius: 12px; border: 1px dashed var(--border);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px; opacity: 0.5;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <h3 style="font-family: var(--body); font-size: 18px; margin-bottom: 8px;">Tidak Ada Hasil</h3>
            @if(request('q'))
                <p>Maaf, kami tidak menemukan event yang cocok dengan pencarian "<strong>{{ request('q') }}</strong>".</p>
                <a href="{{ route('pembeli.explore') }}" style="color: var(--accent); display: inline-block; margin-top: 16px; font-weight: bold; text-decoration: none;">&larr; Lihat Semua Event</a>
            @else
                <p>Belum ada event yang aktif saat ini. Silakan kembali lagi nanti.</p>
            @endif
        </div>
    @else
        <div style="margin-bottom: 24px; color: var(--text-muted); font-size: 14px;">
            Menampilkan {{ $events->count() }} event aktif.
        </div>
        
        <div class="lineup-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px;">
            @foreach($events as $event)
                <a href="{{ route('pembeli.event', $event->id) }}" class="artist-card" style="width: 100%;">
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
    @endif
</div>
@endsection
