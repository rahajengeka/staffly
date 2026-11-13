/**
 * assets/js/script.js
 * Client-Side Scripting: Validasi form JS + handler AJAX (jQuery / Axios)
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

'use strict';

/* ============================================================
   Utilitas: tampilkan / sembunyikan loading overlay
   ============================================================ */
function showLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.classList.add('active');
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.classList.remove('active');
}

/* ============================================================
   Utilitas: tampilkan notifikasi toast/alert di atas halaman
   ============================================================ */
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) return;

    const iconMap = {
        success: 'bi-check-circle-fill',
        danger:  'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info:    'bi-info-circle-fill',
    };

    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi ${iconMap[type] || 'bi-info-circle-fill'}"></i>
            <span>${message}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;

    alertContainer.insertAdjacentHTML('afterbegin', alertHtml);

    // Auto-dismiss setelah 4 detik
    setTimeout(() => {
        const alerts = alertContainer.querySelectorAll('.alert');
        if (alerts.length > 0) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerts[0]);
            bsAlert.close();
        }
    }, 4000);
}

/* ============================================================
   Validasi form tambah/edit pegawai
   ============================================================ */
function validatePegawaiForm(formElement) {
    const nipInput     = formElement.querySelector('[name="nip"]');
    const namaInput    = formElement.querySelector('[name="nama"]');
    const emailInput   = formElement.querySelector('[name="email"]');
    const jabatanInput = formElement.querySelector('[name="jabatan"]');

    let isValid = true;

    // Reset state validasi sebelumnya
    formElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    formElement.querySelectorAll('.invalid-feedback').forEach(el => (el.textContent = ''));

    function markInvalid(inputEl, message) {
        inputEl.classList.add('is-invalid');
        const feedback = inputEl.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        }
        isValid = false;
    }

    // Validasi NIP
    if (!nipInput || nipInput.value.trim() === '') {
        markInvalid(nipInput, 'NIP tidak boleh kosong.');
    } else if (!/^\d{15,20}$/.test(nipInput.value.trim())) {
        markInvalid(nipInput, 'NIP harus berupa angka (15-20 digit).');
    }

    // Validasi Nama
    if (!namaInput || namaInput.value.trim() === '') {
        markInvalid(namaInput, 'Nama lengkap tidak boleh kosong.');
    } else if (namaInput.value.trim().length < 3) {
        markInvalid(namaInput, 'Nama minimal 3 karakter.');
    }

    // Validasi Email (HTML5 regex)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailInput || emailInput.value.trim() === '') {
        markInvalid(emailInput, 'Email tidak boleh kosong.');
    } else if (!emailRegex.test(emailInput.value.trim())) {
        markInvalid(emailInput, 'Format email tidak valid (contoh: nama@domain.com).');
    }

    // Validasi Jabatan
    if (!jabatanInput || jabatanInput.value.trim() === '') {
        markInvalid(jabatanInput, 'Jabatan tidak boleh kosong.');
    }

    if (!isValid) {
        // Scroll ke field pertama yang invalid
        const firstInvalid = formElement.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
        }
    }

    return isValid;
}

/* ============================================================
   Validasi form login
   ============================================================ */
function validateLoginForm(formElement) {
    const usernameInput = formElement.querySelector('[name="username"]');
    const passwordInput = formElement.querySelector('[name="password"]');
    let isValid = true;

    formElement.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    if (!usernameInput || usernameInput.value.trim() === '') {
        usernameInput.classList.add('is-invalid');
        isValid = false;
    }

    if (!passwordInput || passwordInput.value.trim() === '') {
        passwordInput.classList.add('is-invalid');
        isValid = false;
    }

    return isValid;
}

/* ============================================================
   AJAX: hapus pegawai menggunakan jQuery $.ajax
   Parameter:
     pegawaiId  - ID pegawai yang akan dihapus
     rowElement - elemen <tr> yang akan dihapus dari DOM
   ============================================================ */
function hapusPegawai(pegawaiId, rowElement) {
    if (!confirm('Apakah Anda yakin ingin menghapus data pegawai ini?\nTindakan ini tidak dapat dibatalkan.')) {
        return;
    }

    showLoading();

    // Hitung path ke proses/proses_hapus.php relatif dari lokasi saat ini
    const basePath = document.querySelector('meta[name="base-path"]')?.content || '../';

    $.ajax({
        url:    basePath + 'proses/proses_hapus.php',
        method: 'POST',
        data:   { id: pegawaiId },
        dataType: 'json',
        success: function (response) {
            hideLoading();
            if (response.status === 'success') {
                // Hapus baris tabel secara halus tanpa reload halaman
                $(rowElement).fadeOut(400, function () {
                    $(this).remove();
                    // Perbarui nomor urut tabel
                    updateRowNumbers();
                    showAlert(response.message || 'Data pegawai berhasil dihapus.', 'success');
                });
            } else {
                showAlert(response.message || 'Gagal menghapus data pegawai.', 'danger');
            }
        },
        error: function (xhr, status, error) {
            hideLoading();
            console.error('AJAX Error:', status, error);
            showAlert('Terjadi kesalahan pada server. Silakan coba lagi.', 'danger');
        }
    });
}

/* ============================================================
   Perbarui nomor urut tabel setelah penghapusan baris
   ============================================================ */
function updateRowNumbers() {
    const rows = document.querySelectorAll('#tabelPegawai tbody tr');
    rows.forEach((row, index) => {
        const nomorCell = row.querySelector('.row-number');
        if (nomorCell) nomorCell.textContent = index + 1;
    });
}

/* ============================================================
   Inisialisasi semua event listener setelah DOM siap
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {

    // --- Form Tambah/Edit Pegawai ---
    const formPegawai = document.getElementById('formPegawai');
    if (formPegawai) {
        formPegawai.addEventListener('submit', function (e) {
            if (!validatePegawaiForm(this)) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                showLoading();
            }
        });
    }

    // --- Form Login ---
    const formLogin = document.getElementById('formLogin');
    if (formLogin) {
        formLogin.addEventListener('submit', function (e) {
            if (!validateLoginForm(this)) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                showLoading();
            }
        });

        // Toggle visibilitas password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput  = document.getElementById('password');
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const isText = passwordInput.type === 'text';
                passwordInput.type = isText ? 'password' : 'text';
                this.querySelector('i').classList.toggle('bi-eye');
                this.querySelector('i').classList.toggle('bi-eye-slash');
            });
        }
    }

    // --- Auto-dismiss alert bawaan dari session flash ---
    const sessionAlerts = document.querySelectorAll('.session-alert');
    sessionAlerts.forEach(alertEl => {
        setTimeout(() => {
            if (alertEl) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                bsAlert.close();
            }
        }, 5000);
    });

    // --- Tooltip Bootstrap ---
    const tooltipTriggers = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggers.forEach(el => new bootstrap.Tooltip(el));

    // --- Animasi muncul untuk kartu (Intersection Observer) ---
    const animCards = document.querySelectorAll('.anim-on-scroll');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        animCards.forEach(card => observer.observe(card));
    }

});

