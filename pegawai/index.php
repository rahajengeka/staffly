<?php
require_once '../config/koneksi.php';
requireAuth('../login.php');

$employees = $pdo->query("SELECT * FROM employees ORDER BY id DESC")->fetchAll();
$flashSuccess = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staffly — Employee Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=7">

    <style>
        /* Samakan warna dengan Login Page & Dashboard (Hitam, Putih, Hijau Neon) */
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

        .staffly-input {
            background-color: var(--brand-card) !important;
            border: 1px solid #32363e !important;
            color: #ffffff !important;
        }

        .staffly-input:focus {
            border-color: var(--brand-neon) !important;
            box-shadow: none !important;
        }

        /* Sidebar Layout (Fix overflow kanan dengan min-width: 0) */
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
            min-width: 0; /* Penting: cegah konten melebar keluar layar */
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

        /* Header Banner Lurus selaras dengan Dashboard */
        .page-hero-banner {
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
            padding: 36px 0;
        }

        .mobile-topbar {
            background-color: var(--brand-panel);
            border-bottom: 1px solid var(--brand-border);
        }

        /* Table Compact & Muat di Layar */
        .staffly-table {
            --bs-table-bg: transparent;
            --bs-table-color: #e5e7eb;
            --bs-table-border-color: #262930;
            --bs-table-hover-bg: #191b21;
            --bs-table-hover-color: #ffffff;
            --bs-table-striped-bg: rgba(255, 255, 255, 0.015);
            --bs-table-striped-color: #e5e7eb;
            font-size: 0.84rem;
        }

        .staffly-table th,
        .staffly-table td {
            padding: 12px 10px;
        }

        .avatar-circle,
        .avatar-initials {
            width: 32px;
            height: 32px;
            font-size: 0.68rem;
        }

        /* Tombol Actions Selaras (Details & Edit netral elegan, Delete merah) */
        .btn-action-neutral {
            background-color: var(--brand-card);
            border: 1px solid #32363e;
            color: #e5e7eb;
            font-size: 0.76rem;
            font-weight: 600;
            border-radius: 9999px;
            padding: 5px 12px;
            text-decoration: none;
            transition: border-color 0.2s ease, color 0.2s ease, background-color 0.2s ease;
        }

        .btn-action-neutral:hover {
            border-color: var(--brand-neon);
            color: var(--brand-neon);
            background-color: rgba(132, 240, 58, 0.08);
        }

        .btn-action-delete {
            background-color: rgba(220, 53, 69, 0.14);
            border: 1px solid rgba(220, 53, 69, 0.45);
            color: #f87171;
            font-size: 0.76rem;
            font-weight: 600;
            border-radius: 9999px;
            padding: 5px 12px;
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .btn-action-delete:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="staffly-shell">

        <!-- SIDEBAR (Menu Employees Aktif) -->
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

        <!-- KONTEN UTAMA KANAN -->
        <div class="staffly-main-area">

            <!-- Header Khusus Layar HP/Tablet -->
            <div class="mobile-topbar d-flex d-lg-none align-items-center justify-content-between px-3 py-3">
                <a href="../dashboard.php" class="d-flex align-items-center gap-2 text-white text-decoration-none fw-bold">
                    <img src="../assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                    <span>Staffly</span>
                </a>
                <button class="btn btn-sm btn-outline-light rounded-pill px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#stafflySidebar" aria-controls="stafflySidebar">
                    <i class="bi bi-list me-1"></i> Menu
                </button>
            </div>

            <!-- Page Header Banner Lurus + Tombol Add Employee -->
            <section class="page-hero-banner px-3 px-lg-4">
                <div class="container-fluid" style="max-width: 1140px;">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-3">
                        <div>
                            <span class="small font-mono-tabular fw-bold text-dark opacity-75 d-block mb-1">Team Directory</span>
                            <h1 class="h2 fw-bold text-dark mb-1">Employee Records</h1>
                            <p class="small text-dark opacity-75 mb-0">
                                Browse all registered employees, inspect profiles, edit data, or delete rows dynamically.
                            </p>
                        </div>
                        <a href="./tambah.php" class="btn btn-dark rounded-pill px-4 py-2 text-decoration-none small fw-bold flex-shrink-0" style="color: #84f03a;">
                            <i class="bi bi-plus-lg me-1"></i>Add Employee
                        </a>
                    </div>
                </div>
            </section>

            <!-- Tabel Data Pegawai -->
            <main class="container-fluid px-3 px-lg-4 py-4 flex-grow-1" style="max-width: 1140px;">
                <?php if ($flashSuccess): ?>
                    <div class="alert alert-success alert-dismissible fade show small" role="alert">
                        <?= e($flashSuccess) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div id="ajaxAlertBox" class="alert d-none small" role="alert"></div>

                <div class="staffly-panel p-3 p-lg-4">
                    <div class="mb-3" style="max-width: 340px;">
                        <input type="search" id="searchEmployee" class="form-control staffly-input rounded-pill" placeholder="Search by ID, name, email, or role...">
                    </div>

                    <div class="table-responsive">
                        <table class="table staffly-table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr class="small text-secondary text-uppercase">
                                    <th>No</th>
                                    <th>Employee ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                                <?php if (empty($employees)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-secondary">No employee records found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($employees as $idx => $emp): ?>
                                        <tr data-row>
                                            <td class="font-mono-tabular text-secondary"><?= $idx + 1 ?></td>
                                            <td class="font-mono-tabular text-nowrap"><?= e($emp['nip']) ?></td>
                                            <td class="fw-bold text-white">
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($emp['foto'])): ?>
                                                        <img src="../assets/images/employees/<?= e($emp['foto']) ?>" alt="<?= e($emp['nama']) ?>" class="avatar-circle">
                                                    <?php else: ?>
                                                        <span class="avatar-initials"><?= e(getInitials($emp['nama'])) ?></span>
                                                    <?php endif; ?>
                                                    <span><?= e($emp['nama']) ?></span>
                                                </div>
                                            </td>
                                            <td class="text-secondary"><?= e($emp['email']) ?></td>
                                            <td><?= e($emp['jabatan']) ?></td>
                                            <td class="text-end text-nowrap">
                                                <a href="./detail.php?id=<?= (int) $emp['id'] ?>" class="btn btn-action-neutral me-1">Details</a>
                                                <a href="./edit.php?id=<?= (int) $emp['id'] ?>" class="btn btn-action-neutral me-1">Edit</a>
                                                <button type="button"
                                                        class="btn btn-action-delete btn-delete-employee"
                                                        data-id="<?= (int) $emp['id'] ?>"
                                                        data-name="<?= e($emp['nama']) ?>"
                                                        data-nip="<?= e($emp['nip']) ?>">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content staffly-card text-white">
                <div class="modal-header border-secondary border-opacity-25">
                    <h5 class="modal-title fw-bold">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body small text-secondary">
                    Are you sure you want to remove <strong class="text-white" id="deleteEmployeeName"></strong>
                    (ID: <code id="deleteEmployeeNip" style="color: #84f03a;"></code>) from the directory?
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold">Delete Record</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>