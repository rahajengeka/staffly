<?php
/**
 * pegawai/index.php
 * Halaman Daftar Pegawai (Employee List + Tabel CRUD) - Tema "Creatix Digital Studio & HR Engine"
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Guard: wajib login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/koneksi.php';

// ---- Ambil seluruh data pegawai ----
try {
    $stmt = $pdo->query('SELECT id, nip, nama, email, jabatan, no_telepon, created_at FROM employees ORDER BY created_at DESC');
    $daftarPegawai = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Employee list query error: ' . $e->getMessage());
    $daftarPegawai = [];
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
    <meta name="base-path" content="../">
    <title>Daftar Pegawai &mdash; Creatix HR Engine</title>

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
        <p class="mt-3 mb-0 text-white fw-bold small">Menghapus data pegawai...</p>
    </div>
</div>

<!-- ==================== TOP STRIP ==================== -->
<div class="creatix-topstrip">
    <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="neon-dot"></span>
            <span>FR.IA.02 Tugas Praktik Demonstrasi &bull; Uji Kompetensi BNSP JWD</span>
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
                    <a class="creatix-nav-link active" href="index.php">Daftar Pegawai</a>
                </li>
                <li class="nav-item">
                    <a class="creatix-nav-link" href="tambah.php">Input Baru</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="tambah.php" class="btn-creatix-neon">
                    <i class="bi bi-plus-lg"></i> Tambah Pegawai
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="container py-4 flex-grow-1">

    <!-- Flash Alerts -->
    <div id="alertContainer">
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

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <span class="text-uppercase fw-bold small" style="color:var(--creatix-neon); letter-spacing:0.08em;">DATABASE KEPEGAWAIAN</span>
            <h2 class="fw-bold text-white mb-0 mt-1">Daftar Seluruh Pegawai</h2>
            <p class="text-muted small mb-0 mt-1">Total <strong id="totalCount" class="text-white"><?= count($daftarPegawai) ?></strong> data pegawai tercatat di basis data MySQL</p>
        </div>
        <div>
            <a href="tambah.php" class="btn-creatix-neon">
                <i class="bi bi-person-plus-fill"></i> Tambah Pegawai Baru
            </a>
        </div>
    </div>

    <!-- Live Search Filter Box -->
    <div class="card p-3 mb-4 rounded-4 border-0" style="background-color: var(--creatix-card); border: 1px solid var(--creatix-border) !important;">
        <div class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-muted" style="border-radius:12px 0 0 12px; border: 1.5px solid var(--creatix-border); border-right: none;">
                        <i class="bi bi-search text-white"></i>
                    </span>
                    <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0 text-white" placeholder="Ketik nama, NIP, atau posisi jabatan..." style="border-radius:0 12px 12px 0;">
                </div>
            </div>
            <div class="col-auto ms-auto text-muted small">
                <span class="badge" style="background:rgba(132, 240, 58, 0.15); color:var(--creatix-neon); border: 1px solid rgba(132, 240, 58, 0.3);">
                    AJAX ENGINE ACTIVE
                </span>
                <span class="ms-2 d-none d-md-inline">Penghapusan asinkron via jQuery AJAX</span>
            </div>
        </div>
    </div>

    <!-- Tabel Data Pegawai (J.620100.005.02) -->
    <div class="creatix-table-card">
        <div class="table-responsive">
            <table class="table creatix-table" id="tabelPegawai">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">No</th>
                        <th>NIP</th>
                        <th>Nama Pegawai</th>
                        <th>Email Resmi</th>
                        <th>Jabatan</th>
                        <th style="width: 150px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pegawaiTbody">
                    <?php if (empty($daftarPegawai)): ?>
                    <tr id="emptyRow">
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            <div class="fw-bold text-white">Belum Ada Data Pegawai</div>
                            <div class="small text-muted mb-3">Tambahkan data pegawai pertama Anda menggunakan tombol di bawah.</div>
                            <a href="tambah.php" class="btn-creatix-neon">
                                <i class="bi bi-plus-lg"></i> Tambah Sekarang
                            </a>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; foreach ($daftarPegawai as $pegawai): ?>
                    <tr id="row-pegawai-<?= (int)$pegawai['id'] ?>" class="pegawai-row">
                        <td class="row-number text-center fw-bold text-muted small"><?= $no++ ?></td>
                        <td>
                            <span class="badge-creatix-nip"><?= htmlspecialchars($pegawai['nip'], ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td>
                            <div class="fw-bold text-white"><?= htmlspecialchars($pegawai['nama'], ENT_QUOTES, 'UTF-8') ?></div>
                        </td>
                        <td>
                            <a href="mailto:<?= htmlspecialchars($pegawai['email'], ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none text-muted small">
                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($pegawai['email'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge-creatix-role"><?= htmlspecialchars($pegawai['jabatan'], ENT_QUOTES, 'UTF-8') ?></span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <!-- Detail (btn-info) -->
                                <a href="detail.php?id=<?= (int)$pegawai['id'] ?>"
                                   class="btn-action-creatix"
                                   data-bs-toggle="tooltip" title="Lihat Profil Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <!-- Edit (btn-warning) -->
                                <a href="edit.php?id=<?= (int)$pegawai['id'] ?>"
                                   class="btn-action-creatix"
                                   data-bs-toggle="tooltip" title="Ubah Data">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <!-- Hapus (btn-danger via AJAX) -->
                                <button
                                    type="button"
                                    class="btn-action-creatix del"
                                    data-bs-toggle="tooltip" title="Hapus Data (AJAX)"
                                    onclick="hapusPegawai(<?= (int)$pegawai['id'] ?>, document.getElementById('row-pegawai-<?= (int)$pegawai['id'] ?>'))">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
<script>
// Live Search Realtime
document.getElementById('liveSearchInput')?.addEventListener('keyup', function() {
    const filter = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#tabelPegawai tbody tr.pegawai-row');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
</body>
</html>
