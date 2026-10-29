<div class="custom-login-page">
<style>
    :root {
        color-scheme: light;
    }

    .custom-login-shell {
        display: grid;
        grid-template-columns: 55% 45%;
        min-height: 100vh;
        width: 100%;
        overflow: hidden;
        background: #fff;
        color: #18212f;
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .custom-login-brand {
        position: relative;
        display: flex;
        min-height: 100vh;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        padding: clamp(40px, 7vw, 104px);
        isolation: isolate;
        background: #0d1117;
        color: #f8fafc;
    }

    .custom-login-brand::before,
    .custom-login-brand::after {
        position: absolute;
        z-index: -1;
        width: 68vw;
        height: 68vw;
        border-radius: 50%;
        content: "";
        filter: blur(88px);
        opacity: .42;
        pointer-events: none;
        animation: aurora-drift 18s ease-in-out infinite alternate;
    }

    .custom-login-brand::before {
        top: -34%;
        left: -20%;
        background: radial-gradient(circle, #7B3F98 0%, rgba(123, 63, 152, .18) 42%, transparent 70%);
    }

    .custom-login-brand::after {
        right: -24%;
        bottom: -42%;
        background: radial-gradient(circle, #108A44 0%, rgba(16, 138, 68, .18) 42%, transparent 70%);
        animation-delay: -9s;
    }

    .custom-login-aurora {
        position: absolute;
        z-index: -1;
        inset: 12% -15%;
        background:
            radial-gradient(ellipse at 68% 32%, rgba(123, 63, 152, .22), transparent 42%),
            radial-gradient(ellipse at 30% 72%, rgba(16, 138, 68, .16), transparent 38%);
        filter: blur(34px);
        animation: mesh-pulse 12s ease-in-out infinite alternate;
        pointer-events: none;
    }

    .custom-login-brand-content {
        width: min(660px, 100%);
        margin: auto;
    }

    .custom-login-logo {
        display: block;
        width: min(340px, 80%);
        height: auto;
        margin-bottom: clamp(44px, 8vh, 84px);
        object-fit: contain;
        object-position: left center;
    }

    .custom-login-eyebrow {
        margin: 0 0 14px;
        color: #a78bb8;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
    }

    .custom-login-brand h1 {
        max-width: 620px;
        margin: 0;
        color: #f8fafc;
        font-size: clamp(30px, 4vw, 54px);
        font-weight: 700;
        letter-spacing: -.045em;
        line-height: 1.12;
    }

    .custom-login-tagline {
        max-width: 530px;
        margin: 20px 0 0;
        color: #aeb8c6;
        font-size: clamp(15px, 1.5vw, 18px);
        line-height: 1.7;
    }

    .custom-login-features {
        display: grid;
        gap: 18px;
        margin: clamp(36px, 6vh, 64px) 0 0;
        padding: 0;
        list-style: none;
    }

    .custom-login-features li {
        display: flex;
        align-items: center;
        gap: 14px;
        color: #e2e8f0;
        font-size: 14px;
        font-weight: 500;
    }

    .custom-login-feature-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid rgba(167, 139, 184, .28);
        border-radius: 11px;
        background: rgba(123, 63, 152, .12);
        color: #c59cda;
    }

    .custom-login-feature-icon svg {
        width: 19px;
        height: 19px;
    }

    .custom-login-panel {
        display: flex;
        min-height: 100vh;
        align-items: center;
        justify-content: center;
        padding: clamp(28px, 6vw, 88px);
        background: #0d131f !important;
        color: #f8fafc !important;
    }

    .custom-login-card {
        width: min(420px, 100%);
    }

    .custom-login-mobile-logo {
        display: none;
        width: min(240px, 72%);
        height: auto;
        margin: 0 auto 34px;
    }

    .custom-login-title {
        margin: 0;
        color: #f8fafc !important;
        font-size: clamp(27px, 3vw, 34px);
        font-weight: 700;
        letter-spacing: -.04em;
        line-height: 1.2;
    }

    .custom-login-subtitle {
        margin: 10px 0 32px;
        color: #cbd5e1 !important;
        font-size: 14px;
        line-height: 1.6;
    }

    .custom-login-form {
        position: relative;
        color-scheme: dark;
    }

    .custom-login-loading {
        position: absolute;
        z-index: 5;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 14px;
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 12px;
        background: rgba(13, 19, 31, .96);
        color: #f8fafc;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        backdrop-filter: blur(6px);
    }

    .custom-login-loader-orbit {
        position: relative;
        width: 76px;
        height: 76px;
        border: 1px dashed rgba(203, 213, 225, .4);
        border-radius: 50%;
        animation: asset-orbit 4s linear infinite;
    }

    .custom-login-loader-orbit::before {
        position: absolute;
        inset: 23px;
        border: 2px solid rgba(123, 63, 152, .9);
        border-radius: 8px;
        background: linear-gradient(145deg, #7B3F98, #108A44);
        box-shadow: 0 0 18px rgba(123, 63, 152, .38);
        content: "";
        animation: asset-core-pulse 1.2s ease-in-out infinite alternate;
    }

    .custom-login-loader-object {
        position: absolute;
        display: grid;
        width: 30px;
        height: 30px;
        place-items: center;
        border: 1px solid rgba(203, 213, 225, .26);
        border-radius: 9px;
        background: #182333;
        color: #d7b4e8;
        box-shadow: 0 5px 14px rgba(0, 0, 0, .24);
        animation: asset-object-float 1.4s ease-in-out infinite alternate;
    }

    .custom-login-loader-object svg {
        width: 17px;
        height: 17px;
    }

    .custom-login-loader-object:nth-child(1) {
        top: -8px;
        left: 23px;
    }

    .custom-login-loader-object:nth-child(2) {
        right: -8px;
        bottom: 8px;
        color: #a7e4bf;
        animation-delay: -.45s;
    }

    .custom-login-loader-object:nth-child(3) {
        bottom: 8px;
        left: -8px;
        animation-delay: -.9s;
    }

    .custom-login-form .fi-fo-field-wrp-label,
    .custom-login-form .fi-fo-field-wrp-label span,
    .custom-login-form .fi-checkbox-label,
    .custom-login-form .fi-checkbox-label span {
        color: #f1f5f9 !important;
    }

    .custom-login-form .fi-input-wrp {
        border-color: #39465a !important;
        background-color: #111827 !important;
        color: #f8fafc !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .18);
    }

    .custom-login-form .fi-input,
    .custom-login-form .fi-input-wrp input,
    .custom-login-form input {
        background-color: #111827 !important;
        color: #f8fafc !important;
        color-scheme: dark;
    }

    .custom-login-form .fi-input::placeholder,
    .custom-login-form input::placeholder {
        color: #9caabd !important;
    }

    .custom-login-form .fi-checkbox-input {
        border-color: #64748b !important;
        background-color: #111827 !important;
    }

    .custom-login-form .fi-btn {
        min-height: 48px;
        border: 0 !important;
        border-radius: 10px;
        background: linear-gradient(110deg, #7B3F98 0%, #108A44 100%) !important;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(91, 69, 125, .22);
        transition: transform 160ms ease, filter 160ms ease, box-shadow 160ms ease;
    }

    .custom-login-form .fi-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.06);
        box-shadow: 0 11px 22px rgba(91, 69, 125, .28);
    }

    .custom-login-form .fi-btn:disabled {
        cursor: wait;
        opacity: .72;
    }

    .custom-login-form a {
        color: #d0a6e5 !important;
    }

    .custom-login-form .fi-fo-field-wrp-error-message {
        color: #fca5a5 !important;
    }

    .custom-login-footer {
        margin: 34px 0 0;
        color: #aab5c5 !important;
        font-size: 11px;
        text-align: center;
    }

    @keyframes aurora-drift {
        from { transform: translate3d(-2%, -1%, 0) scale(.92); }
        to { transform: translate3d(9%, 8%, 0) scale(1.12); }
    }

    @keyframes mesh-pulse {
        from { transform: translate3d(-2%, 1%, 0) scale(.95); opacity: .66; }
        to { transform: translate3d(3%, -2%, 0) scale(1.08); opacity: 1; }
    }

    @keyframes asset-orbit {
        to { transform: rotate(360deg); }
    }

    @keyframes asset-core-pulse {
        to { transform: scale(.78) rotate(45deg); opacity: .72; }
    }

    @keyframes asset-object-float {
        to { margin-top: -5px; }
    }

    @media (max-width: 900px) {
        .custom-login-shell {
            grid-template-columns: 48% 52%;
        }

        .custom-login-brand {
            padding: 42px;
        }
    }

    @media (max-width: 700px) {
        .custom-login-shell {
            display: block;
            min-height: 100vh;
            overflow: auto;
        }

        .custom-login-brand {
            display: none;
        }

        .custom-login-panel {
            min-height: 100vh;
            padding: 36px 24px;
        }

        .custom-login-mobile-logo {
            display: block;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .custom-login-brand::before,
        .custom-login-brand::after,
        .custom-login-aurora {
            animation: none;
        }

        .custom-login-loader-orbit,
        .custom-login-loader-orbit::before,
        .custom-login-loader-object {
            animation: none;
        }

        .custom-login-form .fi-btn {
            transition: none;
        }
    }
</style>

<main class="custom-login-shell">
    <section class="custom-login-brand" aria-label="Informasi perusahaan">
        <div class="custom-login-aurora" aria-hidden="true"></div>

        <div class="custom-login-brand-content">
            <img class="custom-login-logo" src="{{ asset('images/logo-pkm.png') }}" alt="Logo PT Pakuan Karya Mandiri">
            <p class="custom-login-eyebrow">PT Pakuan Karya Mandiri</p>
            <h1>Kelola aset dengan lebih terarah.</h1>
            <p class="custom-login-tagline">Sistem Manajemen Aset &amp; Logistik Terpadu</p>

            <ul class="custom-login-features">
                <li>
                    <span class="custom-login-feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12s3.2-6 9-6 9 6 9 6-3.2 6-9 6-9-6-9-6Z"></path>
                            <circle cx="12" cy="12" r="2.5"></circle>
                        </svg>
                    </span>
                    <span>Pelacakan Real-Time</span>
                </li>
                <li>
                    <span class="custom-login-feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"></path>
                            <path d="M14 3v5h5M8 14l2.5 2.5L16 11"></path>
                        </svg>
                    </span>
                    <span>Akuntabilitas BAST</span>
                </li>
                <li>
                    <span class="custom-login-feature-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19V5m0 14h17"></path>
                            <path d="m7 15 4-4 3 2 5-6"></path>
                            <path d="M16 7h3v3"></path>
                        </svg>
                    </span>
                    <span>Kalkulasi Nilai Depresiasi</span>
                </li>
            </ul>
        </div>
    </section>

    <section class="custom-login-panel" aria-label="Formulir login">
        <div class="custom-login-card">
            <img class="custom-login-mobile-logo" src="{{ asset('images/logo-pkm.png') }}" alt="Logo PT Pakuan Karya Mandiri">
            <h2 class="custom-login-title">Selamat datang kembali</h2>
            <p class="custom-login-subtitle">Masuk untuk melanjutkan ke portal manajemen aset dan logistik.</p>

            <div class="custom-login-form">
                {{ $this->content }}

                <div
                    class="custom-login-loading"
                    wire:loading.flex
                    wire:target="authenticate"
                    role="status"
                    aria-live="polite"
                >
                    <span class="custom-login-loader-orbit" aria-hidden="true">
                        <span class="custom-login-loader-object">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="13" rx="2"></rect>
                                <path d="M8 21h8m-4-4v4"></path>
                            </svg>
                        </span>
                        <span class="custom-login-loader-object">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 7 9-4 9 4-9 4-9-4Z"></path>
                                <path d="M3 7v10l9 4 9-4V7M12 11v10"></path>
                            </svg>
                        </span>
                        <span class="custom-login-loader-object">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 20V9l8-5 8 5v11H4Z"></path>
                                <path d="M9 20v-6h6v6M8 10h.01M16 10h.01"></path>
                            </svg>
                        </span>
                    </span>
                    <span>Memproses login...</span>
                </div>
            </div>

            <p class="custom-login-footer">© 2026 PT Pakuan Karya Mandiri. All rights reserved.</p>
        </div>
    </section>
</main>
</div>
