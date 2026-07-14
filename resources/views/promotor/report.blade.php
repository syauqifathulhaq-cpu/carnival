@extends('components.layouts.promotor')

@section('title', 'Laporan Penjualan | Promotor Carnival')

@section('content')
<div class="dash-topbar" style="margin-bottom: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase; margin:0;">Laporan Penjualan</h2>
        <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> DOWNLOAD LAPORAN</button>
    </div>
    
    <style>
        @media print {
            body * { visibility: hidden; }
            .dash-content, .dash-content * { visibility: visible; }
            .dash-content { position: absolute; left: 0; top: 0; width: 100%; padding:0; margin:0; }
            .btn { display: none !important; }
        }
    </style>

    <div class="dash-panels" style="display:block;">
        @if($reportData->isEmpty())
            <h3 style="font-family:var(--body); font-size:18px; margin-bottom:12px;">Data Belum Tersedia</h3>
            <p style="color:var(--text-muted);">Data grafik penjualan tiket (harian/bulanan) akan ditampilkan di sini setelah ada transaksi yang masuk.</p>
        @else
            <table style="width: 100%; text-align: left; border-collapse: collapse; margin-top: 16px;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border); color: var(--text-muted);">
                        <th style="padding: 12px 16px;">Nama Event</th>
                        <th style="padding: 12px 16px;">Tanggal</th>
                        <th style="padding: 12px 16px; text-align: center;">Tiket Terjual</th>
                        <th style="padding: 12px 16px; text-align: right;">Total Pendapatan (Gross)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData as $row)
                        <tr style="border-bottom: 1px solid rgba(244,243,240,0.05);">
                            <td style="padding: 16px; font-weight: bold;">{{ $row->event_name }}</td>
                            <td style="padding: 16px; color: var(--text-muted);">{{ date('d M Y', strtotime($row->event_date)) }}</td>
                            <td style="padding: 16px; text-align: center;">
                                <span class="badge badge-live" style="background: rgba(212,255,63,0.1); color: var(--accent);">
                                    {{ $row->total_tickets_sold }} Tiket
                                </span>
                            </td>
                            <td style="padding: 16px; text-align: right; font-family: var(--mono); color: var(--accent);">
                                Rp {{ number_format($row->total_revenue, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
</div>
@endsection
