<?php
/**
 * login.php
 * Halaman Login utama - Bertema "Creatix Digital Studio & HR Engine"
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$pesanError = '';
if (isset($_SESSION['flash_error'])) {
    $pesanError = htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['flash_error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-path" content="./">
    <title>Masuk &mdash; Creatix HR Engine</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Creatix Theme CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="text-center bg-dark p-4 rounded-4 border border-secondary shadow-lg">
        <div class="spinner-border text-success" style="width:2.5rem;height:2.5rem;" role="status">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <p class="mt-3 mb-0 text-white fw-bold small">Memverifikasi akun...</p>
    </div>
</div>

<div class="creatix-login-wrapper">
    <div class="creatix-login-card">

        <!-- Logo & Header -->
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center gap-2 mb-2">
                <span class="text-white fw-bold fs-3" style="letter-spacing:-0.03em;">CREATIX</span>
                <span class="creatix-brand-badge">HR ENGINE</span>
            </div>
            <p class="text-muted small mb-0">Masuk ke sistem pengelolaan data pegawai terpadu</p>
        </div>

        <!-- Alert Error -->
        <?php if ($pesanError): ?>
        <div class="alert alert-danger alert-dismissible fade show session-alert d-flex align-items-center gap-2 py-2 px-3 mb-4 rounded-3 border-0 shadow-sm" role="alert" style="background:#3b1820; color:#ff7b92;">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <div class="small fw-semibold"><?= $pesanError ?></div>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close" style="padding:0.75rem;"></button>
        </div>
        <?php endif; ?>

        <!-- Form Login -->
        <form id="formLogin" action="proses/proses_login.php" method="POST" novalidate>

            <!-- Username -->
            <div class="mb-3">
                <label for="username" class="form-label fw-bold small text-light">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-muted" style="border-radius:12px 0 0 12px; border: 1.5px solid var(--creatix-border); border-right: none;">
                        <i class="bi bi-person"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control border-start-0 ps-0 text-white"
                        id="username"
                        name="username"
                        placeholder="Ketik username admin"
                        autocomplete="username"
                        style="border-radius:0 12px 12px 0;"
                        autofocus
                    >
                </div>
                <div class="invalid-feedback small">Username wajib diisi.</div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="form-label fw-bold small text-light">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-muted" style="border-radius:12px 0 0 12px; border: 1.5px solid var(--creatix-border); border-right: none;">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input
                        type="password"
                        class="form-control border-start-0 border-end-0 ps-0 text-white"
                        id="password"
                        name="password"
                        placeholder="Ketik password"
                        autocomplete="current-password"
                    >
                    <button class="btn btn-outline-secondary border-start-0 bg-transparent" type="button" id="togglePassword" style="border-radius:0 12px 12px 0; border: 1.5px solid var(--creatix-border); border-left: none;">
                        <i class="bi bi-eye text-muted"></i>
                    </button>
                </div>
                <div class="invalid-feedback small" id="passwordFeedback">Password wajib diisi.</div>
            </div>

            <!-- Tombol Masuk Neon Creatix -->
            <button type="submit" class="btn-creatix-neon w-100 justify-content-center py-2 mt-2">
                <span>Masuk Sekarang &rarr;</span>
            </button>

        </form>

        <!-- Catatan Bantuan Akun -->
        <div class="mt-4 pt-3 border-top border-secondary text-center">
            <div class="p-2 rounded-3 text-muted small d-inline-flex align-items-center gap-2" style="background-color: var(--creatix-dark);">
                <i class="bi bi-key-fill text-success"></i>
                <span>Gunakan akun: <strong class="text-light">admin</strong> / <strong class="text-light">admin123</strong></span>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
