<?php
/**
 * config/koneksi.php
 * Koneksi database MySQL menggunakan PDO + try-catch
 * Skema Junior Web Developer - BNSP SKK-32-10/2024
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'db_pegawai');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

$pdo = null;

try {
    $dsn = 'mysql:host=' . DB_HOST
         . ';port=' . DB_PORT
         . ';dbname=' . DB_NAME
         . ';charset=' . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // Sembunyikan pesan teknis dari pengguna akhir
    error_log('Database connection error: ' . $e->getMessage());
    die('<div style="font-family:sans-serif;color:#c0392b;padding:20px;">
            <strong>Koneksi database gagal.</strong><br>
            Pastikan MySQL server berjalan dan konfigurasi di <code>config/koneksi.php</code> sudah benar.
         </div>');
}

