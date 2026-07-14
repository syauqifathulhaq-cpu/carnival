@extends('components.layouts.admin')

@section('title', 'Laporan Keuangan | Admin Carnival')

@section('content')
<div class="dash-topbar" style="margin-bottom: 24px;">
    <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Laporan Keuangan (Platform)</h2>
</div>
        
<div class="stat-grid">
    <div class="stat-card">
        <div class="label" style="color:var(--accent);">Total Transaksi Masuk</div>
        <div class="value" style="color:var(--accent);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Platform Fee (5%)</div>
        <div class="value">Rp {{ number_format($totalRevenue * 0.05, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="label" style="color:var(--accent-2);">Payout Promotor</div>
        <div class="value" style="color:var(--accent-2);">Rp {{ number_format($totalRevenue * 0.95, 0, ',', '.') }}</div>
    </div>
</div>
        
<div class="dash-panels" style="display:block; margin-top:24px;">
    <div class="panel" style="padding:0; overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Pembeli</th>
                    <th>Event</th>
                    <th>Promotor</th>
                    <th>Jml Tiket</th>
                    <th>Gross Total</th>
                    <th>Fee (5%)</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td style="font-weight: bold; color: var(--accent);">{{ $trx->invoice_number }}</td>
                    <td>{{ $trx->user->name ?? 'User' }}</td>
                    <td style="color:var(--text-muted); font-size:13px;">
                        @php
                            $eventNames = $trx->tickets->pluck('ticketCategory.event.event_name')->unique()->implode(', ');
                        @endphp
                        {{ $eventNames }}
                    </td>
                    <td style="font-weight:700;">
                        @php
                            $promotors = $trx->tickets->pluck('ticketCategory.event.promotor.company_name')->unique()->implode(', ');
                        @endphp
                        {{ $promotors }}
                    </td>
                    <td>{{ $trx->tickets->count() }}</td>
                    <td>Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td style="color: var(--accent-3);">Rp {{ number_format($trx->total_price * 0.05, 0, ',', '.') }}</td>
                    <td><div class="badge">{{ date('d M Y, H:i', strtotime($trx->transaction_date)) }}</div></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 2rem; opacity: 0.7;">Belum ada data transaksi di platform ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
