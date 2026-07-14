@extends('components.layouts.promotor')

@section('title', 'Pengaturan Akun | Promotor Carnival')

@section('content')
<div class="dash-topbar" style="margin-bottom: 24px;">
    <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Pengaturan Profil Promotor</h2>
</div>
        
<div class="form-card" style="max-width: 600px;">
    <form action="#" method="POST">
        @csrf
        <div class="field" style="margin-bottom: 20px;">
            <label>Nama Perusahaan</label>
            <input type="text" name="company_name" value="{{ $promotor->company_name }}" required>
        </div>
        
        <div class="field" style="margin-bottom: 20px;">
            <label>Email Penanggung Jawab</label>
            <input type="email" value="{{ Auth::user()->email }}" readonly style="opacity:0.6; cursor:not-allowed;">
        </div>
        
        <div class="field" style="margin-bottom: 32px;">
            <label>Dokumen Izin Usaha Baru (Opsional)</label>
            <input type="file" name="license_doc" accept=".pdf,.png,.jpg">
        </div>
        
        <button type="submit" class="btn btn-primary">SIMPAN PERUBAHAN</button>
    </form>
</div>
@endsection
