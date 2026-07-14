@extends('components.layouts.app')

@section('title', 'Tiket Saya | Carnival')

@section('content')
<div class="container" style="max-width: 1000px; margin: 3rem auto; padding: 0 2rem;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; color: var(--primary-color);" class="event-title">E-Ticket Saya</h1>
        <a href="{{ route('pembeli.home') }}" class="btn" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1);">Cari Event Lain</a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <div style="font-size: 1.5rem;">✅</div>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($transactions->isEmpty())
        <div style="text-align: center; padding: 5rem 0; opacity: 0.7; background: var(--card-bg-light); border-radius: 12px;" class="dark-mode-card">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🎟️</div>
            <p>Anda belum memiliki tiket. Yuk, jelajahi konser yang ada!</p>
        </div>
    @else
        <div style="display: grid; gap: 2rem;">
            @foreach($transactions as $trx)
                <div style="background: var(--card-bg-light); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);" class="dark-mode-card">
                    <!-- Header Transaksi -->
                    <div style="background: var(--primary-color); color: #fff; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 0.8rem; opacity: 0.8; margin-bottom: 0.2rem;">ID Transaksi</div>
                            <div style="font-weight: bold; letter-spacing: 1px;">{{ $trx->invoice_number }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.8rem; opacity: 0.8; margin-bottom: 0.2rem;">Tanggal Pembelian</div>
                            <div>{{ date('d M Y, H:i', strtotime($trx->transaction_date)) }}</div>
                        </div>
                    </div>
                    
                    <!-- Daftar Tiket -->
                    <div style="padding: 1.5rem;">
                        @if($trx->payment_status == 'pending')
                            <div style="text-align: center; padding: 2rem;">
                                <div style="font-size: 2rem; margin-bottom: 1rem;">⏳</div>
                                <h3 style="color: #856404; margin-bottom: 0.5rem;">Menunggu Pembayaran</h3>
                                <p style="margin-bottom: 1.5rem; opacity: 0.8;">Selesaikan pembayaran segera agar tiket Anda diterbitkan.</p>
                                <a href="{{ route('pembeli.payment', $trx->id) }}" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 9999px;">Bayar Sekarang</a>
                            </div>
                        @elseif($trx->payment_status == 'expired')
                            <div style="text-align: center; padding: 2rem;">
                                <h3 style="color: #dc3545;">Pembayaran Dibatalkan / Kadaluarsa</h3>
                            </div>
                        @else
                            @foreach($trx->tickets as $ticket)
                            <div style="border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px; padding: 1.5rem; margin-bottom: 1rem; display: grid; grid-template-columns: auto 1fr auto; gap: 2rem; align-items: center; background: rgba(255,255,255,0.02);" class="ticket-card">
                                
                                <!-- QR Code Mockup -->
                                <div style="width: 100px; height: 100px; background: rgba(255,255,255,0.05); padding: 0.5rem; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($ticket->qr_code_payload) }}" alt="QR Code" style="width: 100%;">
                                </div>
                                
                                <!-- Info Tiket -->
                                <div>
                                    <div style="font-weight: bold; font-size: 1.2rem; margin-bottom: 0.5rem; color: var(--primary-color);" class="event-title">{{ $ticket->ticketCategory->event->event_name }}</div>
                                    <div style="display: flex; gap: 2rem; font-size: 0.9rem; opacity: 0.8; margin-bottom: 0.5rem;">
                                        <div>📅 {{ date('d M Y, H:i', strtotime($ticket->ticketCategory->event->event_date)) }}</div>
                                        <div>📍 {{ $ticket->ticketCategory->event->location }}</div>
                                    </div>
                                    <div style="display: inline-block; background: rgba(0,0,0,0.05); padding: 0.2rem 0.8rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem;" class="badge-cat">
                                        {{ $ticket->ticketCategory->category_name }}
                                    </div>
                                    <div style="font-weight: 600;">
                                        👤 {{ $ticket->name_holder }} <span style="opacity:0.6; font-weight: normal;">(NIK: {{ substr($ticket->nik_holder, 0, 6) }}**********)</span>
                                    </div>
                                </div>
                                
                                <!-- Status -->
                                <div style="text-align: right;">
                                    @if($ticket->checkin_status == 'not_used')
                                        <div style="color: #28a745; font-weight: bold; margin-bottom: 0.5rem;">✔️ Aktif</div>
                                        <div style="font-size: 0.8rem; opacity: 0.6;">Tunjukkan QR di gate</div>
                                    @else
                                        <div style="color: #6c757d; font-weight: bold; margin-bottom: 0.5rem;">❌ Sudah Digunakan</div>
                                        <div style="font-size: 0.8rem; opacity: 0.6;">{{ date('d M, H:i', strtotime($ticket->checked_in_at)) }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @endif
                    </div>
                    
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    body.dark-mode .dark-mode-card {
        background: var(--card-bg-dark);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;
    }
    body.dark-mode .event-title { color: #8bb4f6 !important; }
    body.dark-mode .ticket-card { border-color: rgba(255,255,255,0.2) !important; }
    body.dark-mode .badge-cat { background: rgba(255,255,255,0.1) !important; }
    body.dark-mode .btn-primary[href] { border-color: #444 !important; background: #2a2a2a !important; color: #fff !important; }
</style>
@endsection
