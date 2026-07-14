@extends('components.layouts.promotor')

@section('title', 'Scanner Tiket | Promotor Carnival')

@section('content')
<h2 style="font-family:var(--display); font-size:24px; text-transform:uppercase;">Scanner Tiket</h2>
<p style="color:var(--text-muted); margin-bottom: 2rem;">Arahkan kamera ke QR Code pada tiket pengunjung.</p>

    <div class="dash-panels" style="display:flex; gap:24px; align-items:flex-start; flex-wrap:wrap;">
    <div class="panel" style="text-align: center; flex: 1;">
        
        <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto; border-radius: 10px; overflow: hidden; border: 2px solid var(--border);"></div>

        <div id="scan-result" style="margin-top: 2rem; padding: 1rem; border-radius: 8px; display: none;">
            <h3 id="result-status" style="margin: 0 0 10px 0; font-family: var(--display); font-size: 20px;"></h3>
            <p id="result-message" style="margin: 0; font-size: 16px;"></p>
            <div id="result-info" style="margin-top: 10px; font-size: 14px; opacity: 0.8; display: none;">
                <p style="margin:2px 0;"><strong>Nama:</strong> <span id="info-name"></span></p>
                <p style="margin:2px 0;"><strong>Acara:</strong> <span id="info-event"></span></p>
                <p style="margin:2px 0;"><strong>Tipe Tiket:</strong> <span id="info-category"></span></p>
            </div>
        </div>
    </div>
    
    <div class="panel" style="flex: 1;">
        <h3 style="font-family:var(--display); font-size:18px; margin-bottom:16px;">Riwayat Scan Sesi Ini</h3>
        <div style="overflow-x:auto;">
            <table id="history-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="history-body">
                    <tr>
                        <td colspan="4" style="text-align:center; color:var(--text-muted); padding:20px;">Belum ada tiket yang di-scan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<style>
    /* Styling for the Html5QrcodeScanner UI */
    #reader button {
        background: var(--accent);
        color: #000;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-family: var(--body);
        font-weight: bold;
        cursor: pointer;
        margin: 5px;
    }
    #reader select {
        padding: 8px;
        border-radius: 8px;
        background: var(--bg-body);
        color: var(--text);
        border: 1px solid var(--border);
        margin: 5px;
    }
    #reader a { color: var(--accent); }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const resultBox = document.getElementById('scan-result');
        const statusText = document.getElementById('result-status');
        const messageText = document.getElementById('result-message');
        const infoBox = document.getElementById('result-info');
        
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return; // Prevent multiple scans at once
            isProcessing = true;

            // Optional: Pause scanner temporarily
            try {
                html5QrcodeScanner.pause(true);
            } catch (err) {
                console.log('Scanner not in active video state, skipping pause.');
            }

            // Show processing
            resultBox.style.display = 'block';
            resultBox.style.background = 'var(--bg-elevated)';
            resultBox.style.color = 'var(--text)';
            statusText.innerText = "Memverifikasi...";
            messageText.innerText = "Mohon tunggu...";
            infoBox.style.display = 'none';

            fetch("{{ route('promotor.scanner.verify') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qr_payload: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                let timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                let histBody = document.getElementById('history-body');
                
                // Hapus placeholder teks jika ada
                if(histBody.innerHTML.includes('Belum ada tiket')) {
                    histBody.innerHTML = '';
                }

                if(data.status === 'success') {
                    resultBox.style.background = 'rgba(40, 167, 69, 0.1)';
                    resultBox.style.color = '#28a745';
                    statusText.innerText = "BERHASIL!";
                    
                    Swal.fire({
                        title: 'Check-in Berhasil!',
                        html: `<b>${data.ticket_info.name}</b><br>${data.ticket_info.category}`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    let row = `<tr><td>${timeStr}</td><td style="font-weight:bold;">${data.ticket_info.name}</td><td>${data.ticket_info.category}</td><td><span class="badge badge-live">SUKSES</span></td></tr>`;
                    histBody.insertAdjacentHTML('afterbegin', row);
                    
                } else if(data.status === 'warning') {
                    resultBox.style.background = 'rgba(255, 193, 7, 0.1)';
                    resultBox.style.color = '#ffc107'; 
                    statusText.innerText = "PERINGATAN!";
                    
                    Swal.fire({
                        title: 'Sudah Check-in!',
                        text: data.message,
                        icon: 'warning',
                        confirmButtonColor: '#ffc107'
                    });
                    
                    if(data.ticket_info) {
                        let row = `<tr><td>${timeStr}</td><td style="font-weight:bold;">${data.ticket_info.name}</td><td>${data.ticket_info.category}</td><td><span class="badge badge-soon">DUPLIKAT</span></td></tr>`;
                        histBody.insertAdjacentHTML('afterbegin', row);
                    }
                } else {
                    resultBox.style.background = 'rgba(255, 61, 122, 0.1)';
                    resultBox.style.color = '#ff3d7a';
                    statusText.innerText = "GAGAL!";
                    
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#ff3d7a'
                    });
                }

                messageText.innerText = data.message;

                if(data.ticket_info) {
                    infoBox.style.display = 'block';
                    document.getElementById('info-name').innerText = data.ticket_info.name;
                    document.getElementById('info-event').innerText = data.ticket_info.event;
                    document.getElementById('info-category').innerText = data.ticket_info.category;
                }

                // Jangan hide kotak resultBox secara otomatis jika scan lewat file, biar infonya tetep kelihatan
                setTimeout(() => {
                    isProcessing = false;
                    try { html5QrcodeScanner.resume(); } catch(e) {}
                }, 2000);

            })
            .catch(error => {
                resultBox.style.background = 'rgba(255, 61, 122, 0.1)';
                resultBox.style.color = '#ff3d7a';
                statusText.innerText = "ERROR SERVER!";
                messageText.innerText = "Gagal menghubungi server.";
                
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
                
                setTimeout(() => {
                    isProcessing = false;
                    try { html5QrcodeScanner.resume(); } catch(e) {}
                }, 2000);
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false);
        html5QrcodeScanner.render(onScanSuccess);
    });
</script>
@endsection
