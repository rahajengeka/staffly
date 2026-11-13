<?php
/**
 * proses/proses_edit.php
 * Validasi server-side & query UPDATE PDO
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Tolak request selain POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pegawai/index.php');
    exit;
}

// Guard: wajib login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/koneksi.php';

// ---- Ambil & validasi ID ----
$idPegawai = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$idPegawai || $idPegawai <= 0) {
    $_SESSION['flash_error'] = 'ID pegawai tidak valid.';
    header('Location: ../pegawai/index.php');
    exit;
}

// ---- Ambil & trim input ----
$nipPegawai     = trim($_POST['nip']        ?? '');
$namaPegawai    = trim($_POST['nama']       ?? '');
$emailPegawai   = trim($_POST['email']      ?? '');
$jabatanPegawai = trim($_POST['jabatan']    ?? '');
$noTelepon      = trim($_POST['no_telepon'] ?? '');

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
    header('Location: ../pegawai/edit.php?id=' . $idPegawai);
    exit;
}

// ---- Eksekusi UPDATE dengan PDO Prepared Statement ----
try {
    $stmtUpdate = $pdo->prepare(
        'UPDATE employees
         SET nip = :nip, nama = :nama, email = :email, jabatan = :jabatan, no_telepon = :no_telepon
         WHERE id = :id'
    );
    $stmtUpdate->execute([
        ':nip'        => $nipPegawai,
        ':nama'       => $namaPegawai,
        ':email'      => $emailPegawai,
        ':jabatan'    => $jabatanPegawai,
        ':no_telepon' => $noTelepon ?: null,
        ':id'         => $idPegawai,
    ]);

    $_SESSION['flash_success'] = 'Data pegawai ' . $namaPegawai . ' berhasil diperbarui.';
    header('Location: ../pegawai/index.php');
    exit;

} catch (PDOException $e) {
    error_log('Update employee error: ' . $e->getMessage());

    if ($e->getCode() === '23000') {
        $_SESSION['flash_error'] = 'NIP ' . $nipPegawai . ' sudah digunakan pegawai lain.';
    } else {
        $_SESSION['flash_error'] = 'Terjadi kesalahan pada server saat memperbarui data.';
    }

    $_SESSION['old_input'] = [
        'nip'        => $nipPegawai,
        'nama'       => $namaPegawai,
        'email'      => $emailPegawai,
        'jabatan'    => $jabatanPegawai,
        'no_telepon' => $noTelepon,
    ];
    header('Location: ../pegawai/edit.php?id=' . $idPegawai);
    exit;
}

