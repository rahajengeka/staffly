<?php
/**
 * pegawai/tambah.php
 * Halaman Form Tambah Pegawai - Tema "Creatix Digital Studio & HR Engine"
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Guard: wajib login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$namaAdmin = htmlspecialchars($_SESSION['nama_lengkap'], ENT_QUOTES, 'UTF-8');

$flashError = '';
$oldInput   = [];
if (isset($_SESSION['flash_error'])) {
    $flashError = htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['flash_error']);
}
if (isset($_SESSION['old_input'])) {
    $oldInput = $_SESSION['old_input'];
    unset($_SESSION['old_input']);
}

function oldVal(array $old, string $key): string {
    return htmlspecialchars($old[$key] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-path" content="../">
    <title>Tambah Pegawai &mdash; Creatix HR Engine</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Creatix Theme CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="text-center bg-dark p-4 rounded-4 border border-secondary shadow-lg">
        <div class="spinner-border text-success" style="width:2.5rem;height:2.5rem;" role="status">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <p class="mt-3 mb-0 text-white fw-bold small">Menyimpan data pegawai...</p>
    </div>
</div>

<!-- ==================== TOP STRIP ==================== -->
<div class="creatix-topstrip">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="neon-dot"></span>
            <span>FR-04 Formulir Pendaftaran Pegawai &bull; Standar BNSP JWD</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-person-circle me-1"></i> Sesi: <strong><?= $namaAdmin ?></strong></span>
            <span>&bull;</span>
            <a href="../logout.php" onclick="return confirm('Yakin ingin keluar?')" class="text-decoration-none text-light opacity-75 hover-opacity-100">
                Logout <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- ==================== NAVBAR ==================== -->
<nav class="navbar navbar-expand-lg creatix-navbar sticky-top">
    <div class="container">
        <a class="creatix-brand" href="../dashboard.php">
            <span class="text-white">CREATIX</span>
            <span class="creatix-brand-badge">HR ENGINE</span>
        </a>

        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCreatix">
            <i class="bi bi-list fs-2 text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarCreatix">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="creatix-nav-link" href="../dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="creatix-nav-link" href="index.php">Daftar Pegawai</a>
                </li>
                <li class="nav-item">
                    <a class="creatix-nav-link active" href="tambah.php">Input Baru</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="index.php" class="btn-creatix-dark">
                    Lihat Daftar &rarr;
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="container py-4 flex-grow-1">

    <!-- Flash Error -->
    <div id="alertContainer">
        <?php if ($flashError): ?>
        <div class="alert alert-danger alert-dismissible fade show session-alert d-flex align-items-center gap-2 py-2 px-3 mb-4 rounded-3 border-0 shadow-sm" role="alert" style="background:#3b1820; color:#ff7b92;">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <div class="small fw-semibold"><?= $flashError ?></div>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close" style="padding:0.75rem;"></button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none" style="color:var(--creatix-neon)">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color:var(--creatix-neon)">Data Pegawai</a></li>
            <li class="breadcrumb-item active text-muted" aria-current="page">Input Pegawai Baru</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 p-4 p-md-5 rounded-4" style="background-color: var(--creatix-card); border: 1px solid var(--creatix-border) !important;">
                
                <div class="mb-4">
                    <span class="text-uppercase fw-bold small" style="color:var(--creatix-neon); letter-spacing:0.08em;">FORMULIR PENDAFTARAN</span>
                    <h3 class="fw-bold text-white mb-1 mt-1">Input Data Pegawai Baru</h3>
                    <p class="text-muted small mb-0">Isi seluruh informasi dengan valid sesuai aturan sistem kepegawaian</p>
                </div>

                <form id="formPegawai" action="../proses/proses_tambah.php" method="POST" novalidate>

                    <!-- NIP & Nama -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="nip" class="form-label fw-bold small text-light">
                                NIP (Nomor Induk Pegawai) <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nip" name="nip"
                                   placeholder="Contoh: 199801152024011001"
                                   value="<?= oldVal($oldInput, 'nip') ?>"
                                   maxlength="30">
                            <div class="invalid-feedback small"></div>
                            <div class="form-text text-muted small">Wajib 15–20 digit angka unik.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="nama" class="form-label fw-bold small text-light">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                   placeholder="Masukkan nama lengkap pegawai"
                                   value="<?= oldVal($oldInput, 'nama') ?>"
                                   maxlength="100">
                            <div class="invalid-feedback small"></div>
                        </div>
                    </div>

                    <!-- Email & Jabatan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-bold small text-light">
                                Alamat Email Resmi <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email"
                                   placeholder="nama@perusahaan.id"
                                   value="<?= oldVal($oldInput, 'email') ?>"
                                   maxlength="100">
                            <div class="invalid-feedback small"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="jabatan" class="form-label fw-bold small text-light">
                                Posisi / Jabatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan"
                                   placeholder="Contoh: Web Developer, UI Designer"
                                   value="<?= oldVal($oldInput, 'jabatan') ?>"
                                   maxlength="80">
                            <div class="invalid-feedback small"></div>
                        </div>
                    </div>

                    <!-- No. Telepon -->
                    <div class="mb-4">
                        <label for="no_telepon" class="form-label fw-bold small text-light">
                            Nomor WhatsApp / Telepon <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <input type="tel" class="form-control" id="no_telepon" name="no_telepon"
                               placeholder="Contoh: 081234567890"
                               value="<?= oldVal($oldInput, 'no_telepon') ?>"
                               maxlength="20">
                    </div>

                    <!-- Tombol Aksi Creatix -->
                    <div class="d-flex align-items-center gap-3 pt-3 border-top border-secondary">
                        <button type="submit" class="btn-creatix-neon">
                            <i class="bi bi-save2"></i> Simpan Data Pegawai
                        </button>
                        <a href="index.php" class="btn-creatix-dark">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</main>

<!-- ==================== FOOTER ==================== -->
<footer class="creatix-footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <strong class="text-white">CREATIX</strong>
            <span class="creatix-brand-badge">HR ENGINE</span>
            <span class="text-muted">&bull; Skema Sertifikasi Junior Web Developer</span>
        </div>
        <div class="text-center text-md-end small">
            Dokumen Acuan <strong>FR.IA.02</strong> &bull; Pemrograman Terstruktur PHP &amp; MySQL
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>
