@extends('components.layouts.admin')

@section('title', 'Kelola Pengguna | Admin Carnival')

@section('content')
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="margin: 0;">Kelola Pengguna</h1>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="margin-right: 10px;">+ Tambah Pengguna</a>
                <button class="btn btn-outline">Unduh Data (CSV)</button>
            </div>
        </div>
        
        <div class="dash-panels" style="display:block; margin-top:24px;"><div class="panel" style="padding:0; overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>No HP</th>
                        <th>Role</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>USR-{{ 1000 + $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone_number ?? '-' }}</td>
                        <td>
                            <span style="padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; 
                                background: {{ $user->role == 'admin' ? '#f8d7da' : ($user->role == 'promotor' ? '#cce5ff' : '#e2e3e5') }}; 
                                color: {{ $user->role == 'admin' ? '#721c24' : ($user->role == 'promotor' ? '#004085' : '#383d41') }};">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>{{ date('d M Y', strtotime($user->created_at)) }}</td>
                        <td>
                            @if($user->is_active)
                                <span class="badge" style="background:rgba(212,255,63,.15); color:var(--accent);">Aktif</span>
                            @else
                                <span class="badge" style="background:rgba(255,61,122,.15); color:#ff3d7a;">Nonaktif</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; margin-right: 4px; border-color: {{ $user->is_active ? '#ff3d7a' : 'var(--accent)' }}; color: {{ $user->is_active ? '#ff3d7a' : 'var(--accent)' }};">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px; border-color: var(--text-muted); color: var(--text-muted);">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
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
