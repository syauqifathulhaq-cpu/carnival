@extends('components.layouts.admin')

@section('title', 'Edit Event | Admin Carnival')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="margin: 0; font-family:var(--display); font-size:24px; text-transform:uppercase;">Edit Event</h1>
    <a href="{{ route('admin.events') }}" class="btn btn-outline">Kembali</a>
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

        <form action="{{ route('admin.events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; margin-bottom: 8px; color: var(--text-muted); font-size: 14px;">Promotor Penyelenggara</label>
                <select name="promotor_id" required style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; padding:16px 14px; color:var(--text); font-family:var(--body); font-size:14px; outline:none;">
                    @foreach($promotors as $p)
                        <option value="{{ $p->id }}" {{ old('promotor_id', $event->promotor_id) == $p->id ? 'selected' : '' }}>{{ $p->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field-float">
                <input type="text" name="event_name" id="event_name" placeholder=" " required value="{{ old('event_name', $event->event_name) }}">
                <label for="event_name">Nama Event</label>
            </div>
            
            <div class="field-float">
                <input type="text" name="location" id="location" placeholder=" " required value="{{ old('location', $event->location) }}">
                <label for="location">Lokasi (Kota / Venue)</label>
            </div>
            
            <div class="field-float">
                <input type="datetime-local" name="event_date" id="event_date" placeholder=" " required value="{{ old('event_date', date('Y-m-d\TH:i', strtotime($event->event_date))) }}">
                <label for="event_date">Tanggal & Waktu Event</label>
            </div>
            
            <div class="field-float">
                <input type="number" name="max_tickets_per_nik" id="max_tickets_per_nik" placeholder=" " required min="1" value="{{ old('max_tickets_per_nik', $event->max_tickets_per_nik) }}">
                <label for="max_tickets_per_nik">Maksimal Tiket per NIK</label>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; margin-bottom: 8px; color: var(--text-muted); font-size: 14px;">Status Event</label>
                <select name="status_event" required style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; padding:16px 14px; color:var(--text); font-family:var(--body); font-size:14px; outline:none;">
                    <option value="draft" {{ old('status_event', $event->status_event) == 'draft' ? 'selected' : '' }}>Draft (Sembunyikan)</option>
                    <option value="active" {{ old('status_event', $event->status_event) == 'active' ? 'selected' : '' }}>Aktif (Publikasikan)</option>
                    <option value="completed" {{ old('status_event', $event->status_event) == 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; margin-bottom: 8px; color: var(--text-muted); font-size: 14px;">Deskripsi Singkat</label>
                <textarea name="description" rows="4" style="width:100%; background:var(--bg-elevated); border:1px solid var(--border); border-radius:10px; padding:16px 14px; color:var(--text); font-family:var(--body); font-size:14px; outline:none;">{{ old('description', $event->description) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">SIMPAN PERUBAHAN</button>
        </form>
    </div>
</div>
@endsection
