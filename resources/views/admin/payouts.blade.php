@extends('components.layouts.admin')

@section('title', 'Pencairan Dana (Payout) | Admin Carnival')

@section('content')
<div class="dash-topbar" style="margin-bottom: 24px;">
    <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Pencairan Dana Promotor (Payout)</h2>
</div>

@if(session('success'))
    <div style="background: rgba(212,255,63,.15); color: var(--accent); padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid var(--accent);">
        ✅ {{ session('success') }}
    </div>
@endif

<div class="dash-panels" style="display:block;">
    <!-- Pending Payouts -->
    <div class="panel" style="padding:0; overflow-x:auto; margin-bottom: 32px;">
        <h3 style="font-family:var(--body); text-transform:none; margin-bottom:0; padding:24px 24px 16px; color:var(--accent-2);">⏳ Menunggu Pencairan (Pending)</h3>
        
        <table>
            <thead>
                <tr>
                    <th>Tgl Request</th>
                    <th>Promotor</th>
                    <th>Nominal</th>
                    <th>Rekening Tujuan</th>
                    <th>Aksi / Upload Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingPayouts as $p)
                <tr>
                    <td><div class="badge">{{ $p->created_at->format('d M Y, H:i') }}</div></td>
                    <td style="font-weight:700;">{{ $p->promotor->company_name }}</td>
                    <td style="font-weight: bold; color: var(--accent);">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td style="font-size:13px; color:var(--text-muted); line-height:1.4;">
                        <strong style="color: var(--text);">{{ $p->bank_name }} - {{ $p->account_number }}</strong><br>
                        A.N. {{ $p->account_name }}
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; align-items: stretch;">
                            <form action="{{ route('admin.payouts.approve', $p->id) }}" method="POST" enctype="multipart/form-data" style="display: flex; gap: 8px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: var(--bg-elevated);">
                                @csrf
                                <input type="file" name="proof_image" required accept="image/*" style="font-size: 11px; width: 140px; color:var(--text-muted);">
                                <button type="submit" class="btn btn-primary btn-sm" style="padding: 6px 12px;">Approve</button>
                            </form>
                            <form action="{{ route('admin.payouts.reject', $p->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm" style="height:100%; border-color:var(--accent-3); color:var(--accent-3);" onclick="return confirm('Tolak permintaan ini?')">Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 32px; color:var(--text-muted);">Tidak ada permintaan pencairan dana yang pending.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- History -->
    <div class="panel" style="padding:0; overflow-x:auto;">
        <h3 style="font-family:var(--body); text-transform:none; margin-bottom:0; padding:24px 24px 16px;">Riwayat Pencairan</h3>
        <table>
            <thead>
                <tr>
                    <th>Tgl Disetujui/Ditolak</th>
                    <th>Promotor</th>
                    <th>Nominal</th>
                    <th>Status</th>
                    <th>Bukti Transfer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historyPayouts as $p)
                <tr>
                    <td><div class="badge">{{ $p->updated_at->format('d M Y, H:i') }}</div></td>
                    <td style="font-weight:700;">{{ $p->promotor->company_name }}</td>
                    <td style="font-weight: bold; color:var(--text);">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                    <td>
                        @if($p->status == 'completed')
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
                    <td colspan="5" style="text-align: center; padding: 32px; color:var(--text-muted);">Belum ada riwayat pencairan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
