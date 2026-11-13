<?php
/**
 * dashboard.php
 * Halaman Dashboard - Bertema "Creatix Digital Studio & HR Engine"
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Guard: wajib login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/koneksi.php';

// ---- Ambil data statistik ----
try {
    $stmtCount  = $pdo->query('SELECT COUNT(*) AS total FROM employees');
    $totalPegawai = (int) $stmtCount->fetchColumn();

    // Pegawai terbaru
    $stmtTerbaru = $pdo->query('SELECT id, nama, nip, email, jabatan, created_at FROM employees ORDER BY created_at DESC LIMIT 5');
    $pegawaiTerbaru = $stmtTerbaru->fetchAll();
} catch (PDOException $e) {
    error_log('Dashboard query error: ' . $e->getMessage());
    $totalPegawai   = 0;
    $pegawaiTerbaru = [];
}

$namaAdmin = htmlspecialchars($_SESSION['nama_lengkap'], ENT_QUOTES, 'UTF-8');

$flashSuccess = '';
$flashError   = '';
if (isset($_SESSION['flash_success'])) {
    $flashSuccess = htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $flashError = htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8');
    unset($_SESSION['flash_error']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-path" content="./">
    <title>Dashboard &mdash; Creatix HR Engine</title>

    <!-- Google Fonts: Plus Jakarta Sans & Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom Creatix Theme CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="text-center bg-dark p-4 rounded-4 border border-secondary shadow-lg">
        <div class="spinner-border text-success" style="width:2.5rem;height:2.5rem;" role="status">
            <span class="visually-hidden">Memuat...</span>
        </div>
        <p class="mt-3 mb-0 text-white fw-bold small">Memproses sistem...</p>
    </div>
</div>

<!-- ==================== TOP STRIP ==================== -->
<div class="creatix-topstrip">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="neon-dot"></span>
            <span>Uji Kompetensi BNSP SKK-32-10/2024 &bull; Skema Junior Web Developer</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-person-circle me-1"></i> Sesi: <strong><?= $namaAdmin ?></strong></span>
            <span>&bull;</span>
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')" class="text-decoration-none text-light opacity-75 hover-opacity-100">
                Logout <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- ==================== NAVBAR ==================== -->
<nav class="navbar navbar-expand-lg creatix-navbar sticky-top">
    <div class="container">
        <a class="creatix-brand" href="dashboard.php">
            <span class="text-white">CREATIX</span>
            <span class="creatix-brand-badge">HR ENGINE</span>
        </a>

        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCreatix">
            <i class="bi bi-list fs-2 text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarCreatix">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="creatix-nav-link active" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="creatix-nav-link" href="pegawai/index.php">Daftar Pegawai</a>
                </li>
                <li class="nav-item">
                    <a class="creatix-nav-link" href="pegawai/tambah.php">Input Baru</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="pegawai/tambah.php" class="btn-creatix-neon">
                    <i class="bi bi-plus-lg"></i> Tambah Pegawai
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="w-full flex-grow-1">

    <!-- Flash Alerts -->
    <div class="container pt-3" id="alertContainer">
        <?php if ($flashSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show session-alert d-flex align-items-center gap-2 py-2 px-3 mb-4 rounded-3 border-0 shadow-sm" role="alert" style="background:#1b3820; color:#84f03a;">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <div class="small fw-semibold"><?= $flashSuccess ?></div>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close" style="padding:0.75rem;"></button>
        </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
        <div class="alert alert-danger alert-dismissible fade show session-alert d-flex align-items-center gap-2 py-2 px-3 mb-4 rounded-3 border-0 shadow-sm" role="alert" style="background:#3b1820; color:#ff7b92;">
            <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
            <div class="small fw-semibold"><?= $flashError ?></div>
            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert" aria-label="Close" style="padding:0.75rem;"></button>
        </div>
        <?php endif; ?>
    </div>

    <!-- ==================== SECTION 1: CURVED OFF-WHITE HERO (CREATIX SIGNATURE) ==================== -->
    <section class="creatix-hero-curved">
        <!-- Bintang Dekorasi 8-Sudut Khas Creatix -->
        <div class="creatix-star-decor d-none d-md-block">
            <svg width="64" height="64" viewBox="0 0 100 100" fill="currentColor">
                <path d="M50 0 L55 35 L85 15 L65 45 L100 50 L65 55 L85 85 L55 65 L50 100 L45 65 L15 85 L35 55 L0 50 L35 45 L15 15 L45 35 Z" />
            </svg>
        </div>

        <div class="container">
            <!-- Headline Utama -->
            <div class="text-center max-w-4xl mx-auto mb-5">
                <h1 class="creatix-hero-heading">
                    Empowering Organizations <br class="d-none d-sm-inline"> Through Creative HR Solutions
                </h1>
            </div>

            <!-- Grid 3 Kolom Hero Khas Creatix: Left Blurb | Center Portrait + Dual Dock | Right Rating -->
            <div class="row align-items-end g-4 pt-2">
                <!-- Kolom Kiri: Blurb & Small Action -->
                <div class="col-lg-4 order-2 order-lg-1">
                    <div class="text-success fs-3 fw-bold mb-2">&searr;</div>
                    <p class="creatix-hero-desc mb-4">
                        Dari pencatatan data kepegawaian hingga monitoring struktur organisasi, kami menghadirkan sistem terstruktur berbasis PHP Native &amp; MySQL PDO yang aman dan responsif.
                    </p>
                    <a href="pegawai/index.php" class="btn btn-outline-dark btn-sm rounded-pill px-4 py-2 fw-bold">
                        Buka Database Pegawai
                    </a>
                </div>

                <!-- Kolom Tengah: Aset Gambar + Floating Dual Action Dock -->
                <div class="col-lg-4 text-center order-1 order-lg-2">
                    <div class="position-relative d-inline-block">
                        <img src="assets/images/banner-dashboard.png" 
                             alt="Creatix HR Platform"
                             class="img-fluid rounded-top-circle shadow-lg"
                             style="max-height: 290px; border-radius: 120px 120px 20px 20px;">
                        
                        <!-- Floating Dual Action Dock Khas Creatix -->
                        <div class="position-absolute start-50 translate-middle-x" style="bottom: -20px; white-space: nowrap;">
                            <div class="creatix-action-dock">
                                <a href="pegawai/index.php" class="btn-creatix-neon py-2 px-3 small">
                                    Kelola Pegawai
                                </a>
                                <a href="pegawai/tambah.php" class="btn-creatix-dark py-2 px-3 small">
                                    + Input Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Experience Rating Card Khas Creatix -->
                <div class="col-lg-4 text-lg-end order-3">
                    <div class="creatix-rating-card d-inline-block text-start">
                        <div class="creatix-rating-stars">
                            &starf; &starf; &starf; &starf; &starf;
                        </div>
                        <div class="fw-bold fs-5 text-dark">5.0 Certified</div>
                        <div class="text-muted small">Standard Uji Kompetensi BNSP</div>
                        <div class="mt-2 pt-2 border-top small text-muted">
                            Total Pegawai: <strong class="text-dark"><?= $totalPegawai ?> Orang</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== SECTION 2: INFINITE MARQUEE TICKER (CREATIX SIGNATURE) ==================== -->
    <div class="creatix-ticker-wrapper">
        <div class="creatix-ticker-track">
            <!-- Ticker Loop 1 -->
            <div class="creatix-ticker-item">⚡ PHP 8 NATIVE <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ MYSQL DATABASE <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ PDO PREPARED STATEMENTS <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ JQUERY AJAX ASYNC <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ BOOTSTRAP 5 RESPONSIVE <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ BNSP JWD SKK-32-10/2024 <span class="neon">&bull;</span></div>
            <!-- Ticker Loop 2 (Seamless) -->
            <div class="creatix-ticker-item">⚡ PHP 8 NATIVE <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ MYSQL DATABASE <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ PDO PREPARED STATEMENTS <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ JQUERY AJAX ASYNC <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ BOOTSTRAP 5 RESPONSIVE <span class="neon">&bull;</span></div>
            <div class="creatix-ticker-item">⚡ BNSP JWD SKK-32-10/2024 <span class="neon">&bull;</span></div>
        </div>
    </div>

    <!-- ==================== SECTION 3: SERVICES & NUMBERED CARDS (CREATIX DARK THEME) ==================== -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-uppercase fw-bold small" style="color:var(--creatix-neon); letter-spacing:0.08em;">Layanan Sistem</span>
                <h2 class="fw-bold text-white mb-0 mt-1">Core Modules of HR Engine</h2>
            </div>
            <a href="pegawai/index.php" class="btn-creatix-dark d-none d-md-inline-flex">
                Semua Modul <i class="bi bi-arrow-up-right"></i>
            </a>
        </div>

        <div class="row g-4">
            <!-- Card 01 -->
            <div class="col-md-4">
                <div class="creatix-card">
                    <div>
                        <div class="creatix-card-number">01</div>
                        <h4 class="creatix-card-title">Employee Records Lifecycle</h4>
                        <p class="creatix-card-desc">
                            Pengelolaan siklus penuh data pegawai: penambahan identitas baru, penampilan data terstruktur, hingga pembaruan instan.
                        </p>
                    </div>
                    <a href="pegawai/index.php" class="text-decoration-none fw-bold small text-white">
                        Kelola Sekarang <span class="text-success">&nearr;</span>
                    </a>
                </div>
            </div>

            <!-- Card 02 -->
            <div class="col-md-4">
                <div class="creatix-card">
                    <div>
                        <div class="creatix-card-number">02</div>
                        <h4 class="creatix-card-title">Department &amp; Roles Mapping</h4>
                        <p class="creatix-card-desc">
                            Struktur penempatan posisi terintegrasi dengan Nomor Induk Pegawai unik untuk mencegah redundansi informasi.
                        </p>
                    </div>
                    <a href="pegawai/index.php" class="text-decoration-none fw-bold small text-white">
                        Lihat Departemen <span class="text-success">&nearr;</span>
                    </a>
                </div>
            </div>

            <!-- Card 03 -->
            <div class="col-md-4">
                <div class="creatix-card">
                    <div>
                        <div class="creatix-card-number">03</div>
                        <h4 class="creatix-card-title">Dynamic AJAX Data Removal</h4>
                        <p class="creatix-card-desc">
                            Penghapusan baris data asinkron tanpa reload menggunakan jQuery AJAX, mengembalikan response JSON aman dari backend PDO.
                        </p>
                    </div>
                    <a href="pegawai/index.php" class="text-decoration-none fw-bold small text-white">
                        Uji Fitur AJAX <span class="text-success">&nearr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== SECTION 4: METRICS & RECENT EMPLOYEES ==================== -->
    <div class="container mb-5">
        <div class="row g-4 align-items-stretch">
            <!-- Kolom Kiri: Metric Cards -->
            <div class="col-lg-4">
                <div class="d-flex flex-column gap-3 h-100">
                    <div class="creatix-stat-card">
                        <div class="creatix-stat-num"><?= $totalPegawai ?></div>
                        <div class="creatix-stat-label">Total Pegawai Aktif</div>
                    </div>
                    <div class="creatix-stat-card">
                        <div class="creatix-stat-num text-success">PDO</div>
                        <div class="creatix-stat-label">Prepared Statement Engine</div>
                    </div>
                    <div class="creatix-stat-card">
                        <div class="creatix-stat-num text-warning">230'</div>
                        <div class="creatix-stat-label">Waktu Standar Uji BNSP</div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Tabel Pegawai Terbaru (Dark Creatix) -->
            <div class="col-lg-8">
                <div class="creatix-table-card h-100 d-flex flex-column">
                    <div class="p-3 px-4 border-bottom d-flex justify-content-between align-items-center" style="background:var(--creatix-dark); border-color:var(--creatix-border)!important;">
                        <div>
                            <h6 class="fw-bold mb-0 text-white">
                                <i class="bi bi-clock-history me-1" style="color:var(--creatix-neon);"></i> Pegawai Terbaru
                            </h6>
                            <small class="text-muted">Data terbaru yang tersimpan di MySQL</small>
                        </div>
                        <a href="pegawai/index.php" class="btn-creatix-dark py-1 px-3 small">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="table-responsive flex-grow-1">
                        <table class="table creatix-table">
                            <thead>
                                <tr>
                                    <th>Nama Pegawai</th>
                                    <th>NIP</th>
                                    <th>Jabatan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($pegawaiTerbaru)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Belum ada data pegawai yang tercatat.
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($pegawaiTerbaru as $peg): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-white"><?= htmlspecialchars($peg['nama'], ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($peg['email'], ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td>
                                        <span class="badge-creatix-nip"><?= htmlspecialchars($peg['nip'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge-creatix-role"><?= htmlspecialchars($peg['jabatan'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="pegawai/detail.php?id=<?= (int)$peg['id'] ?>" class="btn-action-creatix" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
            <span class="text-muted">&bull; Uji Kompetensi Junior Web Developer BNSP</span>
        </div>
        <div class="text-center text-md-end small">
            Dokumen Acuan <strong>FR.IA.02</strong> &bull; Pemrograman Terstruktur PHP &amp; MySQL
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
