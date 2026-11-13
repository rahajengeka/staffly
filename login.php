<?php
require_once './config/koneksi.php';
ensureDefaultAdmin($pdo);

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staffly — Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css?v=6">

    <style>
        /* Khusus Landing / Login Page: Side-by-Side & Pembatas Lurus */
        .login-split-wrapper {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
            width: 100%;
        }

        /* Sisi Kiri: Pakai Gambar Latar */
        .login-hero-side {
            flex: 1 1 52%;
            background-color: #0c0d0e;
            background-image:
                linear-gradient(rgba(12, 13, 14, 0.55), rgba(12, 13, 14, 0.65)),
                url('./assets/images/banner-hero.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #ffffff;
            border-radius: 0;
            padding: 64px 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-hero-inner {
            width: 100%;
            max-width: 520px;
        }

        /* Sisi Kanan: Gradient Putih -> Hijau + Card Sign In */
        .login-form-side {
            flex: 1 1 48%;
            background: linear-gradient(
                155deg,
                #ffffff 0%,
                #f4f5f7 38%,
                #cff9a4 72%,
                #84f03a 100%
            );
            border-left: 1px solid rgba(0, 0, 0, 0.08);
            padding: 64px 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-form-side .staffly-card {
            width: 100%;
            max-width: 440px;
            background-color: #141518;
            border: 1px solid #272a30;
            border-radius: 24px;
        }

        .login-form-side .staffly-input {
            background-color: #1b1d22;
            border-color: #32363e;
        }

        @media (max-width: 991.98px) {
            .login-split-wrapper {
                flex-direction: column;
            }

            .login-hero-side {
                padding: 56px 24px 44px;
            }

            .login-form-side {
                padding: 40px 20px 56px;
                border-left: none;
                border-top: 1px solid rgba(0, 0, 0, 0.08);
            }
        }
    </style>
</head>
<body>
    <main class="login-split-wrapper">
        <!-- SISI KIRI: Hero Employee Records dengan Gambar Latar -->
        <section class="login-hero-side">
            <div class="login-hero-inner text-center text-lg-start">
                <div class="d-inline-flex align-items-center gap-2 mb-3">
                    <img src="./assets/images/logo.png" alt="Staffly Logo" class="staffly-logo">
                    <span class="fw-bold text-white">Staffly</span>
                </div>
                <h1 class="display-5 fw-bold text-white mb-3">Employee Records &amp; Directory</h1>
                <p class="text-light opacity-75 mb-0" style="max-width: 480px;">
                    Sign in to the administrator workspace to manage employee profiles, job titles, and verified team contacts.
                </p>
            </div>
        </section>

        <!-- SISI KANAN: Sign In Card di atas Gradient Putih -> Hijau -->
        <section class="login-form-side">
            <div class="staffly-card p-4 p-sm-5 shadow-lg">
                <h2 class="h5 fw-bold text-white mb-1">Sign In</h2>
                <p class="text-secondary small mb-4">Enter your credentials to access the Staffly dashboard.</p>

                <div id="clientErrorBox" class="alert alert-danger py-2 px-3 small <?= $error ? '' : 'd-none' ?>" role="alert">
                    <?= e($error) ?>
                </div>

                <form id="loginForm" action="./proses/proses_login.php" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label text-light small fw-semibold">Username</label>
                        <input type="text" id="username" name="username" class="form-control staffly-input" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label text-light small fw-semibold">Password</label>
                        <input type="password" id="password" name="password" class="form-control staffly-input" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-neon w-100 py-2.5">Sign In</button>
                </form>

                <div class="mt-4 pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center small text-secondary">
                    <span>Demo: <code class="text-light">admin</code> / <code class="text-light">admin123</code></span>
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold" style="color: #84f03a;"
                        onclick="document.getElementById('username').value='admin'; document.getElementById('password').value='admin123';">
                        Fill Demo
                    </button>
                </div>
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>
</body>
</html>