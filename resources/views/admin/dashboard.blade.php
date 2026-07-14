@extends('components.layouts.admin')

@section('title', 'Dashboard Admin | Carnival')

@section('content')
<div class="stat-grid">
    <div class="stat-card">
        <div class="label">Total Promotor</div>
        <div class="value">{{ $totalPromotors }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Menunggu Verifikasi</div>
        <div class="value" style="color: var(--accent-3);">{{ $pendingPromotorsCount }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Pengguna</div>
        <div class="value">{{ $totalUsers }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Revenue Platform</div>
        <div class="value" style="color: var(--accent);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
    </div>
</div>

<div class="dash-panels" style="margin-top: 40px; display:block;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Permohonan Promotor Baru</h2>
    </div>

    @if(session('success'))
        <div style="background: rgba(212,255,63,.15); color: var(--accent); padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid var(--accent);">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="panel" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Perusahaan</th>
                    <th>Email PIC</th>
                    <th>Tanggal Daftar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendingPromotors as $promotor)
                <tr>
                    <td style="font-family:var(--mono);">PRM-{{ 1000 + $promotor->id }}</td>
                    <td style="font-weight:700;">{{ $promotor->company_name }}</td>
                    <td>{{ $promotor->user->email }}</td>
                    <td><div class="badge">{{ date('d M Y', strtotime($promotor->created_at)) }}</div></td>
                    <td><span class="badge badge-soon">MENUNGGU</span></td>
                    <td>
                        <form action="{{ route('admin.promotor.approve', $promotor->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" style="padding: 6px 12px; border-color:var(--accent); color:var(--accent);">Setujui</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color:var(--text-muted); padding:32px;">Tidak ada permohonan promotor baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table></div></div>
</div>
@endsection
