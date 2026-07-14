@extends('components.layouts.admin')

@section('title', 'Edit Promotor | Admin Carnival')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="margin: 0; font-family:var(--display); font-size:24px; text-transform:uppercase;">Edit Promotor: {{ $promotor->company_name }}</h1>
    <a href="{{ route('admin.promotors') }}" class="btn btn-outline">Kembali</a>
</div>

<div class="dash-panels" style="display:block; margin-top:24px;">
    <div class="panel">
        @if ($errors->any())
            <div style="background: rgba(255,61,122,.1); color: #ff3d7a; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.promotors.update', $promotor->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <h3 style="margin-bottom: 1rem; font-family: var(--display); color: var(--accent);">Data Perusahaan</h3>
            <div class="field-float">
                <input type="text" name="company_name" id="company_name" placeholder=" " required value="{{ old('company_name', $promotor->company_name) }}">
                <label for="company_name">Nama Perusahaan Promotor</label>
            </div>

            <hr style="border: 1px solid var(--border); margin: 2rem 0;">

            <h3 style="margin-bottom: 1rem; font-family: var(--display); color: var(--accent);">Data Akun Login (PIC)</h3>
            <div class="field-float">
                <input type="text" name="name" id="name" placeholder=" " required value="{{ old('name', $promotor->user->name) }}">
                <label for="name">Nama PIC Lengkap</label>
            </div>
            
            <div class="field-float">
                <input type="email" name="email" id="email" placeholder=" " required value="{{ old('email', $promotor->user->email) }}">
                <label for="email">Alamat Email Login</label>
            </div>
            
            <div class="field-float">
                <input type="password" name="password" id="password" placeholder=" ">
                <label for="password">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
            </div>
            
            <div class="field-float">
                <input type="text" name="phone_number" id="phone_number" placeholder=" " value="{{ old('phone_number', $promotor->user->phone_number) }}">
                <label for="phone_number">Nomor HP</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">SIMPAN PERUBAHAN PROMOTOR</button>
        </form>
    </div>
</div>
@endsection
