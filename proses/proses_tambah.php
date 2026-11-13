<?php
require_once '../config/koneksi.php';
requireAuth('../login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pegawai/index.php');
    exit;
}

$nip = trim($_POST['nip'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$jabatan = trim($_POST['jabatan'] ?? '');
$noTelepon = trim($_POST['no_telepon'] ?? '');

if ($nip === '' || $nama === '' || $email === '' || $jabatan === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['form_error'] = 'Please fill all required fields with a valid email address.';
    header('Location: ../pegawai/tambah.php');
    exit;
}

$checkStmt = $pdo->prepare("SELECT id FROM employees WHERE nip = :nip LIMIT 1");
$checkStmt->execute([':nip' => $nip]);
if ($checkStmt->fetch()) {
    $_SESSION['form_error'] = 'Employee ID (NIP) is already registered.';
    header('Location: ../pegawai/tambah.php');
    exit;
}

$photoFilename = null;
if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($ext, $allowed, true)) {
        $uploadDir = '../assets/images/employees/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $photoFilename = uniqid('emp_', true) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $photoFilename);
    }
}

$stmt = $pdo->prepare("
    INSERT INTO employees (nip, nama, email, jabatan, no_telepon, foto)
    VALUES (:nip, :nama, :email, :jabatan, :no_telepon, :foto)
");
$stmt->execute([
    ':nip' => $nip,
    ':nama' => $nama,
    ':email' => $email,
    ':jabatan' => $jabatan,
    ':no_telepon' => $noTelepon !== '' ? $noTelepon : null,
    ':foto' => $photoFilename,
]);

$_SESSION['flash_success'] = "Employee \"{$nama}\" has been added.";
header('Location: ../pegawai/index.php');
exit;
