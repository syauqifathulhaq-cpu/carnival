@extends('components.layouts.promotor')

@section('title', 'Edit Event | Promotor Carnival')

@section('content')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<div class="dash-topbar" style="margin-bottom: 24px;">
    <h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Edit Event Konser</h2>
    <a href="{{ route('promotor.events.index') }}" class="btn btn-outline btn-sm">Kembali</a>
</div>

<div class="form-card" style="max-width: 800px;">
    <form action="{{ route('promotor.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="field" style="margin-bottom: 16px;">
            <label>Nama Konser / Event</label>
            <input type="text" name="event_name" value="{{ $event->event_name }}" required>
        </div>

        <div class="field" style="margin-bottom: 16px;">
            <label>Kategori Event</label>
            <select name="event_category" required>
                <option value="Musik" {{ $event->event_category == 'Musik' ? 'selected' : '' }}>Musik</option>
                <option value="Seminar" {{ $event->event_category == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                <option value="Teater" {{ $event->event_category == 'Teater' ? 'selected' : '' }}>Teater & Seni</option>
                <option value="Olahraga" {{ $event->event_category == 'Olahraga' ? 'selected' : '' }}>Olahraga</option>
                <option value="Lainnya" {{ $event->event_category == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 8px;">
            <label style="font-size:12px; text-transform:uppercase; letter-spacing:.05em; color:var(--text-muted); font-weight:700;">Lokasi Event</label>
            <button type="button" onclick="openMapModal()" class="btn btn-outline btn-sm">📍 Pilih dari Peta</button>
        </div>

        <div class="form-row">
            <div class="field">
                <label>Kota</label>
                <input type="text" id="city_input" name="city" value="{{ $event->city }}" required>
            </div>
            <div class="field">
                <label>Lokasi / Venue</label>
                <input type="text" id="location_input" name="location" value="{{ $event->location }}" required>
            </div>
        </div>
        
        <!-- Hidden inputs for Coordinates -->
        <input type="hidden" id="latitude_input" name="latitude" value="{{ $event->latitude }}">
        <input type="hidden" id="longitude_input" name="longitude" value="{{ $event->longitude }}">
        <input type="hidden" name="maps_url" value="">
        
        <div class="form-row">
            <div class="field">
                <label>Tanggal & Waktu Acara</label>
                <input type="datetime-local" name="event_date" value="{{ date('Y-m-d\TH:i', strtotime($event->event_date)) }}" required>
            </div>
            <div class="field">
                <label>Waktu Buka Penjualan Tiket (Opsional)</label>
                <input type="datetime-local" name="sale_start_date" value="{{ $event->sale_start_date ? date('Y-m-d\TH:i', strtotime($event->sale_start_date)) : '' }}">
                <small style="opacity: 0.6; font-size:11px;">Kosongkan jika bisa langsung dibeli.</small>
            </div>
        </div>

        <div class="field" style="margin-bottom: 16px;">
            <label>Deskripsi Konser</label>
            <textarea name="description" rows="4" style="background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:12px 14px; color:var(--text); font-family:var(--body); font-size:14px; width:100%;">{{ $event->description }}</textarea>
        </div>

        <div class="form-row" style="margin-bottom: 32px;">
            <div class="field">
                <label>Max Tiket Per NIK</label>
                <input type="number" name="max_tickets_per_nik" value="{{ $event->max_tickets_per_nik }}" min="1" required>
            </div>
            <div class="field">
                <label>Poster/Banner Baru (Opsional)</label>
                @if($event->banner_image_path)
                    <div style="margin-bottom: 8px;">
                        <img src="{{ Storage::url($event->banner_image_path) }}" alt="Banner saat ini" style="max-height: 100px; border-radius: 8px; border: 1px solid var(--border);">
                    </div>
                @endif
                <input type="file" name="banner_image" accept="image/*" style="margin-bottom: 8px;">
                
                <label style="margin-top: 16px;">Denah Kursi / Seatmap Baru (Opsional)</label>
                @if($event->seatmap_image_path)
                    <div style="margin-bottom: 8px;">
                        <img src="{{ Storage::url($event->seatmap_image_path) }}" alt="Seatmap saat ini" style="max-height: 100px; border-radius: 8px; border: 1px solid var(--border);">
                    </div>
                @endif
                <input type="file" name="seatmap_image" accept="image/*">
                <small style="opacity: 0.6; font-size:11px;">Kosongkan jika tidak ingin mengubah gambar.</small>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">SIMPAN PERUBAHAN</button>
    </form>
</div>

<!-- Map Modal -->
<div id="mapModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter:blur(5px); z-index: 1000; align-items: center; justify-content: center;">
    <div class="form-card" style="width: 90%; max-width: 800px; padding:0; display: flex; flex-direction: column;">
        <div style="padding: 16px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-family:var(--body); font-size:18px;">Pilih Lokasi di Peta</h3>
            <button type="button" onclick="closeMapModal()" style="background: transparent; border: none; font-size: 24px; cursor: pointer; color:var(--text);">&times;</button>
        </div>
        <div style="padding: 24px;">
            <p style="margin-bottom: 12px; font-size: 13px; color: var(--text-muted);">Cari lokasi atau klik pada peta untuk menetapkan titik.</p>
            <div style="display: flex; gap: 8px; margin-bottom: 16px;">
                <input type="text" id="mapSearchInput" placeholder="Cari nama tempat, stadion, atau kota..." style="flex: 1; background:var(--bg-elevated); border:1px solid var(--border); border-radius:8px; padding:12px 14px; color:var(--text);">
                <button type="button" onclick="searchLocationOnMap()" class="btn btn-outline">Cari</button>
            </div>
            <div id="leafletMap" style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid var(--border); z-index:1;"></div>
            <div id="mapLoading" style="display: none; color: var(--accent); margin-top: 8px; font-size: 13px;">Mencari alamat...</div>
        </div>
        <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" onclick="closeMapModal()" class="btn btn-outline">Batal</button>
            <button type="button" onclick="confirmLocation()" class="btn btn-primary">Gunakan Lokasi Ini</button>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
    let map = null;
    let marker = null;
    let selectedLat = document.getElementById('latitude_input').value;
    let selectedLng = document.getElementById('longitude_input').value;
    let selectedCity = '';
    let selectedVenue = '';

    function openMapModal() {
        document.getElementById('mapModal').style.display = 'flex';
        
        if (!map) {
            let initialLat = selectedLat ? parseFloat(selectedLat) : -6.2088;
            let initialLng = selectedLng ? parseFloat(selectedLng) : 106.8456;
            
            map = L.map('leafletMap').setView([initialLat, initialLng], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            if (selectedLat && selectedLng) {
                setMarker(initialLat, initialLng);
            }

            map.on('click', function(e) {
                setMarker(e.latlng.lat, e.latlng.lng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });
        }
        
        setTimeout(() => {
            map.invalidateSize();
        }, 100);
    }

    function closeMapModal() {
        document.getElementById('mapModal').style.display = 'none';
    }

    function setMarker(lat, lng) {
        selectedLat = lat;
        selectedLng = lng;
        
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng]).addTo(map);
    }

    function reverseGeocode(lat, lng) {
        document.getElementById('mapLoading').style.display = 'block';
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('mapLoading').style.display = 'none';
                
                if (data && data.address) {
                    selectedCity = data.address.city || data.address.town || data.address.municipality || data.address.county || data.address.state || '';
                    selectedVenue = data.address.amenity || data.address.building || data.address.leisure || data.address.tourism || data.address.shop || data.name || data.address.road || '';
                    
                    if (marker) {
                        marker.bindPopup(`<b>${selectedVenue}</b><br>${selectedCity}`).openPopup();
                    }
                }
            })
            .catch(error => {
                console.error("Geocoding failed:", error);
                document.getElementById('mapLoading').style.display = 'none';
            });
    }

    function searchLocationOnMap() {
        let query = document.getElementById('mapSearchInput').value;
        if (!query) return;
        
        document.getElementById('mapLoading').style.display = 'block';
        document.getElementById('mapLoading').innerText = 'Mencari lokasi...';
        
        fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    let lat = data[0].lat;
                    let lon = data[0].lon;
                    
                    map.setView([lat, lon], 16);
                    setMarker(lat, lon);
                    reverseGeocode(lat, lon);
                } else {
                    alert('Lokasi tidak ditemukan. Coba kata kunci yang lebih spesifik.');
                    document.getElementById('mapLoading').style.display = 'none';
                }
            })
            .catch(error => {
                console.error("Search failed:", error);
                document.getElementById('mapLoading').style.display = 'none';
            });
    }

    function confirmLocation() {
        if (selectedLat && selectedLng) {
            document.getElementById('latitude_input').value = selectedLat;
            document.getElementById('longitude_input').value = selectedLng;
            
            if (selectedCity) {
                document.getElementById('city_input').value = selectedCity;
            }
            if (selectedVenue) {
                document.getElementById('location_input').value = selectedVenue;
            }
        }
        closeMapModal();
    }
</script>
@endsection
