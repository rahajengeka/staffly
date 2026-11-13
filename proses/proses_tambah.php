<?php
/**
 * proses/proses_tambah.php
 * Validasi server-side & query INSERT PDO
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Tolak request selain POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pegawai/tambah.php');
    exit;
}

// Guard: wajib login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/koneksi.php';

// ---- Ambil & trim input ----
$nipPegawai     = trim($_POST['nip']         ?? '');
$namaPegawai    = trim($_POST['nama']        ?? '');
$emailPegawai   = trim($_POST['email']       ?? '');
$jabatanPegawai = trim($_POST['jabatan']     ?? '');
$noTelepon      = trim($_POST['no_telepon']  ?? '');

// ---- Validasi server-side ----
$errors = [];

if ($nipPegawai === '') {
    $errors[] = 'NIP tidak boleh kosong.';
} elseif (!preg_match('/^\d{15,20}$/', $nipPegawai)) {
    $errors[] = 'NIP harus berupa angka (15-20 digit).';
}

if ($namaPegawai === '') {
    $errors[] = 'Nama lengkap tidak boleh kosong.';
} elseif (strlen($namaPegawai) < 3) {
    $errors[] = 'Nama lengkap minimal 3 karakter.';
}

if ($emailPegawai === '') {
    $errors[] = 'Email tidak boleh kosong.';
} elseif (!filter_var($emailPegawai, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
}

if ($jabatanPegawai === '') {
    $errors[] = 'Jabatan tidak boleh kosong.';
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode(' ', $errors);
    $_SESSION['old_input']   = [
        'nip'        => $nipPegawai,
        'nama'       => $namaPegawai,
        'email'      => $emailPegawai,
        'jabatan'    => $jabatanPegawai,
        'no_telepon' => $noTelepon,
    ];
    header('Location: ../pegawai/tambah.php');
    exit;
}

// ---- Eksekusi INSERT dengan PDO Prepared Statement ----
try {
    $stmtInsert = $pdo->prepare(
        'INSERT INTO employees (nip, nama, email, jabatan, no_telepon)
         VALUES (:nip, :nama, :email, :jabatan, :no_telepon)'
    );
    $stmtInsert->execute([
        ':nip'        => $nipPegawai,
        ':nama'       => $namaPegawai,
        ':email'      => $emailPegawai,
        ':jabatan'    => $jabatanPegawai,
        ':no_telepon' => $noTelepon ?: null,
    ]);

    $_SESSION['flash_success'] = 'Data pegawai ' . $namaPegawai . ' berhasil ditambahkan.';
    header('Location: ../pegawai/index.php');
    exit;

} catch (PDOException $e) {
    error_log('Insert employee error: ' . $e->getMessage());

    // Tangani duplikat NIP
    if ($e->getCode() === '23000') {
        $_SESSION['flash_error'] = 'NIP ' . $nipPegawai . ' sudah terdaftar. Gunakan NIP yang berbeda.';
    } else {
        $_SESSION['flash_error'] = 'Terjadi kesalahan pada server saat menyimpan data.';
    }

    $_SESSION['old_input'] = [
        'nip'        => $nipPegawai,
        'nama'       => $namaPegawai,
        'email'      => $emailPegawai,
        'jabatan'    => $jabatanPegawai,
        'no_telepon' => $noTelepon,
    ];
    header('Location: ../pegawai/tambah.php');
    exit;
}

