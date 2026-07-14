@extends('components.layouts.promotor')

@section('title', 'Transaksi | Promotor Carnival')

@section('content')
        <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Daftar Transaksi Tiket</h2>
        
        <div class="dash-panels" style="display:block; margin-top:24px;"><div class="panel" style="padding:0; overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pembeli</th>
                        <th>Event</th>
                        <th>Jml Tiket</th>
                        <th>Total Bayar</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td style="font-weight: bold; color: var(--accent);">{{ $trx->invoice_number }}</td>
                        <td>{{ $trx->user->name ?? 'User' }}</td>
                        <td>
                            @php
                                $eventNames = $trx->tickets->pluck('ticketCategory.event.event_name')->unique()->implode(', ');
                            @endphp
                            {{ $eventNames }}
                        </td>
                        <td>{{ $trx->tickets->count() }}</td>
                        <td>Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                        <td>{{ date('d M Y, H:i', strtotime($trx->transaction_date)) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; opacity: 0.7;">Belum ada transaksi pembelian tiket pada event Anda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div></div>
@endsection
