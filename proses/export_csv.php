<?php
require_once '../config/koneksi.php';
requireAuth('../login.php');

$employees = $pdo->query("SELECT nip, nama, email, jabatan, no_telepon, created_at FROM employees ORDER BY id DESC")->fetchAll();

$filename = 'staffly_employees_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// BOM UTF-8 agar Excel bisa baca karakter spesial
fwrite($output, "\xEF\xBB\xBF");

// Header kolom CSV
fputcsv($output, ['Employee ID (NIP)', 'Full Name', 'Work Email', 'Role / Position', 'Phone Number', 'Created At']);

// Isi data
foreach ($employees as $emp) {
    fputcsv($output, [
        $emp['nip'],
        $emp['nama'],
        $emp['email'],
        $emp['jabatan'],
        $emp['no_telepon'] ?: '-',
        $emp['created_at'],
    ]);
}

fclose($output);
exit;

