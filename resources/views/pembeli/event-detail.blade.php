@extends('components.layouts.app')

@section('title', $event->event_name . ' | Carnival')

@section('content')
<div class="page active" id="event-detail">
    @if(session('error'))
        <div class="wrap" style="margin-top:20px;">
            <div style="background: var(--accent-3); color: #fff; padding: 1rem; border-radius: 8px;">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="ticket-hero">
        <div class="wrap">
            <span class="eyebrow">{{ $event->event_category ?? 'Konser' }}</span>
            <h1>{{ $event->event_name }}</h1>
            <div class="hero-meta" style="justify-content:center; margin-top:20px;">
                <div><div class="num">Tanggal</div><div class="val">{{ date('d M Y', strtotime($event->event_date)) }}</div></div>
                <div><div class="num">Lokasi</div><div class="val">{{ $event->city }}</div></div>
                <div><div class="num">Promotor</div><div class="val">{{ $event->promotor->company_name }}</div></div>
            </div>
            @if($event->banner_image_path)
                <img src="{{ Storage::url($event->banner_image_path) }}" alt="Banner" style="width:100%; max-width:800px; margin:40px auto 0; border-radius:24px; border:1px solid var(--border);">
            @endif
        </div>
    </div>

    <section class="wrap">
        <div class="dash-panels" style="margin-bottom:60px;">
            <div class="panel">
                <h3>Deskripsi Acara</h3>
                <p style="color:var(--text-muted); line-height:1.6; white-space:pre-wrap;">{{ $event->description ?: 'Tidak ada deskripsi.' }}</p>
                
                @if($event->seatmap_image_path)
                <h3 style="margin-top:40px;">Denah Kursi</h3>
                <img src="{{ Storage::url($event->seatmap_image_path) }}" alt="Seatmap" style="max-width:100%; border-radius:12px; margin-top:16px;">
                @endif
            </div>
            <div class="panel">
                <h3>Lokasi</h3>
                <p style="color:var(--text-muted); margin-bottom:16px;">{{ $event->location }}</p>
                @if($event->latitude && $event->longitude)
                <div id="eventMap" style="height:250px; border-radius:12px; z-index:1;"></div>
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let map = L.map('eventMap').setView([{{ $event->latitude }}, {{ $event->longitude }}], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                        L.marker([{{ $event->latitude }}, {{ $event->longitude }}]).addTo(map);
                    });
                </script>
                @endif
            </div>
        </div>

        <div class="section-head">
            <h2>Pilih Tiket</h2>
        </div>

        @php
            $isSaleStarted = empty($event->sale_start_date) || \Carbon\Carbon::parse($event->sale_start_date)->isPast();
        @endphp

        @if($isSaleStarted)
        <form action="{{ route('pembeli.checkout', $event->id) }}" method="GET" id="ticketForm">
            <div class="tier-grid">
                @forelse($event->ticketCategories as $cat)
                <div class="tier-card {{ $loop->first ? 'featured' : '' }}">
                    <div>
                        <div class="tier-name">{{ $cat->category_name }}</div>
                        <div class="tier-price">Rp {{ number_format($cat->price, 0, ',', '.') }}</div>
                    </div>
                    
                    <div class="stub-divider"><div class="dot"></div><div class="dot"></div></div>
                    
                    @if($cat->remaining_quota > 0)
                        <div class="qty-stepper" style="margin:0 auto;">
                            <button type="button" onclick="updateQty({{ $cat->id }}, -1)">-</button>
                            <input type="number" name="qty[{{ $cat->id }}]" id="qty-{{ $cat->id }}" value="0" readonly style="background:transparent; border:none; width:40px; text-align:center; color:#fff; font-weight:700;" data-price="{{ $cat->price }}">
                            <button type="button" onclick="updateQty({{ $cat->id }}, 1, {{ min($cat->remaining_quota, $event->max_tickets_per_nik) }})">+</button>
                        </div>
                        <div style="text-align:center; color:var(--text-muted); font-size:12px; margin-top:8px;">Sisa: {{ $cat->remaining_quota }} tiket</div>
                    @else
                        <div class="badge badge-sold" style="text-align:center; margin:0 auto;">Habis Terjual</div>
                    @endif
                </div>
                @empty
                <div style="grid-column:1/-1; text-align:center; color:var(--text-muted);">Tiket belum tersedia.</div>
                @endforelse
            </div>

            @if($event->ticketCategories->count() > 0)
            <div class="checkout" style="margin-top:40px; display:block;">
                <div class="summary-card" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;">
                    <div>
                        <div style="color:var(--text-muted); font-size:12px; text-transform:uppercase; font-weight:700;">Total Pembayaran</div>
                        <div style="font-size:28px; font-weight:700; font-family:var(--display);">Rp <span id="total-price">0</span></div>
                    </div>
                    
                    @if(Auth::check() && Auth::user()->role !== 'buyer')
                        <div style="text-align: right;">
                            <button type="button" class="btn btn-outline" disabled style="opacity:0.5; cursor:not-allowed;">HANYA AKUN PEMBELI</button>
                            <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">Admin/Promotor tidak bisa membeli tiket.</div>
                        </div>
                    @else
                        <button type="submit" class="btn btn-primary" id="btnCheckout" disabled>BELI TIKET</button>
                    @endif
                </div>
            </div>
            @endif
        </form>
        @else
        <div class="panel" style="text-align:center; padding:60px 20px;">
            <h3>Penjualan Belum Dimulai</h3>
            <p style="color:var(--text-muted); margin-bottom:24px;">Penjualan tiket akan dibuka pada {{ date('d M Y, H:i', strtotime($event->sale_start_date)) }}</p>
            <div id="countdown-timer" style="display:flex; justify-content:center; gap:20px;"></div>
        </div>
        <script>
            const saleStart = new Date("{{ \Carbon\Carbon::parse($event->sale_start_date)->toIso8601String() }}").getTime();
            setInterval(function(){
                const now = new Date().getTime();
                const d = saleStart - now;
                if(d < 0) {
                    setTimeout(() => window.location.reload(), 1500);
                    return;
                }
                const days = Math.floor(d/(1000*60*60*24));
                const hours = Math.floor((d%(1000*60*60*24))/(1000*60*60));
                const mins = Math.floor((d%(1000*60*60))/(1000*60));
                const secs = Math.floor((d%(1000*60))/1000);
                document.getElementById('countdown-timer').innerHTML = `
                    <div><h2 style="font-size:32px; color:var(--accent)">${days}</h2><span style="font-size:12px; color:var(--text-muted)">HARI</span></div>
                    <div><h2 style="font-size:32px; color:var(--accent)">${hours}</h2><span style="font-size:12px; color:var(--text-muted)">JAM</span></div>
                    <div><h2 style="font-size:32px; color:var(--accent)">${mins}</h2><span style="font-size:12px; color:var(--text-muted)">MNT</span></div>
                    <div><h2 style="font-size:32px; color:var(--accent)">${secs}</h2><span style="font-size:12px; color:var(--text-muted)">DTK</span></div>
                `;
            }, 1000);
        </script>
        @endif
    </section>
</div>

<script>
    function updateQty(id, change, max) {
        const input = document.getElementById('qty-' + id);
        let current = parseInt(input.value) || 0;
        let newVal = current + change;
        if(newVal < 0) newVal = 0;
        if(max !== undefined && newVal > max) newVal = max;
        input.value = newVal;
        calcTotal();
    }
    function calcTotal() {
        let total = 0, totalQty = 0;
        document.querySelectorAll('input[name^="qty["]').forEach(inp => {
            let qty = parseInt(inp.value) || 0;
            total += qty * parseInt(inp.getAttribute('data-price'));
            totalQty += qty;
        });
        document.getElementById('total-price').innerText = total.toLocaleString('id-ID');
        const btn = document.getElementById('btnCheckout');
        const maxGlobal = {{ $event->max_tickets_per_nik }};
        btn.disabled = !(totalQty > 0 && totalQty <= maxGlobal);
        if(totalQty > maxGlobal) {
            alert('Maksimal ' + maxGlobal + ' tiket per pesanan.');
            document.querySelectorAll('input[name^="qty["]').forEach(i => i.value = 0);
            calcTotal();
        }
    }
</script>
@endsection
