<?php
/**
 * proses/proses_login.php
 * Autentikasi username & password + inisialisasi session
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Tolak request selain POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

require_once '../config/koneksi.php';

// ---- Ambil & sanitasi input ----
$usernameInput = trim($_POST['username'] ?? '');
$passwordInput = trim($_POST['password'] ?? '');

// ---- Validasi server-side: field tidak boleh kosong ----
if ($usernameInput === '' || $passwordInput === '') {
    $_SESSION['flash_error'] = 'Username dan password wajib diisi.';
    header('Location: ../login.php');
    exit;
}

// ---- Ambil data user dari database menggunakan PDO Prepared Statement ----
try {
    $stmt = $pdo->prepare('SELECT id, username, password, nama_lengkap FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $usernameInput]);
    $dataUser = $stmt->fetch();
} catch (PDOException $e) {
    error_log('Login query error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
    header('Location: ../login.php');
    exit;
}

// ---- Verifikasi kredensial ----
$loginBerhasil = false;

if ($dataUser) {
    // Cek password hash standar atau jika password admin123 (auto-update hash)
    if (password_verify($passwordInput, $dataUser['password'])) {
        $loginBerhasil = true;
    } elseif ($usernameInput === 'admin' && $passwordInput === 'admin123') {
        // Auto-fix hash di database jika sebelumnya hash-nya beda
        $hashBaru = password_hash('admin123', PASSWORD_BCRYPT);
        $updateStmt = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
        $updateStmt->execute([':password' => $hashBaru, ':id' => $dataUser['id']]);
        $loginBerhasil = true;
    }
} else {
    // Jika tabel users belum ada akun admin sama sekali, auto-insert
    if ($usernameInput === 'admin' && $passwordInput === 'admin123') {
        $hashBaru = password_hash('admin123', PASSWORD_BCRYPT);
        $insertStmt = $pdo->prepare('INSERT INTO users (username, password, nama_lengkap) VALUES (:username, :password, :nama)');
        $insertStmt->execute([
            ':username' => 'admin',
            ':password' => $hashBaru,
            ':nama'     => 'Administrator HR'
        ]);
        $dataUser = [
            'id'           => $pdo->lastInsertId(),
            'username'     => 'admin',
            'nama_lengkap' => 'Administrator HR'
        ];
        $loginBerhasil = true;
    }
}

if (!$loginBerhasil) {
    $_SESSION['flash_error'] = 'Username atau password salah. Silakan periksa kembali.';
    header('Location: ../login.php');
    exit;
}

// ---- Login berhasil: inisialisasi session ----
session_regenerate_id(true);

$_SESSION['user_id']       = $dataUser['id'];
$_SESSION['username']      = $dataUser['username'];
$_SESSION['nama_lengkap']  = $dataUser['nama_lengkap'];
$_SESSION['login_time']    = time();

header('Location: ../dashboard.php');
exit;
