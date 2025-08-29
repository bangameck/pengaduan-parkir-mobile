/**
 * ===================================================================
 * File Javascript Utama Aplikasi
 * ===================================================================
 * 1. Bootstrap: Memuat Axios & Alpine.js (untuk dropdown, dll).
 * 2. Library Eksternal: Memuat Lity, SweetAlert2, dan FilePond.
 * 3. Logika Custom: Menjalankan semua script custom kita setelah
 * halaman siap.
 */

// Alpine.start();
// -------------------------------------------------------------------
// BAGIAN 1: BOOTSTRAP (WAJIB PALING ATAS)
// -------------------------------------------------------------------
import './bootstrap';
// -------------------------------------------------------------------
// BAGIAN 2: IMPOR SEMUA LIBRARY & CSS
// -------------------------------------------------------------------

// Lity (Lightbox untuk pop-up gambar)
import lity from 'lity';
import 'lity/dist/lity.min.css';

// SweetAlert2 (Untuk notifikasi toast dan modal konfirmasi)
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// FilePond (Untuk upload file yang canggih)
import * as FilePond from 'filepond';
import 'filepond/dist/filepond.min.css';

// Plugin-plugin FilePond
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import FilePondPluginMediaPreview from 'filepond-plugin-media-preview';
import 'filepond-plugin-media-preview/dist/filepond-plugin-media-preview.min.css';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageCrop from 'filepond-plugin-image-crop';
import FilePondPluginImageTransform from 'filepond-plugin-image-transform';



// -------------------------------------------------------------------
// BAGIAN 3: KONFIGURASI GLOBAL
// -------------------------------------------------------------------

// Membuat SweetAlert2 bisa diakses dari script di file Blade
window.Swal = Swal;

// Mendaftarkan semua plugin FilePond yang sudah kita impor
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginMediaPreview,
    FilePondPluginFileValidateType,
    FilePondPluginImageCrop,
    FilePondPluginImageTransform
);


// -------------------------------------------------------------------
// BAGIAN 4: LOGIKA CUSTOM APLIKASI
// -------------------------------------------------------------------

// Menjalankan semua script yang berinteraksi dengan DOM setelah halaman siap
document.addEventListener('DOMContentLoaded', function () {

    // --- Inisialisasi FilePond untuk semua input dengan class .filepond ---
    const residentReportInput = document.querySelector('input[id="images"]');
    if (residentReportInput) {
        const pond = FilePond.create(residentReportInput, {
            storeAsFile: true,
            allowMultiple: true,
            acceptedFileTypes: ['image/png', 'image/jpeg', 'video/mp4', 'video/quicktime'],
            labelIdle: `Seret & Lepas foto/video atau <span class="filepond--label-action">Pilih File</span>`,
        });

        // Kirim event progress ke window agar bisa ditangkap Alpine.js
        pond.on('processfilestart', () => {
            window.dispatchEvent(new CustomEvent('filepond-processing-start'));
        });
        pond.on('processprogress', (file, progress) => {
            window.dispatchEvent(new CustomEvent('filepond-progress', { detail: { progress: Math.round(progress * 100) } }));
        });
        pond.on('processfiles', () => {
            window.dispatchEvent(new CustomEvent('filepond-progress', { detail: { progress: 100 } }));
        });
    }


    // --- Animasi Loading untuk Form Laporan Resident ---
    const residentForm = document.querySelector('form[action*="/laporan"]');
    if (residentForm && residentForm.querySelector('#submit-laporan')) {
        const submitButton = residentForm.querySelector('#submit-laporan');
        const spinner = submitButton.querySelector('#loading-spinner');
        const buttonText = submitButton.querySelector('#button-text');
        const progressContainer = document.getElementById('progress-container');
        const progressBar = document.getElementById('progress-bar');

        residentForm.addEventListener('submit', function () {
            submitButton.disabled = true;
            if (spinner) spinner.classList.remove('hidden');
            if (buttonText) buttonText.textContent = 'Mengirim...';

            if (progressContainer && progressBar) {
                progressContainer.classList.remove('hidden');
                let width = 0;
                const interval = setInterval(function () {
                    if (width >= 95) {
                        clearInterval(interval);
                    } else {
                        width += 5;
                        progressBar.style.width = width + '%';
                    }
                }, 100);
            }
        });
    }

    // --- Animasi Loading untuk Form Tindak Lanjut Field Officer ---
    const followUpForm = document.getElementById('follow-up-form');
    if (followUpForm) {
        const submitButton = followUpForm.querySelector('#submit-btn');
        const spinner = followUpForm.querySelector('#loading-spinner');
        const buttonText = followUpForm.querySelector('#button-text');

        followUpForm.addEventListener('submit', function () {
            if (submitButton && spinner && buttonText) {
                submitButton.disabled = true;
                spinner.classList.remove('hidden');
                buttonText.textContent = 'Menyimpan...';
            }
        });
    }

    // === LOGIKA BARU UNTUK MEMICU POP-UP MEDIA PLAYER ===
    document.body.addEventListener('click', function (event) {
        const link = event.target.closest('.js-media-viewer-trigger');

        if (link) {
            event.preventDefault();

            // Siapkan data detail dari link yang diklik
            const detail = {
                url: link.href,
                type: link.dataset.mediaType
            };

            // Kirim/Dispatch event custom 'open-media-viewer'
            // Komponen Alpine di layout akan 'mendengar' event ini
            window.dispatchEvent(new CustomEvent('open-media-viewer', { detail }));
        }
    });

    document.body.addEventListener('click', function (event) {
        if (event.target.closest('.js-logout-btn')) {
            event.preventDefault();

            Swal.fire({
                title: 'Anda yakin ingin logout?',
                text: "Anda akan keluar dari sesi ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Cari form logout terdekat dan submit
                    const logoutForm = document.getElementById('logout-form');
                    if (logoutForm) {
                        logoutForm.submit();
                    }
                }
            });
        }
    });

});
