@extends('components.layouts.app')

@section('title', 'Pengaturan Profil | Carnival')

@section('content')
<div class="container" style="max-width: 800px; margin: 3rem auto; padding: 0 2rem;">
    
    <h1 style="font-size: 2rem; color: var(--primary-color); margin-bottom: 2rem;">Pengaturan Profil</h1>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div style="background: var(--card-bg-light); padding: 2rem; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);" class="dark-mode-card">
        <form action="{{ route('pembeli.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text);">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text);">
                @error('name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text);">Nomor HP</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text);">
                @error('phone_number') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text);">Email <span style="font-size:0.8rem; opacity:0.6; font-weight:normal;">(Tidak dapat diubah)</span></label>
                <input type="email" value="{{ $user->email }}" disabled style="width: 100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px; background: rgba(0,0,0,0.05); color: var(--text-muted); cursor: not-allowed;">
            </div>

            <hr style="border: none; border-top: 1px solid var(--border); margin: 2rem 0;">

            <h3 style="margin-bottom: 1rem; color: var(--text);">Ubah Kata Sandi <span style="font-size:0.9rem; opacity:0.6; font-weight:normal;">(Kosongkan jika tidak ingin mengubah)</span></h3>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text);">Kata Sandi Baru</label>
                <input type="password" name="password" class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text);">
                @error('password') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text);">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" class="form-input" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text);">
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem; border-radius: 8px; width: 100%; font-weight: bold; font-size: 1.1rem;">Simpan Perubahan</button>
        </form>
    </div>
</div>

<style>
    body.dark-mode .dark-mode-card {
        background: var(--card-bg-dark);
        box-shadow: 0 4px 20px rgba(0,0,0,0.3) !important;
    }
</style>
@endsection
