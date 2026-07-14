@extends('components.layouts.app')

@section('title', 'Pembayaran Tiket | Carnival')

@section('content')
<div class="container" style="max-width: 600px; margin: 4rem auto; padding: 2rem; background: var(--card-bg-light); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center;" class="dark-mode-card">
    <div style="font-size: 3rem; margin-bottom: 1rem;">💳</div>
    <h2 style="color: var(--primary-color); margin-bottom: 0.5rem;" class="event-title">Selesaikan Pembayaran Anda</h2>
    <p style="opacity: 0.8; margin-bottom: 2rem;">Selesaikan pembayaran Anda segera agar tiket tidak hangus.</p>
    
    <div style="background: rgba(0,0,0,0.02); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; text-align: left;" class="summary-box">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span style="opacity: 0.7;">No. Invoice</span>
            <span style="font-weight: bold;">{{ $transaction->invoice_number }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span style="opacity: 0.7;">Total Tagihan</span>
            <span style="font-weight: bold; color: var(--primary-color); font-size: 1.2rem;" class="event-title">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
            <span style="opacity: 0.7;">Status</span>
            @if($transaction->payment_status == 'pending')
                <span style="color: #856404; background: #ffeeba; padding: 0.2rem 0.6rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">Menunggu Pembayaran</span>
            @elseif($transaction->payment_status == 'paid')
                <span style="color: #155724; background: #d4edda; padding: 0.2rem 0.6rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">Lunas</span>
            @else
                <span style="color: #721c24; background: #f8d7da; padding: 0.2rem 0.6rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">Kadaluarsa / Batal</span>
            @endif
        </div>
    </div>
    
    @if($transaction->payment_status == 'pending')
        <button id="pay-button" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(50, 89, 232, 0.4);">
            Bayar Sekarang
        </button>
    @elseif($transaction->payment_status == 'paid')
        <a href="{{ route('pembeli.tickets') }}" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; border-radius: 12px;">Lihat E-Ticket</a>
    @endif
</div>

<style>
    body.dark-mode .dark-mode-card { background: var(--card-bg-dark) !important; box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important; }
    body.dark-mode .event-title { color: #8bb4f6 !important; }
    body.dark-mode .summary-box { background: rgba(255,255,255,0.05) !important; }
</style>

@if($transaction->payment_status == 'pending')
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function () {
        // Trigger snap popup. @TODO: Ganti ini dengan token dari backend Anda
        window.snap.pay('{{ $transaction->snap_token }}', {
            onSuccess: function(result){
                alert("Pembayaran Berhasil!");
                window.location.href = "{{ route('pembeli.tickets') }}";
            },
            onPending: function(result){
                alert("Menunggu pembayaran Anda!");
            },
            onError: function(result){
                alert("Pembayaran gagal!");
            },
            onClose: function(){
                console.log('User menutup popup tanpa menyelesaikan pembayaran');
            }
        });
    };
</script>
@endif

@endsection
