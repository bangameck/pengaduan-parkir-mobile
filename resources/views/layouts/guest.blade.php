<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SiParkirKita - Kota Pekanbaru') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
            integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />

        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

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
                /* overflow: hidden; Dihapus untuk memperbaiki tooltip */
            }

            .auth-left::before,
            .auth-left::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.08);
                z-index: 1;
                /* Lapisan Latar */
            }

            .auth-left::before {
                top: -50px;
                right: -50px;
                width: 200px;
                height: 200px;
            }

            .auth-left::after {
                bottom: -80px;
                left: -80px;
                width: 300px;
                height: 300px;
            }

            .auth-left-content {
                position: relative;
                z-index: 2;
                /* Lapisan Konten, di atas dekorasi */
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
                padding: 5px;
            }

            .app-logo-icon img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
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

            .social-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: #f3f4f6;
                color: #4b5563;
                font-size: 20px;
                text-decoration: none;
                transition: all 0.3s ease-in-out;
            }

            .social-icon:hover {
                color: #ffffff;
                transform: scale(1.1);
            }

            @media (max-width: 768px) {
                .auth-container {
                    flex-direction: column;
                }

                .auth-left {
                    display: none;
                }

                .auth-right {
                    padding: 2rem 1.5rem;
                }
            }
        </style>

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
        <div class="auth-container">
            <div class="auth-left">
                <div class="auth-left-content">
                    <div class="app-logo">
                        <div class="app-logo-icon">
                            <img src="{{ asset('logo-parkir.png') }}" alt="Logo SiParkirKita">
                        </div>
                        <div class="app-logo-text">SiParkirKita</div>
                    </div>
                    <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem;">Layanan Pengaduan Perparkiran
                    </h2>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p style="opacity: 0.9;">Kota Pekanbaru</p>
                    </div>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M15.428 4.25a.75.75 0 01.286.672l-1.5 9.75a.75.75 0 01-1.428-.218L13.5 9.75h-3.379a.75.75 0 01-.564-.22l-1.75-2.5a.75.75 0 01.99-.085l1.83 1.22H13.5a.75.75 0 010 1.5H9.75a.75.75 0 01-.634-.322l-1.32-2.31A.75.75 0 018.25 6h5.828l.214-1.393a.75.75 0 01.786-.607zM6.5 4.75a.75.75 0 01.75-.75h2.5a.75.75 0 010 1.5h-2.5a.75.75 0 01-.75-.75z"
                                        clip-rule="evenodd" />
                                </svg></div>
                            <span>Pengaduan parkir lebih mudah & terpusat</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 5.168A.75.75 0 008 5.75v5.306l-1.11-1.332a.75.75 0 00-1.111.922l1.75 2.1a.75.75 0 00.56.273h3.5a.75.75 0 000-1.5H9.555V5.75a.75.75 0 00-.75-.75g"
                                        clip-rule="evenodd" />
                                </svg></div>
                            <span>Respon cepat & penanganan real-time</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M2.5 3A1.5 1.5 0 001 4.5v11A1.5 1.5 0 002.5 17h15a1.5 1.5 0 001.5-1.5v-11A1.5 1.5 0 0017.5 3h-15zM3.5 6a.5.5 0 01.5-.5h12a.5.5 0 010 1h-12a.5.5 0 01-.5-.5zM12 9a1 1 0 11-2 0 1 1 0 012 0zM5 9a1 1 0 11-2 0 1 1 0 012 0zM17 9a1 1 0 11-2 0 1 1 0 012 0zM12 12a1 1 0 11-2 0 1 1 0 012 0zM5 12a1 1 0 11-2 0 1 1 0 012 0zM17 12a1 1 0 11-2 0 1 1 0 012 0z" />
                                </svg></div>
                            <span>Transparansi laporan & tindak lanjut</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M7 8a3 3 0 100-6 3 3 0 000 6zM14.5 9a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM4.057 12.164a4.502 4.502 0 00-1.304 1.348l-.29.351A2.5 2.5 0 004.29 17.5h11.42a2.5 2.5 0 001.826-3.637l-.29-.351a4.502 4.502 0 00-1.304-1.348C13.21 11.25 10.1 11 7 11c-3.1 0-6.21.25-8.943 1.164z" />
                                </svg></div>
                            <span>Dukungan masyarakat & pemerintah</span>
                        </div>
                    </div>
                    <div class="mt-12 pt-6 border-t border-gray-200/50">
                        <h3 class="text-center font-semibold text-white-600/80 mb-4">Ikuti Kami</h3>
                        <div class="flex justify-center gap-4">
                            <a href="#" target="_blank" data-tippy-content="WhatsApp"
                                class="social-icon hover:bg-green-500"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" target="_blank" data-tippy-content="Instagram"
                                class="social-icon hover:bg-gradient-to-br hover:from-pink-500 hover:via-red-500 hover:to-yellow-500"><i
                                    class="fab fa-instagram"></i></a>
                            <a href="#" target="_blank" data-tippy-content="TikTok"
                                class="social-icon hover:bg-black"><i class="fab fa-tiktok"></i></a>
                            <a href="#" target="_blank" data-tippy-content="X / Twitter"
                                class="social-icon hover:bg-black"><i class="fab fa-x-twitter"></i></a>
                            <a href="#" target="_blank" data-tippy-content="Facebook"
                                class="social-icon hover:bg-blue-600"><i class="fab fa-facebook-f"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="auth-right">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://unpkg.com/@popperjs/core@2"></script>
        <script src="https://unpkg.com/tippy.js@6"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                tippy('[data-tippy-content]', {
                    animation: 'scale-extreme',
                    theme: 'translucent',
                });
            });
        </script>
    </body>

</html>
