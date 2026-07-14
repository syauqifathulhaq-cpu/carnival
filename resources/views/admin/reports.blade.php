@extends('components.layouts.admin')

@section('title', 'Laporan Platform | Admin Carnival')

@section('content')
<div class="dash-topbar" style="margin-bottom: 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase; margin:0;">Laporan Sistem</h2>
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
</div>
        
<div class="stat-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="label" style="color:var(--accent);">Total Tiket Terjual</div>
        <div class="value" style="color:var(--accent);">{{ number_format($totalTickets, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Pengguna Aktif</div>
        <div class="value">{{ number_format($totalUsers, 0, ',', '.') }}</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Mitra Promotor</div>
        <div class="value">{{ number_format($totalPromotors, 0, ',', '.') }}</div>
    </div>
</div>

<div class="dash-panels" style="display:block;">
    <div class="panel">
        <h3 style="margin-bottom: 16px; font-family:var(--mono); text-transform:uppercase; letter-spacing:.1em; font-size:14px; color:var(--text-muted);">Ringkasan Finansial All-Time</h3>
        
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:16px;">
            <div style="font-size:16px;">Total Gross Revenue (Semua Transaksi Sukses)</div>
            <div style="font-size:20px; font-weight:bold; color:var(--text);">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:16px;">
            <div style="font-size:16px;">Estimasi Platform Fee (5%)</div>
            <div style="font-size:20px; font-weight:bold; color:var(--accent);">Rp {{ number_format($platformFee, 0, ',', '.') }}</div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:16px;">Estimasi Payout ke Promotor (95%)</div>
            <div style="font-size:20px; font-weight:bold; color:var(--accent-2);">Rp {{ number_format($grossRevenue - $platformFee, 0, ',', '.') }}</div>
        </div>
    </div>
</div>
@endsection
