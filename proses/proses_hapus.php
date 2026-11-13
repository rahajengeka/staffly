<?php
/**
 * proses/proses_hapus.php
 * Endpoint JSON untuk operasi DELETE via AJAX
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

session_start();

// Pastikan response selalu JSON
header('Content-Type: application/json; charset=UTF-8');

// ---- Guard: wajib login ----
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid. Silakan login kembali.']);
    exit;
}

// ---- Tolak request selain POST ----
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
    exit;
}

// ---- Ambil & validasi ID ----
$idPegawai = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$idPegawai || $idPegawai <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID pegawai tidak valid.']);
    exit;
}

require_once '../config/koneksi.php';

// ---- Periksa apakah data pegawai ada ----
try {
    $stmtCheck = $pdo->prepare('SELECT id FROM employees WHERE id = :id LIMIT 1');
    $stmtCheck->execute([':id' => $idPegawai]);

    if (!$stmtCheck->fetch()) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Data pegawai tidak ditemukan.']);
        exit;
    }

    // ---- Eksekusi DELETE dengan PDO Prepared Statement ----
    $stmtDelete = $pdo->prepare('DELETE FROM employees WHERE id = :id');
    $stmtDelete->execute([':id' => $idPegawai]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Data pegawai berhasil dihapus.',
    ]);
    exit;

} catch (PDOException $e) {
    error_log('Delete employee error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan pada server saat menghapus data.',
    ]);
    exit;
}

