<?php
require_once './config/koneksi.php';
requireAuth('./login.php');

$totalEmployees = (int) $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
$totalRoles = (int) $pdo->query("SELECT COUNT(DISTINCT jabatan) FROM employees")->fetchColumn();
$verifiedPhones = (int) $pdo->query("SELECT COUNT(*) FROM employees WHERE no_telepon IS NOT NULL AND TRIM(no_telepon) != ''")->fetchColumn();
$recentEmployees = $pdo->query("SELECT * FROM employees ORDER BY id DESC LIMIT 4")->fetchAll();

// Data untuk chart: jumlah pegawai per jabatan
$chartData = $pdo->query("SELECT jabatan, COUNT(*) as total FROM employees GROUP BY jabatan ORDER BY total DESC LIMIT 7")->fetchAll();
$chartLabels = json_encode(array_column($chartData, 'jabatan'));
$chartValues = json_encode(array_column($chartData, 'total'));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staffly — Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css?v=7">

    <style>
        /* Samakan warna dengan Login Page (Hitam, Putih, Hijau Neon) */
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

        .staffly-card {
            background-color: var(--brand-dark) !important;
            border: 1px solid var(--brand-border) !important;
        }

        .staffly-panel {
            background-color: var(--brand-panel) !important;
            border: 1px solid var(--brand-border) !important;
        }

        .staffly-logo {
            height: 1.5em;
            width: auto;
            object-fit: contain;
            display: inline-block;
            flex-shrink: 0;
        }

        /* Sidebar Layout */
        .staffly-shell {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .staffly-sidebar {
            width: 240px;
            flex-shrink: 0;
            background-color: var(--brand-panel);
            border-right: 1px solid var(--brand-border);
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

        /* Hero Dashboard: Garis Lurus (Tanpa Lengkungan) + Gradient Putih -> Hijau */
        .dashboard-hero-banner {
            background: linear-gradient(
                155deg,
                #ffffff 0%,
                #f4f5f7 48%,
                #cff9a4 82%,
                #84f03a 100%
            );
            color: #171717;
            border-radius: 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 48px 0;
        }

        /* Action Buttons Presisi di Hero */
        .btn-hero-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 22px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            line-height: 1;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .btn-hero-light {
            background-color: #ffffff;
            color: #0c0d0e;
            border: 1px solid rgba(0, 0, 0, 0.12);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .btn-hero-light:hover {
            background-color: #f8fafc;
            color: #000000;
            border-color: rgba(0, 0, 0, 0.2);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .btn-hero-dark {
            background-color: #141518;
            color: #84f03a;
            border: 1px solid #141518;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        .btn-hero-dark:hover {
            background-color: #000000;
            color: #84f03a;
            border-color: #000000;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
        }

        .btn-hero-action i {
            font-size: 0.95rem;
            line-height: 1;
            display: inline-flex;
            align-items: center;
        }

        .mobile-topbar {
            background-color: var(--brand-panel);
            border-bottom: 1px solid var(--brand-border);
        }

        @media (min-width: 992px) {
            .chart-divider {
                border-left: 1px solid var(--brand-border);
                padding-left: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="staffly-shell">

        <!-- SIDEBAR (Hanya Dashboard, Employees, dan Logout) -->
        <aside class="staffly-sidebar offcanvas-lg offcanvas-start text-white" tabindex="-1" id="stafflySidebar" aria-labelledby="stafflySidebarLabel">
            <div class="offcanvas-header border-bottom border-secondary border-opacity-25 px-4 py-3 d-lg-none">
                <a href="./dashboard.php" class="d-flex align-items-center gap-2 text-white text-decoration-none fw-bold" id="stafflySidebarLabel">
                    <img src="./assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                    <span>Staffly</span>
                </a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#stafflySidebar" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column justify-content-between h-100 p-4">
                <div>
                    <!-- Brand Logo (Desktop) -->
                    <a href="./dashboard.php" class="d-none d-lg-flex align-items-center gap-2 text-white text-decoration-none fw-bold fs-5 mb-1">
                        <img src="./assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                        <span>Staffly</span>
                    </a>
                    <p class="d-none d-lg-block small text-secondary mb-4 ps-1">Workforce Directory</p>

                    <div class="small text-uppercase fw-bold text-secondary mb-2 px-2" style="font-size: 0.7rem; letter-spacing: 0.06em;">
                        Menu
                    </div>

                    <nav class="d-flex flex-column gap-2">
                        <a href="./dashboard.php" class="sidebar-menu-link active">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="./pegawai/index.php" class="sidebar-menu-link">
                            <i class="bi bi-people-fill"></i>
                            <span>Employees</span>
                        </a>
                    </nav>
                </div>

                <!-- User Info & Logout di Bawah Sidebar -->
                <div class="pt-4 border-top border-secondary border-opacity-25">
                    <div class="p-3 rounded-4 mb-3" style="background-color: var(--brand-card); border: 1px solid var(--brand-border);">
                        <div class="small text-secondary">Signed in as</div>
                        <div class="small fw-bold text-white text-truncate">
                            <?= e($_SESSION['nama_lengkap'] ?? 'HR Administrator') ?>
                        </div>
                    </div>

                    <a href="./logout.php" class="btn btn-neon w-100 py-2 text-decoration-none">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </div>
            </div>
        </aside>

        <!-- KONTEN UTAMA KANAN -->
        <div class="flex-grow-1 d-flex flex-column" style="min-width: 0;">

            <!-- Header Khusus Layar HP/Tablet -->
            <div class="mobile-topbar d-flex d-lg-none align-items-center justify-content-between px-3 py-3">
                <a href="./dashboard.php" class="d-flex align-items-center gap-2 text-white text-decoration-none fw-bold">
                    <img src="./assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                    <span>Staffly</span>
                </a>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#stafflySidebar" aria-controls="stafflySidebar">
                    <i class="bi bi-list me-1"></i> Menu
                </button>
            </div>

            <!-- Hero Banner (Tombol Atas-Bawah, Lebar Sama & Lurus Presisi) -->
            <section class="dashboard-hero-banner px-3 px-lg-5">
                <div class="container-fluid" style="max-width: 1140px;">
                    <div class="row align-items-end g-4">
                        <div class="col-lg-8">
                            <p class="small fw-semibold text-dark opacity-75 mb-2">
                                Welcome back, <?= e($_SESSION['nama_lengkap'] ?? 'HR Administrator') ?> · Workforce Operations
                            </p>
                            <h1 class="display-6 fw-bold text-dark mb-3">Employee Overview &amp; Team Directory</h1>
                            <p class="text-dark opacity-75 mb-0" style="max-width: 560px;">
                                Monitor total active headcount, browse verified employee records, and update team profiles seamlessly from one unified workspace.
                            </p>
                        </div>
                        <div class="col-lg-4 d-flex flex-column align-items-start align-items-lg-end">
                            <div class="d-flex flex-column gap-2" style="width: 196px;">
                                <a href="./pegawai/index.php" class="btn-hero-action btn-hero-light w-100">
                                    <span>View Employees</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                <a href="./pegawai/tambah.php" class="btn-hero-action btn-hero-dark w-100">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Add Employee</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stat Cards & Recent Employees -->
            <main class="container-fluid px-3 px-lg-5 py-5 flex-grow-1" style="max-width: 1140px;">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="staffly-card card-hover-lift p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small fw-semibold text-secondary">Total Employees</span>
                                <i class="bi bi-people fs-5" style="color: #84f03a;"></i>
                            </div>
                            <div class="display-6 fw-bold text-white font-mono-tabular"><?= $totalEmployees ?></div>
                            <p class="small text-secondary mb-0 mt-1">Active records in directory</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="staffly-card card-hover-lift p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small fw-semibold text-secondary">Active Roles</span>
                                <i class="bi bi-briefcase fs-5" style="color: #84f03a;"></i>
                            </div>
                            <div class="display-6 fw-bold text-white font-mono-tabular"><?= $totalRoles ?></div>
                            <p class="small text-secondary mb-0 mt-1">Distinct job titles across teams</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="staffly-card card-hover-lift p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small fw-semibold text-secondary">Verified Contacts</span>
                                <i class="bi bi-telephone fs-5" style="color: #84f03a;"></i>
                            </div>
                            <div class="display-6 fw-bold text-white font-mono-tabular"><?= $verifiedPhones ?></div>
                            <p class="small text-secondary mb-0 mt-1">Profiles with active phone numbers</p>
                        </div>
                    </div>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-7">
                        <div class="staffly-card card-hover-lift overflow-hidden position-relative h-100 d-flex flex-column justify-content-end" style="min-height: 300px;">
                            <img src="./assets/images/banner-dashboard.jpg" alt="Human resources management illustration"
                                 class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                                 onerror="this.src='./assets/images/banner-dashboard.png';">
                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                 style="background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.25));"></div>
                            <div class="position-relative p-4 p-sm-5">
                                <h2 class="h4 fw-bold text-white mb-2">Centralized Workforce Records</h2>
                                <p class="small text-light opacity-75 mb-0" style="max-width: 420px;">
                                    Keep employee IDs, role assignments, photos, and contact details organized and ready to update anytime.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="staffly-panel p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h2 class="h5 fw-bold text-white mb-0">Recent Employees</h2>
                                    <a href="./pegawai/index.php" class="small fw-semibold text-decoration-none" style="color: #84f03a;">View All</a>
                                </div>

                                <?php if (empty($recentEmployees)): ?>
                                    <p class="small text-secondary py-4 text-center">No employee records saved yet.</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($recentEmployees as $emp): ?>
                                            <a href="./pegawai/detail.php?id=<?= (int) $emp['id'] ?>"
                                               class="list-group-item bg-transparent border-secondary border-opacity-25 px-0 py-3 d-flex justify-content-between align-items-center text-decoration-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if (!empty($emp['foto'])): ?>
                                                        <img src="./assets/images/employees/<?= e($emp['foto']) ?>" alt="<?= e($emp['nama']) ?>" class="avatar-circle">
                                                    <?php else: ?>
                                                        <span class="avatar-initials"><?= e(getInitials($emp['nama'])) ?></span>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="small fw-bold text-white"><?= e($emp['nama']) ?></div>
                                                        <div class="small text-secondary"><?= e($emp['jabatan']) ?> · <span class="font-mono-tabular"><?= e($emp['nip']) ?></span></div>
                                                    </div>
                                                </div>
                                                <i class="bi bi-arrow-right text-secondary"></i>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <a href="./pegawai/tambah.php" class="btn btn-neon w-100 py-2.5 mt-4 text-decoration-none">
                                + Add New Employee
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Chart: Employees by Role -->
                <?php if (!empty($chartData)): ?>
                <div class="row g-4 mt-0">
                    <div class="col-12">
                        <div class="staffly-panel p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h2 class="h5 fw-bold text-white mb-1">Employees by Role</h2>
                                    <p class="small text-secondary mb-0">Distribution of headcount across job positions</p>
                                </div>
                                <!-- Dropdown Export di Dashboard -->
                                <div class="dropdown">
                                    <button class="btn btn-sm rounded-pill fw-bold px-4 py-2 d-flex align-items-center gap-2 dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            style="background-color: var(--brand-card); border: 1px solid #32363e; color: #84f03a; font-size: 0.8rem;">
                                        <i class="bi bi-box-arrow-up"></i> Export
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow mt-2"
                                        style="background-color: #1b1d22; border: 1px solid #32363e; border-radius: 14px; min-width: 170px; padding: 6px;">
                                        <li>
                                            <a href="./proses/export_csv.php"
                                               class="dropdown-item rounded-3 d-flex align-items-center gap-2 small fw-semibold"
                                               style="color: #e5e7eb; padding: 10px 14px;">
                                                <i class="bi bi-filetype-csv" style="color: #84f03a;"></i> Export as CSV
                                            </a>
                                        </li>
                                        <li>
                                            <a href="./pegawai/index.php#export-pdf"
                                               onclick="localStorage.setItem('triggerPdfExport','1'); return true;"
                                               class="dropdown-item rounded-3 d-flex align-items-center gap-2 small fw-semibold"
                                               style="color: #e5e7eb; padding: 10px 14px;">
                                                <i class="bi bi-filetype-pdf" style="color: #84f03a;"></i> Export as PDF
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row g-4 align-items-center">
                                <!-- Kolom Kiri: Bar Chart -->
                                <div class="col-lg-7">
                                    <div style="height: 220px; position: relative;">
                                        <canvas id="roleChart"></canvas>
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Top Roles & Headcount Breakdown -->
                                <div class="col-lg-5 chart-divider">
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                                        <span class="small fw-semibold text-secondary text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Top Headcount Roles</span>
                                        <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(132, 240, 58, 0.12); color: #84f03a; border: 1px solid rgba(132, 240, 58, 0.3); font-size: 0.75rem;">
                                            <?= count($chartData) ?> Active Roles
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column gap-3">
                                        <?php foreach (array_slice($chartData, 0, 4) as $item): 
                                            $pct = $totalEmployees > 0 ? round(($item['total'] / $totalEmployees) * 100) : 0;
                                        ?>
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                                    <span class="text-white fw-semibold" style="font-size: 0.85rem;"><?= e($item['jabatan']) ?></span>
                                                    <span class="text-secondary font-mono-tabular" style="font-size: 0.8rem;"><?= (int) $item['total'] ?> emp <span class="opacity-75">(<?= $pct ?>%)</span></span>
                                                </div>
                                                <div class="progress" style="height: 6px; background-color: #272a30; border-radius: 999px;">
                                                    <div class="progress-bar" role="progressbar" style="width: <?= $pct ?>%; background-color: #84f03a; border-radius: 999px;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>

            <footer class="container-fluid px-3 px-lg-5 pb-4 mt-auto" style="max-width: 1140px;">
                <div class="staffly-footer p-4 p-sm-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-3 border-bottom border-dark border-opacity-10">
                        <div>
                            <h3 class="h4 fw-bold mb-1">Staffly</h3>
                            <p class="small mb-0 opacity-75">Structured employee record management workspace.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-3 small">
                            <a href="./dashboard.php">Dashboard</a>
                            <a href="./pegawai/index.php">Employees</a>
                            <a href="./logout.php">Logout</a>
                        </div>
                    </div>
                    <div class="pt-3 small opacity-75">© 2026 Staffly. Employee Management System.</div>
                </div>
            </footer>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="./assets/js/script.js"></script>
    <script>
        // Hover styling dropdown di dashboard
        document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(el => {
            el.addEventListener('mouseenter', () => {
                el.style.backgroundColor = 'rgba(132, 240, 58, 0.1)';
                el.style.color = '#ffffff';
            });
            el.addEventListener('mouseleave', () => {
                el.style.backgroundColor = '';
                el.style.color = '#e5e7eb';
            });
        });

        // Chart: Pegawai per Jabatan
        const ctx = document.getElementById('roleChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= $chartLabels ?>,
                    datasets: [{
                        label: 'Employees',
                        data: <?= $chartValues ?>,
                        backgroundColor: 'rgba(132, 240, 58, 0.25)',
                        borderColor: '#84f03a',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 28,
                        barPercentage: 0.85,
                        categoryPercentage: 0.8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1b1d22',
                            borderColor: '#272a30',
                            borderWidth: 1,
                            titleColor: '#84f03a',
                            bodyColor: '#e5e7eb',
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: '#9ca3af', font: { size: 11 } },
                            grid: { display: false }
                        },
                        y: {
                            ticks: { color: '#9ca3af', stepSize: 1, precision: 0, font: { size: 11 } },
                            grid: { color: 'rgba(255,255,255,0.05)' },
                            beginAtZero: true,
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>