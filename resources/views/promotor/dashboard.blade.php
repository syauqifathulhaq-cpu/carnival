@extends('components.layouts.promotor')

@section('title', 'Dashboard Promotor | Carnival')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="stat-grid">
    <div class="stat-card">
        <div class="label">Total Penjualan</div>
        <div class="value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Tiket Terjual</div>
        <div class="value">{{ number_format($ticketsSold, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Event Aktif</div>
        <div class="value">{{ $events->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Status Akun</div>
        <div class="value" style="color: {{ $promotor->status_promotor == 'active' ? 'var(--accent)' : 'var(--accent-3)' }}; font-size: 24px;">
            {{ ucfirst($promotor->status_promotor) }}
        </div>
    </div>
</div>

<div class="dash-panels" style="margin-top: 40px; display:block;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Event Saya</h2>
        <a href="{{ route('promotor.events.create') }}" class="btn btn-primary btn-sm">+ BUAT EVENT BARU</a>
    </div>
    
    <div class="panel" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Nama Event</th>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td style="font-weight:700;">{{ $event->event_name }}</td>
                    <td><div class="badge">{{ date('d M Y', strtotime($event->event_date)) }}</div></td>
                    <td style="color:var(--text-muted); font-family:var(--mono); font-size:13px;">{{ $event->location }}</td>
                    <td>
                        @if($event->status_event == 'active')
                            <span class="badge badge-live">ACTIVE</span>
                        @else
                            <span class="badge badge-soon">{{ strtoupper($event->status_event) }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('promotor.events.tickets', $event->id) }}" title="Kelola Tiket" class="btn btn-outline btn-sm" style="padding:6px 12px; border-color:var(--accent-2); color:var(--accent-2);"><i class="fas fa-ticket-alt"></i></a>
                            <a href="{{ route('promotor.events.edit', $event->id) }}" title="Edit Event" class="btn btn-outline btn-sm" style="padding:6px 12px; border-color:var(--accent); color:var(--accent);"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('promotor.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini? Semua data tiket yang terjual akan hilang!');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Hapus Event" class="btn btn-outline btn-sm" style="padding:6px 12px; border-color:var(--accent-3); color:var(--accent-3);"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color:var(--text-muted); padding:32px;">Belum ada event yang dibuat.</td>
                </tr>
                @endforelse
            </tbody>
        </table></div></div>
</div>
@endsection
