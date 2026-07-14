@extends('components.layouts.app')

@section('title', 'Daftar | Carnival')

@section('content')
<style>
    .footer { display: none !important; }
</style>
<div class="auth-shell">
    <div class="auth-visual">
      <div class="blob b1"></div>
      <div class="blob b2"></div>
      <div>
        <div class="kicker">CARNIVAL<span style="color:var(--accent)">.</span> 2026</div>
        <div class="headline">Satu akun,<br>akses ke semua panggung.</div>
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

      <div class="marquee">
        <span>
            @if(isset($events) && $events->count() > 0)
                @foreach($events as $event)
                    {{ strtoupper($event->event_name) }} &nbsp;•&nbsp; {{ strtoupper($event->city) }} &nbsp;•&nbsp; 
                @endforeach
                @foreach($events as $event)
                    {{ strtoupper($event->event_name) }} &nbsp;•&nbsp; {{ strtoupper($event->city) }} &nbsp;•&nbsp; 
                @endforeach
            @else
                3 PANGGUNG &nbsp;•&nbsp; 42 ARTIS &nbsp;•&nbsp; 22–24 AGUSTUS 2026 &nbsp;•&nbsp; PANTAI SEGARA, BALI &nbsp;•&nbsp; 3 PANGGUNG &nbsp;•&nbsp; 42 ARTIS &nbsp;•&nbsp; 22–24 AGUSTUS 2026 &nbsp;•&nbsp; PANTAI SEGARA, BALI &nbsp;•&nbsp;
            @endif
        </span>
      </div>
    </div>

    <div class="auth-panel">
      <div class="auth-card">
        <span class="eyebrow" id="authEyebrow">Selamat datang kembali</span>
        <h2 id="authHeadline">Masuk ke akunmu</h2>

        @if ($errors->any())
            <div style="color: var(--accent-3); background: rgba(255,61,122,0.1); padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-size: 13px; font-weight: 600; border:1px solid rgba(255,61,122,0.3);">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="auth-toggle">
          <div class="slider" id="authSlider"></div>
          <button type="button" id="tabLogin" class="active" onclick="setAuthMode('login')">Masuk</button>
          <button type="button" id="tabRegister" onclick="setAuthMode('register')">Daftar</button>
        </div>

        <!-- LOGIN FORM -->
        <form class="auth-form" id="formLogin" action="{{ route('auth.login.process') }}" method="POST">
          @csrf
          <div class="field-float">
            <input type="email" name="email" placeholder=" " required value="{{ old('email') }}">
            <label>Email</label>
          </div>
          <div class="field-float">
            <input type="password" name="password" placeholder=" " id="loginPass" required>
            <label>Kata Sandi</label>
            <button type="button" class="toggle-eye" onclick="togglePass('loginPass', this)">Lihat</button>
          </div>
          <div class="auth-row-between">
            <label class="check-inline"><input type="checkbox" name="remember"> Ingat saya</label>
            <a href="#">Lupa kata sandi?</a>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-auth">Masuk</button>
        </form>

        <!-- REGISTER FORM -->
        <form class="auth-form" id="formRegister" action="{{ route('auth.register.process') }}" method="POST">
          @csrf
          <div class="field-float">
            <input type="text" name="name" placeholder=" " required value="{{ old('name') }}">
            <label>Nama Lengkap</label>
          </div>
          <div class="field-float">
            <input type="text" name="nik" placeholder=" " required minlength="16" maxlength="16" value="{{ old('nik') }}">
            <label>NIK (Nomor Induk Kependudukan)</label>
          </div>
          <div class="field-float">
            <input type="text" name="phone_number" placeholder=" " required value="{{ old('phone_number') }}">
            <label>Nomor Telepon</label>
          </div>
          <div class="field-float">
            <input type="email" name="email" placeholder=" " required value="{{ old('email') }}">
            <label>Email</label>
          </div>
          <div class="field-float">
            <input type="password" name="password" placeholder=" " id="regPass" required>
            <label>Kata Sandi</label>
            <button type="button" class="toggle-eye" onclick="togglePass('regPass', this)">Lihat</button>
          </div>
          <div class="field-float">
            <input type="password" name="password_confirmation" placeholder=" " id="regPassConfirm" required>
            <label>Konfirmasi Kata Sandi</label>
            <button type="button" class="toggle-eye" onclick="togglePass('regPassConfirm', this)">Lihat</button>
          </div>
          <div class="auth-row-between" style="margin-top:2px;">
            <label class="check-inline"><input type="checkbox" required> Saya setuju dengan Syarat & Ketentuan</label>
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-auth">Buat Akun</button>
        </form>

        <div class="auth-divider">atau lanjutkan dengan</div>
        <div class="auth-alt">
          <button class="btn btn-outline btn-sm" style="flex:1;">Google</button>
          <button class="btn btn-outline btn-sm" style="flex:1;">Apple</button>
        </div>

        <div class="auth-switch-hint" id="authHint">
          Belum punya akun? <a href="{{ route('auth.register') }}">Daftar sekarang</a>
        </div>
      </div>
    </div>
</div>

<script>
function setAuthMode(mode){
  const slider = document.getElementById('authSlider');
  const tabLogin = document.getElementById('tabLogin');
  const tabRegister = document.getElementById('tabRegister');
  const formLogin = document.getElementById('formLogin');
  const formRegister = document.getElementById('formRegister');
  const eyebrow = document.getElementById('authEyebrow');
  const headline = document.getElementById('authHeadline');
  const hint = document.getElementById('authHint');

  if(mode === 'register'){
    slider.style.transform = 'translateX(100%)';
    tabLogin.classList.remove('active'); tabRegister.classList.add('active');
    formLogin.classList.remove('active'); formRegister.classList.add('active');
    eyebrow.textContent = 'Gabung di Carnival';
    headline.textContent = 'Buat akun baru';
    hint.innerHTML = 'Sudah punya akun? <a href="#" onclick="setAuthMode(\'login\'); return false;">Masuk di sini</a>';
  } else {
    slider.style.transform = 'translateX(0)';
    tabRegister.classList.remove('active'); tabLogin.classList.add('active');
    formRegister.classList.remove('active'); formLogin.classList.add('active');
    eyebrow.textContent = 'Selamat datang kembali';
    headline.textContent = 'Masuk ke akunmu';
    hint.innerHTML = 'Belum punya akun? <a href="#" onclick="setAuthMode(\'register\'); return false;">Daftar sekarang</a>';
  }
}

function togglePass(id, btn){
  const input = document.getElementById(id);
  const isPass = input.type === 'password';
  input.type = isPass ? 'text' : 'password';
  btn.textContent = isPass ? 'Sembunyikan' : 'Lihat';
}

document.addEventListener('DOMContentLoaded', () => {
    // If they have old email but no old name, it means they failed login. Else default to register.
    const mode = "{{ old('email') && !old('name') ? 'login' : 'register' }}";
    setAuthMode(mode);
});
</script>
@endsection
