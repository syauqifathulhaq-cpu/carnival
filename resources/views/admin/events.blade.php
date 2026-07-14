@extends('components.layouts.admin')

@section('title', 'Kelola Event | Admin Carnival')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="margin: 0; font-family:var(--display); font-size:24px; text-transform:uppercase;">Kelola Event</h1>
    <div>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">+ Tambah Event</a>
    </div>
</div>
        
<div class="dash-panels" style="display:block;">
    <div class="panel" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Event</th>
                    <th>Promotor</th>
                    <th>Tanggal & Lokasi</th>
                    <th>Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td style="font-family:var(--mono); color:var(--text-muted);">#{{ $event->id }}</td>
                    <td style="font-weight: bold;">{{ $event->event_name }}</td>
                    <td style="color:var(--accent);">{{ $event->promotor->company_name ?? 'N/A' }}</td>
                    <td>
                        <div style="font-size:13px; margin-bottom:4px;">{{ date('d M Y', strtotime($event->event_date)) }}</div>
                        <div style="color:var(--text-muted); font-size:12px;">{{ $event->city }} - {{ $event->venue }}</div>
                    </td>
                    <td>
                        @if($event->status_event === 'active')
                            <span class="badge" style="background:rgba(212,255,63,.15); color:var(--accent);">AKTIF</span>
                        @else
                            <span class="badge" style="background:rgba(244,243,240,.1); color:var(--text-muted);">{{ strtoupper($event->status_event) }}</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <form action="{{ route('admin.events.toggle', $event->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; margin-right: 4px; border-color: {{ $event->status_event === 'active' ? '#ff3d7a' : 'var(--accent)' }}; color: {{ $event->status_event === 'active' ? '#ff3d7a' : 'var(--accent)' }};">
                                {{ $event->status_event === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; border-color: var(--text-muted); color: var(--text-muted);">Edit</a>
                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; border-color: #ff3d7a; color: #ff3d7a;">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; opacity: 0.7;">Belum ada event yang didaftarkan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
