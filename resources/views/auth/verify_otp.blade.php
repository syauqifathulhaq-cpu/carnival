@extends('components.layouts.app')

@section('title', 'Verifikasi OTP | Carnival')

@section('content')
<style>
    .footer { display: none !important; }
    
    .otp-inputs {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
    }
    
    .otp-inputs input {
        width: 100%;
        height: 60px;
        background: var(--bg-elevated);
        border: 1px solid var(--border);
        border-radius: 12px;
        color: var(--text);
        font-family: var(--display);
        font-size: 24px;
        text-align: center;
        transition: border-color 0.2s;
    }
    
    .otp-inputs input:focus {
        border-color: var(--accent);
        outline: none;
    }
    
    /* Hide number arrows */
    .otp-inputs input::-webkit-outer-spin-button,
    .otp-inputs input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .mock-alert {
        background: rgba(212, 255, 63, 0.1);
        border: 1px solid rgba(212, 255, 63, 0.3);
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .mock-alert-text {
        font-size: 13px;
        color: var(--text-muted);
    }
    
    .mock-alert-code {
        font-family: var(--mono);
        font-size: 18px;
        font-weight: 700;
        color: var(--accent);
        letter-spacing: 2px;
    }
</style>

<div class="auth-shell">
    <div class="auth-visual">
        <div class="blob b1"></div>
        <div class="blob b2"></div>
        <div>
            <div class="kicker">CARNIVAL<span style="color:var(--accent)">.</span> 2026</div>
            <div class="headline">Satu langkah lagi<br>menuju panggung.</div>
        </div>

        <div class="wristband">
            <svg viewBox="0 0 220 70" fill="none">
                <rect x="4" y="14" width="212" height="42" rx="21" stroke="rgba(244,243,240,.5)" stroke-width="2"/>
                <circle cx="34" cy="35" r="10" fill="rgba(212,255,63,.9)"/>
                <rect x="58" y="27" width="100" height="6" rx="3" fill="rgba(244,243,240,.35)"/>
                <rect x="58" y="39" width="70" height="6" rx="3" fill="rgba(244,243,240,.2)"/>
                <line class="scan-line" x1="4" y1="14" x2="216" y2="14" stroke="var(--accent)" stroke-width="2"/>
            </svg>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <span class="eyebrow">Verifikasi Keamanan</span>
            <h2>Masukkan Kode OTP</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 24px; line-height: 1.5;">
                Kami telah mengirimkan 4 digit kode OTP ke nomor <strong>{{ $user->phone_number }}</strong>.
            </p>

            @if(session('mock_otp'))
            <div class="mock-alert">
                <div class="mock-alert-text">
                    [MODE PENGEMBANG]<br>Kode Mock OTP Anda:
                </div>
                <div class="mock-alert-code">{{ session('mock_otp') }}</div>
            </div>
            @endif

            @if($errors->any())
            <div style="color: var(--accent-3); background: rgba(255,61,122,0.1); padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 13px; font-weight: 600; border:1px solid rgba(255,61,122,0.3);">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('auth.verify.otp.process') }}" method="POST" id="otpForm">
                @csrf
                <input type="hidden" name="otp" id="otpValue" value="">
                
                <div class="otp-inputs">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" class="otp-box">
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" class="otp-box" disabled>
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" class="otp-box" disabled>
                    <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off" class="otp-box" disabled>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-auth" id="btnVerify" disabled>Verifikasi</button>
            </form>

            <form action="{{ route('auth.resend.otp') }}" method="POST" style="margin-top: 24px; text-align: center;">
                @csrf
                <div style="color: var(--text-muted); font-size: 13px;">
                    Belum menerima kode? 
                    <button type="submit" style="color: var(--accent); font-weight: 700; text-decoration: underline;">Kirim Ulang</button>
                </div>
            </form>
            
            <div style="margin-top: 24px; text-align: center;">
                 <a href="{{ route('auth.login') }}" style="color: var(--text-muted); font-size: 13px; font-weight: 600;">&larr; Kembali ke halaman Masuk</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.otp-box');
    const otpValue = document.getElementById('otpValue');
    const btnVerify = document.getElementById('btnVerify');

    function checkOtpFilled() {
        let val = '';
        inputs.forEach(input => val += input.value);
        otpValue.value = val;
        
        if(val.length === 4) {
            btnVerify.disabled = false;
        } else {
            btnVerify.disabled = true;
        }
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1) {
                if (index < inputs.length - 1) {
                    inputs[index + 1].disabled = false;
                    inputs[index + 1].focus();
                }
            }
            checkOtpFilled();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0) {
                if (index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = '';
                }
            }
            checkOtpFilled();
        });
        
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').slice(0, 4).replace(/\D/g, '');
            if(pastedData) {
                for(let i=0; i<pastedData.length; i++) {
                    if(i < inputs.length) {
                        inputs[i].disabled = false;
                        inputs[i].value = pastedData[i];
                    }
                }
                if (pastedData.length < 4) {
                    inputs[pastedData.length].disabled = false;
                    inputs[pastedData.length].focus();
                } else {
                    inputs[3].focus();
                }
                checkOtpFilled();
            }
        });
    });
});
</script>
@endsection
