<?php
require_once '../config/koneksi.php';
requireAuth('../login.php');

$error = $_SESSION['form_error'] ?? '';
unset($_SESSION['form_error']);

// Generate NIP otomatis: YYYYMMDD + 4 digit sequence (contoh: 202611140005)
$maxId = (int) $pdo->query("SELECT MAX(id) FROM employees")->fetchColumn();
$autoNip = date('Ymd') . str_pad((string) ($maxId + 1), 4, '0', STR_PAD_LEFT);
$daftarJabatan = getDaftarJabatan($pdo);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staffly — Add Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=7">

    <style>
        :root {
            --brand-black: #0c0d0e;
            --brand-dark: #141518;
            --brand-panel: #121316;
            --brand-card: #1b1d22;
            --brand-border: #272a30;
            --brand-neon: #84f03a;
            --brand-neon-dark: #6ccd26;
        }

        body {
            background-color: var(--brand-black) !important;
            color: #e5e7eb;
        }

        .staffly-panel {
            background-color: var(--brand-panel) !important;
            border: 1px solid var(--brand-border) !important;
            border-radius: 24px;
        }

        .staffly-input {
            background-color: var(--brand-card) !important;
            border: 1px solid #32363e !important;
            color: #ffffff !important;
        }

        .staffly-input:focus {
            border-color: var(--brand-neon) !important;
            box-shadow: none !important;
        }

        .staffly-shell {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .staffly-sidebar {
            width: 230px;
            flex-shrink: 0;
            background-color: var(--brand-panel);
            border-right: 1px solid var(--brand-border);
        }

        .staffly-main-area {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 992px) {
            .staffly-sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
            }
        }

        .sidebar-menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 14px;
            color: #cbd5e1;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sidebar-menu-link:hover {
            background-color: rgba(255, 255, 255, 0.06);
            color: #ffffff;
        }

        .sidebar-menu-link.active {
            background-color: var(--brand-neon);
            color: #0c0d0e;
            font-weight: 700;
        }

        .mobile-topbar {
            background-color: var(--brand-panel);
            border-bottom: 1px solid var(--brand-border);
        }
    </style>
</head>
<body>
    <div class="staffly-shell">

        <!-- SIDEBAR -->
        <aside class="staffly-sidebar offcanvas-lg offcanvas-start text-white" tabindex="-1" id="stafflySidebar" aria-labelledby="stafflySidebarLabel">
            <div class="offcanvas-header border-bottom border-secondary border-opacity-25 px-4 py-3 d-lg-none">
                <a href="../dashboard.php" class="d-flex align-items-center gap-2 text-white text-decoration-none fw-bold" id="stafflySidebarLabel">
                    <img src="../assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                    <span>Staffly</span>
                </a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#stafflySidebar" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column justify-content-between h-100 p-4">
                <div>
                    <a href="../dashboard.php" class="d-none d-lg-flex align-items-center gap-2 text-white text-decoration-none fw-bold fs-5 mb-1">
                        <img src="../assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                        <span>Staffly</span>
                    </a>
                    <p class="d-none d-lg-block small text-secondary mb-4 ps-1">Workforce Directory</p>

                    <div class="small text-uppercase fw-bold text-secondary mb-2 px-2" style="font-size: 0.7rem; letter-spacing: 0.06em;">
                        Menu
                    </div>

                    <nav class="d-flex flex-column gap-2">
                        <a href="../dashboard.php" class="sidebar-menu-link">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="./index.php" class="sidebar-menu-link active">
                            <i class="bi bi-people-fill"></i>
                            <span>Employees</span>
                        </a>
                    </nav>
                </div>

                <div class="pt-4 border-top border-secondary border-opacity-25">
                    <div class="p-3 rounded-4 mb-3" style="background-color: var(--brand-card); border: 1px solid var(--brand-border);">
                        <div class="small text-secondary">Signed in as</div>
                        <div class="small fw-bold text-white text-truncate">
                            <?= e($_SESSION['nama_lengkap'] ?? 'HR Administrator') ?>
                        </div>
                    </div>

                    <a href="../logout.php" class="btn btn-neon w-100 py-2 text-decoration-none">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </aside>

        <!-- KONTEN UTAMA -->
        <div class="staffly-main-area">
            <div class="mobile-topbar d-flex d-lg-none align-items-center justify-content-between px-3 py-3">
                <a href="../dashboard.php" class="d-flex align-items-center gap-2 text-white text-decoration-none fw-bold">
                    <img src="../assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                    <span>Staffly</span>
                </a>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#stafflySidebar" aria-controls="stafflySidebar">
                    <i class="bi bi-list me-1"></i> Menu
                </button>
            </div>

            <main class="container-fluid px-3 px-lg-4 py-5 flex-grow-1" style="max-width: 820px;">
                <a href="./index.php" class="text-secondary text-decoration-none small d-inline-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-arrow-left"></i>Back to Employee List
                </a>

                <div class="staffly-panel p-4 p-sm-5">
                    <span class="small font-mono-tabular d-block mb-1" style="color: #84f03a;">New Team Member</span>
                    <h1 class="h3 fw-bold text-white mb-2">Add New Employee</h1>
                    <p class="small text-secondary mb-4">Complete the form below with a unique Employee ID, work email, and optional photo.</p>

                    <div id="formErrorBox" class="alert alert-danger small <?= $error ? '' : 'd-none' ?>" role="alert">
                        <?= e($error) ?>
                    </div>

                    <form id="employeeForm" action="../proses/proses_tambah.php" method="POST" enctype="multipart/form-data" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="nip" class="form-label small fw-semibold text-light mb-0">Employee ID (NIP) *</label>
                                    <button type="button" id="btnGenNip" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" style="color: #84f03a; font-size: 0.76rem;">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Regenerate
                                    </button>
                                </div>
                                <input type="text" id="nip" name="nip" class="form-control staffly-input font-mono-tabular" value="<?= e($autoNip) ?>" placeholder="e.g. 199801152024011001" required>
                                <div class="form-text text-secondary" style="font-size: 0.72rem;">Otomatis dibuat oleh sistem, tetap bisa diedit manual.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="nama" class="form-label small fw-semibold text-light">Full Name *</label>
                                <input type="text" id="nama" name="nama" class="form-control staffly-input" placeholder="e.g. Budi Santoso" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-semibold text-light">Work Email *</label>
                                <input type="email" id="email" name="email" class="form-control staffly-input" placeholder="name@company.com" required>
                            </div>
                            <div class="col-md-6">
                                <label for="jabatan" class="form-label small fw-semibold text-light">Role / Position *</label>
                                <select id="jabatan" name="jabatan" class="form-select staffly-input" required>
                                    <option value="" disabled selected>— Select Role / Position —</option>
                                    <?php foreach ($daftarJabatan as $role): ?>
                                        <option value="<?= e($role) ?>"><?= e($role) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="no_telepon" class="form-label small fw-semibold text-light">Phone Number</label>
                                <input type="text" id="no_telepon" name="no_telepon" class="form-control staffly-input font-mono-tabular" placeholder="e.g. 081234567890">
                            </div>
                            <div class="col-md-6">
                                <label for="foto" class="form-label small fw-semibold text-light">Profile Photo (Optional)</label>
                                <input type="file" id="foto" name="foto" accept="image/*" class="form-control staffly-input">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <a href="./index.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 small">Cancel</a>
                            <button type="submit" class="btn btn-neon px-4 py-2 small">Save Employee</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
        const btnGen = document.getElementById('btnGenNip');
        if (btnGen) {
            btnGen.addEventListener('click', function () {
                const now = new Date();
                const y = now.getFullYear();
                const m = String(now.getMonth() + 1).padStart(2, '0');
                const d = String(now.getDate()).padStart(2, '0');
                const rand = String(Math.floor(1000 + Math.random() * 9000));
                document.getElementById('nip').value = `${y}${m}${d}${rand}`;
            });
        }
    </script>
</body>
</html>