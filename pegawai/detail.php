<?php
require_once '../config/koneksi.php';
requireAuth('../login.php');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ./index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = :id");
$stmt->execute([':id' => $id]);
$employee = $stmt->fetch();

if (!$employee) {
    header('Location: ./index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staffly — <?= e($employee['nama']) ?></title>
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
            --hero-light: #f4f5f7;
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

        .detail-hero-header {
            background: linear-gradient(
                155deg,
                #ffffff 0%,
                #f4f5f7 48%,
                #cff9a4 82%,
                #84f03a 100%
            );
            color: #171717;
        }

        @media print {
            .staffly-sidebar,
            .mobile-topbar,
            .no-print {
                display: none !important;
            }
            .staffly-shell {
                display: block !important;
            }
            .staffly-main-area {
                width: 100% !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
            }
            .staffly-panel {
                border: 1px solid #ccc !important;
                background: #ffffff !important;
            }
            .detail-hero-header {
                background: #f4f5f7 !important;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="staffly-shell">

        <!-- SIDEBAR -->
        <aside class="staffly-sidebar offcanvas-lg offcanvas-start text-white" tabindex="-1" id="stafflySidebar" aria-labelledby="stafflySidebarLabel">
            <div class="offcanvas-header border-bottom border-secondary border-opacity-25 px-4 py-3 d-lg-none">
                <a href="../dashboard.php" class="d-flex align-items-center gap-2 text-white text-decoration-none fw-bold" id="stafflySidebarLabel">
                    <span class="staffly-dot"></span>
                    <img src="../assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                    <span>Staffly</span>
                </a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#stafflySidebar" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column justify-content-between h-100 p-4">
                <div>
                    <a href="../dashboard.php" class="d-none d-lg-flex align-items-center gap-2 text-white text-decoration-none fw-bold fs-5 mb-1">
                        <span class="staffly-dot"></span>
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
                    <span class="staffly-dot"></span>
                    <img src="../assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                    <span>Staffly</span>
                </a>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#stafflySidebar" aria-controls="stafflySidebar">
                    <i class="bi bi-list me-1"></i> Menu
                </button>
            </div>

            <main class="container-fluid px-3 px-lg-4 py-5 flex-grow-1" style="max-width: 820px;">
                <a href="./index.php" class="text-secondary text-decoration-none small d-inline-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-arrow-left"></i>Back to List
                </a>

                <div class="staffly-panel overflow-hidden">
                    <div class="detail-hero-header p-4 p-sm-5 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <?php if (!empty($employee['foto'])): ?>
                                <img src="../assets/images/employees/<?= e($employee['foto']) ?>" alt="<?= e($employee['nama']) ?>"
                                     style="width: 64px; height: 64px; border-radius: 16px; object-fit: cover;">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 64px; height: 64px; border-radius: 16px; background: #18191c; color: #84f03a; font-size: 1.25rem;">
                                    <?= e(getInitials($employee['nama'])) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h1 class="h3 fw-bold mb-0"><?= e($employee['nama']) ?></h1>
                                <div class="small text-dark opacity-75"><?= e($employee['jabatan']) ?> · <span class="font-mono-tabular"><?= e($employee['nip']) ?></span></div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-shrink-0 no-print">
                            <button onclick="window.print()" class="btn btn-sm rounded-pill px-3 py-2 fw-bold"
                                style="background-color: var(--brand-card); border: 1px solid #32363e; color: #e5e7eb; font-size: 0.8rem;">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <a href="./edit.php?id=<?= (int) $employee['id'] ?>" class="btn btn-dark rounded-pill px-4 py-2 small fw-bold" style="color: #84f03a;">
                                Edit Data
                            </a>
                        </div>
                    </div>

                    <div class="p-4 p-sm-5 row g-4">
                        <div class="col-sm-6">
                            <div class="small text-secondary mb-1">Internal Record ID</div>
                            <div class="fw-semibold text-white font-mono-tabular">#<?= (int) $employee['id'] ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-secondary mb-1">Employee ID (NIP)</div>
                            <div class="fw-semibold text-white font-mono-tabular"><?= e($employee['nip']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-secondary mb-1">Role / Position</div>
                            <div class="fw-semibold text-white"><?= e($employee['jabatan']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-secondary mb-1">Work Email</div>
                            <div class="fw-semibold text-white"><?= e($employee['email']) ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-secondary mb-1">Phone Number</div>
                            <div class="fw-semibold text-white font-mono-tabular"><?= e($employee['no_telepon'] ?: 'Not provided') ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-secondary mb-1">Created At</div>
                            <div class="fw-semibold text-white font-mono-tabular"><?= e($employee['created_at']) ?></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>