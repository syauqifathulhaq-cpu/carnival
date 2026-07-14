@extends('components.layouts.promotor')

@section('title', 'Data Pengunjung | Promotor Carnival')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="dash-panels" style="display:block;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-family:var(--display); font-size:32px; text-transform:uppercase; margin-bottom:4px;">Data Scan QR</h1>
            <p style="color:var(--text-muted); margin:0;">Event: <strong style="color:var(--text);">{{ $event->event_name }}</strong></p>
        </div>
        <a href="{{ route('promotor.events.index') }}" class="btn btn-outline btn-sm">&larr; KEMBALI</a>
    </div>
    
    <div class="panel" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Pengunjung</th>
                    <th>NIK</th>
                    <th>Nomor HP</th>
                    <th>Kategori Tiket</th>
                    <th>Waktu Scan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $index => $ticket)
                <tr>
                    <td style="text-align: center; color:var(--text-muted);">{{ $index + 1 }}</td>
                    <td style="font-weight:700;">{{ $ticket->name_holder }}</td>
                    <td style="font-family:var(--mono); color:var(--text-muted);">{{ $ticket->nik_holder }}</td>
                    <td style="font-family:var(--mono);">{{ $ticket->phone_holder ?? '-' }}</td>
                    <td><div class="badge" style="background:rgba(212,255,63,0.1); color:var(--accent);">{{ $ticket->ticketCategory->category_name }}</div></td>
                    <td style="font-family:var(--mono); font-size:13px;">{{ date('d M Y, H:i', strtotime($ticket->checked_in_at)) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color:var(--text-muted); padding:40px;">
                        <div style="font-size:32px; margin-bottom:12px; opacity:0.3;"><i class="fas fa-qrcode"></i></div>
                        Belum ada tiket yang di-scan (check-in) untuk event ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
