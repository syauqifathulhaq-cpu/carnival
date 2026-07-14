@extends('components.layouts.promotor')

@section('title', 'Kelola Tiket | Carnival')

@section('content')
<div class="dash-panels" style="display:grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start;">
    
    <!-- Form Tambah Tiket -->
    <div class="form-card">
        <h3 style="font-family:var(--body); text-transform:none; margin-bottom:24px;">Tambah Kategori Tiket</h3>
        
        <form action="{{ route('promotor.events.tickets.store', $event->id) }}" method="POST">
            @csrf
            <div class="field" style="margin-bottom: 16px;">
                <label>Nama Kategori</label>
                <input type="text" name="category_name" required placeholder="Contoh: CAT 1 (Festival)">
            </div>
            <div class="field" style="margin-bottom: 16px;">
                <label>Harga (Rp)</label>
                <input type="number" name="price" required min="0" placeholder="Contoh: 1500000">
            </div>
            <div class="field" style="margin-bottom: 32px;">
                <label>Kuota Tiket</label>
                <input type="number" name="total_quota" required min="1" placeholder="Jumlah maksimal yang dijual">
            </div>
            <button type="submit" class="btn btn-primary btn-block">TAMBAH TIKET</button>
        </form>
    </div>

    <!-- Daftar Tiket -->
    <div class="panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="font-family:var(--body); text-transform:none; margin:0;">Kategori Tiket: {{ $event->event_name }}</h3>
            <a href="{{ route('promotor.events.index') }}" class="btn btn-outline btn-sm">Selesai</a>
        </div>
        
        @if(session('success'))
            <div style="background: rgba(212,255,63,.15); color: var(--accent); padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid var(--accent);">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(255,61,122,.15); color: var(--accent-3); padding: 16px; border-radius: 8px; margin-bottom: 24px; border: 1px solid var(--accent-3);">
                ❌ {{ session('error') }}
            </div>
        @endif

        <div style="display: flex; flex-direction:column; gap: 16px;">
            @forelse($ticketCategories as $cat)
            <div style="border: 1px solid var(--border); background:var(--bg-elevated); border-radius: 12px; padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h4 style="font-family:var(--body); font-size:18px; margin-bottom: 4px; text-transform:none;">{{ $cat->category_name }}</h4>
                        <p style="color: var(--text-muted); font-size:13px; font-family:var(--mono);">Kuota: {{ $cat->remaining_quota }} / {{ $cat->total_quota }}</p>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 700; color: var(--accent); font-family:var(--mono); font-size: 18px; margin-bottom: 8px;">
                            Rp {{ number_format($cat->price, 0, ',', '.') }}
                        </div>
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button type="button" onclick="toggleEditForm({{ $cat->id }})" class="btn btn-outline btn-sm" style="padding: 4px 12px; font-size: 12px;">Edit</button>
                            <form action="{{ route('promotor.events.tickets.destroy', ['id' => $event->id, 'ticket_id' => $cat->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tiket ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-sm" style="padding: 4px 12px; font-size: 12px; border-color: var(--accent-3); color: var(--accent-3);">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Form Edit (Hidden by default) -->
                <div id="edit-form-{{ $cat->id }}" style="display: none; margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border);">
                    <form action="{{ route('promotor.events.tickets.update', ['id' => $event->id, 'ticket_id' => $cat->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-row" style="margin-bottom: 12px;">
                            <div class="field">
                                <label style="font-size: 12px;">Nama Kategori</label>
                                <input type="text" name="category_name" value="{{ $cat->category_name }}" required style="padding: 8px; font-size: 14px;">
                            </div>
                            <div class="field">
                                <label style="font-size: 12px;">Harga (Rp)</label>
                                <input type="number" name="price" value="{{ (int)$cat->price }}" required min="0" style="padding: 8px; font-size: 14px;">
                            </div>
                            <div class="field">
                                <label style="font-size: 12px;">Total Kuota</label>
                                <input type="number" name="total_quota" value="{{ $cat->total_quota }}" required min="{{ $cat->total_quota - $cat->remaining_quota }}" style="padding: 8px; font-size: 14px;">
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button type="button" onclick="toggleEditForm({{ $cat->id }})" class="btn btn-outline btn-sm">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align: center; color: var(--text-muted); padding: 32px; border:1px dashed var(--border); border-radius:12px;">
                Belum ada kategori tiket. Silakan tambah di form sebelah kiri.
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function toggleEditForm(id) {
        const form = document.getElementById('edit-form-' + id);
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>
@endsection
