<?php
/**
 * pegawai/detail.php
 * Halaman Detail Informasi Satu Pegawai - Tema "Creatix Digital Studio & HR Engine"
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Guard: wajib login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/koneksi.php';

// ---- Ambil & validasi parameter ID ----
$idPegawai = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idPegawai || $idPegawai <= 0) {
    $_SESSION['flash_error'] = 'ID pegawai tidak valid.';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, nip, nama, email, jabatan, no_telepon, created_at FROM employees WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $idPegawai]);
    $dataPegawai = $stmt->fetch();
} catch (PDOException $e) {
    error_log('Detail query error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Terjadi kesalahan saat mengambil data.';
    header('Location: index.php');
    exit;
}

if (!$dataPegawai) {
    $_SESSION['flash_error'] = 'Data pegawai tidak ditemukan.';
    header('Location: index.php');
    exit;
}

$namaAdmin = htmlspecialchars($_SESSION['nama_lengkap'], ENT_QUOTES, 'UTF-8');

function safeHtml(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-path" content="../">
    <title>Profil: <?= safeHtml($dataPegawai['nama']) ?> &mdash; Creatix HR Engine</title>

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
            <span>FR-06 Rincian Detail Profil Pegawai &bull; Standar BNSP JWD</span>
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
                <a href="index.php" class="btn-creatix-dark">
                    Lihat Daftar &rarr;
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- ==================== MAIN CONTENT ==================== -->
<main class="container py-4 flex-grow-1">

    <div id="alertContainer"></div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none" style="color:var(--creatix-neon)">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none" style="color:var(--creatix-neon)">Data Pegawai</a></li>
            <li class="breadcrumb-item active text-muted" aria-current="page">Detail: <?= safeHtml($dataPegawai['nama']) ?></li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="creatix-table-card overflow-hidden">

                <!-- Header Profil Creatix -->
                <div class="p-4 p-md-5" style="background-color: var(--creatix-dark); border-bottom: 1px solid var(--creatix-border);">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-4">
                        <div class="rounded-4 d-flex align-items-center justify-content-center fw-bold shadow" 
                             style="width: 80px; height: 80px; background-color: var(--creatix-neon); color: #000000; font-size: 2.2rem; flex-shrink: 0;">
                            <?= strtoupper(substr($dataPegawai['nama'], 0, 1)) ?>
                        </div>
                        <div>
                            <span class="text-uppercase fw-bold small" style="color:var(--creatix-neon); letter-spacing:0.08em;">PROFIL LENGKAP PEGAWAI</span>
                            <h2 class="fw-bold text-white mb-1 mt-1" style="letter-spacing: -0.02em;">
                                <?= safeHtml($dataPegawai['nama']) ?>
                            </h2>
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                <span class="badge-creatix-role">
                                    <?= safeHtml($dataPegawai['jabatan']) ?>
                                </span>
                                <span class="badge-creatix-nip">
                                    NIP: <?= safeHtml($dataPegawai['nip']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Body Rincian Informasi -->
                <div class="p-4 p-md-5" style="background-color: var(--creatix-card);">
                    <h5 class="fw-bold text-white mb-4 pb-2 border-bottom border-secondary">
                        <i class="bi bi-info-circle-fill me-2" style="color:var(--creatix-neon)"></i> Rincian Identitas Pegawai
                    </h5>

                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background-color: var(--creatix-dark); border: 1px solid var(--creatix-border);">
                                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:0.05em;">Nomor Induk Pegawai</div>
                                <div class="fw-bold fs-6 mt-1 text-white" style="font-family:monospace;">
                                    <?= safeHtml($dataPegawai['nip']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background-color: var(--creatix-dark); border: 1px solid var(--creatix-border);">
                                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:0.05em;">Jabatan / Posisi</div>
                                <div class="fw-bold fs-6 mt-1 text-white">
                                    <?= safeHtml($dataPegawai['jabatan']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background-color: var(--creatix-dark); border: 1px solid var(--creatix-border);">
                                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:0.05em;">Email Resmi Perusahaan</div>
                                <div class="fw-bold fs-6 mt-1">
                                    <a href="mailto:<?= safeHtml($dataPegawai['email']) ?>" class="text-decoration-none" style="color:var(--creatix-neon);">
                                        <i class="bi bi-envelope me-1"></i> <?= safeHtml($dataPegawai['email']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background-color: var(--creatix-dark); border: 1px solid var(--creatix-border);">
                                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:0.05em;">Nomor WhatsApp / Kontak</div>
                                <div class="fw-bold fs-6 mt-1 text-white">
                                    <?php if ($dataPegawai['no_telepon']): ?>
                                    <a href="tel:<?= safeHtml($dataPegawai['no_telepon']) ?>" class="text-decoration-none text-white">
                                        <i class="bi bi-whatsapp me-1 text-success"></i> <?= safeHtml($dataPegawai['no_telepon']) ?>
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted fst-italic">Belum dicatat</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background-color: var(--creatix-dark); border: 1px solid var(--creatix-border);">
                                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing:0.05em;">Waktu Terdaftar di Sistem</div>
                                <div class="fw-bold fs-6 mt-1 text-white">
                                    <i class="bi bi-calendar-event me-1" style="color:var(--creatix-neon);"></i>
                                    <?= date('d F Y, H:i', strtotime($dataPegawai['created_at'])) ?> WIB
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Creatix -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pt-4 mt-4 border-top border-secondary">
                        <a href="index.php" class="btn-creatix-dark">
                            &larr; Kembali ke Daftar
                        </a>
                        <div class="d-flex gap-2">
                            <a href="edit.php?id=<?= (int)$dataPegawai['id'] ?>" class="btn-creatix-neon">
                                <i class="bi bi-pencil"></i> Ubah Data
                            </a>
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4" onclick="hapusDetailPegawai(<?= (int)$dataPegawai['id'] ?>)">
                                <i class="bi bi-trash"></i> Hapus Pegawai
                            </button>
                        </div>
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
function hapusDetailPegawai(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data pegawai ini?\nTindakan ini tidak dapat dibatalkan.')) {
        return;
    }

    showLoading();

    $.ajax({
        url: '../proses/proses_hapus.php',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(res) {
            hideLoading();
            if (res.status === 'success') {
                window.location.href = 'index.php';
            } else {
                alert(res.message || 'Gagal menghapus data.');
            }
        },
        error: function() {
            hideLoading();
            alert('Terjadi kesalahan pada server.');
        }
    });
}
</script>
</body>
</html>
