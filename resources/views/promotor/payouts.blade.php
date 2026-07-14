@extends('components.layouts.promotor')

@section('title', 'Tarik Dana | Promotor Carnival')

@section('content')
<div class="dash-topbar" style="margin-bottom: 24px;">
    <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Tarik Dana (Payout)</h2>
</div>

@if(session('success'))
    <div style="background: rgba(212,255,63,.15); color: var(--accent); padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid var(--accent);">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background: rgba(255,61,122,.15); color: var(--accent-3); padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid var(--accent-3);">
        ❌ {{ session('error') }}
    </div>
@endif

<div class="stat-grid">
    <div class="stat-card">
        <div class="label">Total Pendapatan (Net 95%)</div>
        <div class="value">Rp {{ number_format($netSales, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="label" style="color:var(--accent-2);">Sedang/Telah Ditarik</div>
        <div class="value" style="color:var(--accent-2);">Rp {{ number_format($totalWithdrawn, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="label" style="color:var(--accent);">Saldo Tersedia</div>
        <div class="value" style="color:var(--accent);">Rp {{ number_format($availableBalance, 0, ',', '.') }}</div>
    </div>
</div>

<div class="dash-panels" style="margin-top: 32px;">
    <!-- Form Request Payout -->
    <div class="form-card">
        <h3 style="font-family:var(--body); text-transform:none; margin-bottom:24px;">Ajukan Pencairan Baru</h3>
        
        <form action="{{ route('promotor.payouts.request') }}" method="POST">
            @csrf
            
            <div class="field" style="margin-bottom: 16px;">
                <label>Nominal Penarikan (Rp)</label>
                <input type="number" name="amount" required min="50000" max="{{ $availableBalance }}" value="{{ $availableBalance }}">
                <small style="color:var(--text-muted); font-size:12px;">Minimal penarikan Rp 50.000</small>
            </div>
            
            <div class="field" style="margin-bottom: 16px;">
                <label>Nama Bank Tujuan</label>
                <select name="bank_name" required>
                    <option value="">-- Pilih Bank --</option>
                    <option value="BCA">BCA</option>
                    <option value="Mandiri">Mandiri</option>
                    <option value="BNI">BNI</option>
                    <option value="BRI">BRI</option>
                    <option value="BSI">BSI</option>
                    <option value="GoPay">GoPay</option>
                    <option value="OVO">OVO</option>
                    <option value="Dana">Dana</option>
                </select>
            </div>
            
            <div class="field" style="margin-bottom: 16px;">
                <label>Nomor Rekening / Nomor e-Wallet</label>
                <input type="text" name="account_number" required>
            </div>
            
            <div class="field" style="margin-bottom: 32px;">
                <label>Nama Pemilik Rekening</label>
                <input type="text" name="account_name" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block" {{ $availableBalance < 50000 ? 'disabled' : '' }}>
                AJUKAN PENCAIRAN DANA
            </button>
        </form>
    </div>

    <!-- History -->
    <div class="panel" style="padding:0; overflow-x:auto; height:fit-content;">
        <h3 style="font-family:var(--body); text-transform:none; margin-bottom:0; padding:24px 24px 16px;">Riwayat Penarikan Dana</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Rekening Tujuan</th>
                    <th>Status</th>
                    <th>Bukti Transfer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $p)
                <tr>
                    <td><div class="badge">{{ $p->created_at->format('d M Y, H:i') }}</div></td>
                    <td style="font-weight: 700; color:var(--text);">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td style="font-size:13px; color:var(--text-muted); line-height:1.4;">
                        {{ $p->bank_name }} - {{ $p->account_number }}<br>
                        <span style="color:var(--text);">{{ $p->account_name }}</span>
                    </td>
                    <td>
                        @if($p->status == 'pending')
                            <span class="badge badge-soon">PENDING</span>
                        @elseif($p->status == 'completed')
                            <span class="badge badge-live">SELESAI</span>
                        @else
                            <span class="badge badge-sold">DITOLAK</span>
                        @endif
                    </td>
                    <td>
                        @if($p->proof_image_path)
                            <a href="{{ Storage::url($p->proof_image_path) }}" target="_blank" style="color: var(--accent); text-decoration: underline; font-size:13px;">Lihat Bukti</a>
                        @else
                            <span style="opacity: 0.5;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 32px; color:var(--text-muted);">Belum ada riwayat penarikan dana.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
