<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SiParkirKita - Kota Pekanbaru') }}</title>

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- SweetAlert2 -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            :root {
                --primary: #0058e1;
                --primary-dark: #0044b2;
                --secondary: #f59e0b;
                --light: #f3f4f6;
                --dark: #1f2937;
                --success: #10b981;
                --error: #ef4444;
            }

            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }

            .auth-container {
                display: flex;
                max-width: 1000px;
                width: 100%;
                border-radius: 1rem;
                overflow: hidden;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
                background: white;
            }

            .auth-left {
                flex: 1;
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                color: white;
                padding: 3rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }

            .auth-left::before {
                content: '';
                position: absolute;
                top: -50px;
                right: -50px;
                width: 200px;
                height: 200px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
            }

            .auth-left::after {
                content: '';
                position: absolute;
                bottom: -80px;
                left: -80px;
                width: 300px;
                height: 300px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
            }

            .auth-right {
                flex: 1;
                padding: 3rem 2rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .app-logo {
                display: flex;
                align-items: center;
                margin-bottom: 2rem;
            }

            .app-logo-icon {
                width: 50px;
                height: 50px;
                background: white;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 1rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                /* Tambahkan padding jika logo terlalu mepet */
                padding: 5px;
                /* Sesuaikan nilai ini jika perlu */
            }

            .app-logo-icon img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                /* Memastikan gambar pas di dalam tanpa terpotong */
            }

            .app-logo-text {
                font-weight: 700;
                font-size: 1.5rem;
            }

            .feature-list {
                margin-top: 2rem;
            }

            .feature-item {
                display: flex;
                align-items: center;
                margin-bottom: 1rem;
            }

            .feature-icon {
                width: 30px;
                height: 30px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 1rem;
            }

            .auth-title {
                font-size: 1.875rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                color: var(--dark);
            }

            .auth-subtitle {
                color: #6b7280;
                margin-bottom: 2rem;
            }

            .input-group {
                position: relative;
                margin-bottom: 1.5rem;
            }

            .input-field {
                width: 100%;
                padding: 1rem 1rem;
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                font-size: 1rem;
                transition: all 0.3s;
            }

            .input-field:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(0, 88, 225, 0.1);
            }

            .input-label {
                position: absolute;
                top: 1rem;
                left: 1rem;
                color: #6b7280;
                pointer-events: none;
                transition: all 0.3s;
            }

            .input-field:focus~.input-label,
            .input-field:not(:placeholder-shown)~.input-label {
                top: -0.5rem;
                left: 0.75rem;
                font-size: 0.75rem;
                background: white;
                padding: 0 0.5rem;
                color: var(--primary);
            }

            .toggle-password {
                position: absolute;
                right: 1rem;
                top: 1rem;
                color: #6b7280;
                cursor: pointer;
            }

            .remember-forgot {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin: 1.5rem 0;
            }

            .remember-me {
                display: flex;
                align-items: center;
            }

            .switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
                margin-right: 0.5rem;
            }

            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 24px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }

            input:checked+.slider {
                background-color: var(--primary);
            }

            input:checked+.slider:before {
                transform: translateX(26px);
            }

            .auth-link {
                color: var(--primary);
                text-decoration: none;
                font-weight: 500;
            }

            .auth-link:hover {
                text-decoration: underline;
            }

            .submit-btn {
                width: 100%;
                padding: 1rem;
                background: var(--primary);
                color: white;
                border: none;
                border-radius: 0.5rem;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.3s;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .submit-btn:hover {
                background: var(--primary-dark);
            }

            .submit-btn:disabled {
                opacity: 0.7;
                cursor: not-allowed;
            }

            .auth-footer {
                text-align: center;
                margin-top: 1.5rem;
                color: #6b7280;
            }

            @media (max-width: 768px) {
                .auth-container {
                    flex-direction: column;
                }

                .auth-left {
                    display: none;
                }
            }

            .spinner {
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }
        </style>

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
        <div class="auth-container">
            <div class="auth-left">
                <div class="app-logo">
                    <div class="app-logo-icon">
                        {{-- Mengganti icon Font Awesome dengan gambar logo --}}
                        <img src="{{ asset('logo-parkir.png') }}" alt="Logo SiParkirKita">
                    </div>
                    <div class="app-logo-text">SiParkirKita</div>
                </div>

                <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem;">Sistem Pengaduan Parkir Terpadu
                </h2>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-map-pin"></i>
                    </div>
                    <p style="opacity: 0.9;">Kota Pekanbaru</p>
                </div>

                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <span>Pengaduan parkir lebih mudah & terpusat</span>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span>Respon cepat & penanganan real-time</span>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span>Transparansi laporan & tindak lanjut</span>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span>Dukungan masyarakat & pemerintah</span>
                    </div>
                </div>
            </div>

            <div class="auth-right">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>

</html>
