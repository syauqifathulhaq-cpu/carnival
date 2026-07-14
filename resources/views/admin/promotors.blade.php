@extends('components.layouts.admin')

@section('title', 'Verifikasi Promotor | Admin Carnival')

@section('content')
        <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Daftar Promotor</h2>
        
        <div class="dash-panels" style="display:block; margin-top:24px;"><div class="panel" style="padding:0; overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Perusahaan</th>
                        <th>Email PIC</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promotors as $promotor)
                    <tr>
                        <td>PRM-{{ 1000 + $promotor->id }}</td>
                        <td>{{ $promotor->company_name }}</td>
                        <td>{{ $promotor->user->email }}</td>
                        <td>{{ date('d M Y', strtotime($promotor->created_at)) }}</td>
                        <td>
                            <span style="padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; 
                                background: {{ $promotor->status_promotor == 'active' ? '#d4edda' : ($promotor->status_promotor == 'pending' ? '#fff3cd' : '#f8d7da') }}; 
                                color: {{ $promotor->status_promotor == 'active' ? '#155724' : ($promotor->status_promotor == 'pending' ? '#856404' : '#721c24') }};">
                                {{ ucfirst($promotor->status_promotor) }}
                            </span>
                        </td>
                        <td style="text-align: right; min-width: 250px;">
                            @if($promotor->status_promotor == 'pending')
                            <form action="{{ route('admin.promotor.approve', $promotor->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="font-size: 12px; padding: 4px 10px; margin-right: 4px;">Setujui</button>
                            </form>
                            @endif

                            <form action="{{ route('admin.promotors.toggle', $promotor->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; margin-right: 4px; border-color: {{ $promotor->status_promotor === 'active' ? '#ff3d7a' : 'var(--accent)' }}; color: {{ $promotor->status_promotor === 'active' ? '#ff3d7a' : 'var(--accent)' }};">
                                    {{ $promotor->status_promotor === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            
                            <a href="{{ route('admin.promotors.edit', $promotor->id) }}" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; border-color: var(--text-muted); color: var(--text-muted); margin-right: 4px;">Edit</a>

                            <form action="{{ route('admin.promotors.destroy', $promotor->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data promotor ini? Ini juga bisa memengaruhi event mereka.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; border-color: #ff3d7a; color: #ff3d7a;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table></div></div>
@endsection
