<?php
require_once '../config/koneksi.php';
requireAuth('../login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pegawai/index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nip = trim($_POST['nip'] ?? '');
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$jabatan = trim($_POST['jabatan'] ?? '');
$noTelepon = trim($_POST['no_telepon'] ?? '');

if (!$id || $nip === '' || $nama === '' || $email === '' || $jabatan === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['form_error'] = 'Please fill all required fields with a valid email address.';
    header("Location: ../pegawai/edit.php?id={$id}");
    exit;
}

$checkStmt = $pdo->prepare("SELECT id FROM employees WHERE nip = :nip AND id != :id LIMIT 1");
$checkStmt->execute([':nip' => $nip, ':id' => $id]);
if ($checkStmt->fetch()) {
    $_SESSION['form_error'] = 'Employee ID (NIP) is already assigned to another employee.';
    header("Location: ../pegawai/edit.php?id={$id}");
    exit;
}

$currentStmt = $pdo->prepare("SELECT foto FROM employees WHERE id = :id LIMIT 1");
$currentStmt->execute([':id' => $id]);
$current = $currentStmt->fetch();
$photoFilename = $current['foto'] ?? null;

if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($ext, $allowed, true)) {
        $uploadDir = '../assets/images/employees/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $newPhoto = uniqid('emp_', true) . '.' . $ext;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $newPhoto)) {
            $photoFilename = $newPhoto;
        }
    }
}

$stmt = $pdo->prepare("
    UPDATE employees
    SET nip = :nip, nama = :nama, email = :email, jabatan = :jabatan, no_telepon = :no_telepon, foto = :foto
    WHERE id = :id
");
$stmt->execute([
    ':nip' => $nip,
    ':nama' => $nama,
    ':email' => $email,
    ':jabatan' => $jabatan,
    ':no_telepon' => $noTelepon !== '' ? $noTelepon : null,
    ':foto' => $photoFilename,
    ':id' => $id,
]);

$_SESSION['flash_success'] = "Employee \"{$nama}\" has been updated.";
header('Location: ../pegawai/index.php');
exit;
