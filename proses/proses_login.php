<?php
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Username and password are required.';
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = :username LIMIT 1");
$stmt->execute([':username' => $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    header('Location: ../dashboard.php');
    exit;
}

$_SESSION['login_error'] = 'Invalid username or password.';
header('Location: ../login.php');
exit;
