@extends('components.layouts.app')

@section('title', 'Checkout | Carnival')

@section('content')
<div class="wrap" style="padding-top:40px; padding-bottom:80px;">
    @if(session('error'))
        <div style="background: var(--accent-3); color: #fff; padding: 1rem; border-radius: 8px; margin-bottom: 24px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom: 24px;">
        <a href="{{ route('pembeli.event', $event->id) }}" style="color:var(--text-muted); font-weight:700; font-size:14px; text-transform:uppercase;">&larr; Kembali</a>
    </div>

    <div class="checkout">
        <!-- Identitas Pemegang Tiket Form -->
        <div class="form-card">
            <h2 style="font-size:24px; margin-bottom:8px;">Informasi Pemegang Tiket</h2>
            <p style="color:var(--text-muted); font-size:14px; margin-bottom:32px;">Sesuai regulasi, NIK diperlukan untuk keamanan. E-Tiket tidak dapat dipindahtangankan.</p>
            
            <form action="{{ route('pembeli.checkout.process', $event->id) }}" method="POST" id="checkoutForm">
                @csrf
                @php $ticketIndex = 0; @endphp
                @foreach($quantities as $catId => $qty)
                    @php $category = $categories->where('id', $catId)->first(); @endphp
                    @for($i = 0; $i < $qty; $i++)
                        <div style="border:1px solid var(--border); padding:24px; border-radius:12px; margin-bottom:24px; position:relative; background:var(--bg-elevated);">
                            <div class="badge badge-live" style="position:absolute; top:-12px; left:20px;">
                                Tiket {{ $ticketIndex + 1 }} - {{ $category->category_name }}
                            </div>
                            
                            <input type="hidden" name="tickets[{{ $ticketIndex }}][category_id]" value="{{ $catId }}">
                            
                            <div class="field" style="margin-bottom:16px; margin-top:10px;">
                                <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom: 8px;">
                                    <label style="margin-bottom:0;">Nama Lengkap (Sesuai KTP)</label>
                                </div>
                                <div style="position:relative;">
                                    <input type="text" id="name_{{ $ticketIndex }}" name="tickets[{{ $ticketIndex }}][name]" required minlength="3" placeholder="" autocomplete="off" onfocus="document.getElementById('suggestion_{{ $ticketIndex }}').style.display='block'" onblur="setTimeout(() => document.getElementById('suggestion_{{ $ticketIndex }}').style.display='none', 200)" style="width:100%; box-sizing:border-box; display:block;">
                                    
                                    <div id="suggestion_{{ $ticketIndex }}" onclick="autofillData({{ $ticketIndex }})" style="display:none; position:absolute; top:100%; left:0; width:100%; background:var(--bg-elevated); border:1px solid var(--border); padding:12px; border-radius:8px; z-index:50; margin-top:4px; cursor:pointer; box-shadow:0 10px 30px rgba(0,0,0,0.8); transition: 0.2s;">
                                        <div style="font-weight:bold; color:var(--accent);">{{ Auth::user()->name }}</div>
                                        <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Gunakan data akun saya ({{ Auth::user()->phone_number }})</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="field" style="margin-bottom:16px;">
                                <label>NIK (Nomor Induk Kependudukan)</label>
                                <input type="text" id="nik_{{ $ticketIndex }}" name="tickets[{{ $ticketIndex }}][nik]" required minlength="16" maxlength="16" pattern="[0-9]{16}" placeholder="">
                            </div>

                            <div class="field">
                                <label>Nomor HP Aktif</label>
                                <input type="text" id="phone_{{ $ticketIndex }}" name="tickets[{{ $ticketIndex }}][phone]" required minlength="10" placeholder="">
                            </div>
                        </div>
                        @php $ticketIndex++; @endphp
                    @endfor
                @endforeach
            </form>
        </div>
        
        <!-- Ringkasan Pesanan -->
        <div class="summary-card" style="position:sticky; top:88px;">
            <h3 style="margin-bottom:24px;">Ringkasan Pesanan</h3>
            <div style="margin-bottom:24px;">
                <div style="font-family:var(--display); font-size:20px; text-transform:uppercase;">{{ $event->event_name }}</div>
                <div style="font-family:var(--mono); font-size:12px; color:var(--text-muted); margin-top:4px;">{{ date('d M Y', strtotime($event->event_date)) }}</div>
            </div>
            
            <div class="stub-divider" style="margin-bottom:24px;"><div class="dot"></div><div class="dot"></div></div>
            
            <div style="display:flex; flex-direction:column; gap:16px; margin-bottom:24px;">
                @php $totalAmount = 0; @endphp
                @foreach($quantities as $catId => $qty)
                    @php 
                        $category = $categories->where('id', $catId)->first(); 
                        $subtotal = $category->price * $qty;
                        $totalAmount += $subtotal;
                    @endphp
                    <div class="summary-row" style="padding:0; border:none;">
                        <div>
                            <div style="font-weight:700; color:var(--text);">{{ $qty }}x {{ $category->category_name }}</div>
                            <div style="font-family:var(--mono); font-size:11px;">Rp {{ number_format($category->price, 0, ',', '.') }}</div>
                        </div>
                        <div style="font-family:var(--mono); font-weight:700; color:var(--text);">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
            
            <div class="stub-divider" style="margin-bottom:24px;"><div class="dot"></div><div class="dot"></div></div>
            
            <div class="summary-row total" style="padding:0; margin-bottom:32px;">
                <div style="font-family:var(--mono); font-size:13px; color:var(--text-muted);">TOTAL</div>
                <div style="font-family:var(--display); font-size:28px; color:var(--accent);">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            </div>
            
            <button type="button" onclick="document.getElementById('checkoutForm').submit();" class="btn btn-primary btn-block">LANJUT PEMBAYARAN</button>
            <div style="text-align:center; margin-top:16px; font-size:12px; color:var(--text-muted);">🔒 Pembayaran aman dan terenkripsi.</div>
        </div>
    </div>
</div>

<script>
    const userData = {
        name: "{{ Auth::user()->name }}",
        phone: "{{ Auth::user()->phone_number }}",
        nik: "{{ Auth::user()->identity->nik ?? '' }}"
    };

    function autofillData(index) {
        document.getElementById('name_' + index).value = userData.name;
        document.getElementById('phone_' + index).value = userData.phone;
        document.getElementById('nik_' + index).value = userData.nik; 
        document.getElementById('suggestion_' + index).style.display = 'none';
    }
</script>
@endsection
