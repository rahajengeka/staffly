<?php
// fix_password.php - File temporary untuk fix password admin
// HAPUS file ini setelah berhasil!

require_once 'config/koneksi.php';

$hashBaru = password_hash('admin123', PASSWORD_BCRYPT);

$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = 'admin'");
$stmt->execute([':password' => $hashBaru]);

if ($stmt->rowCount() > 0) {
    echo '<h2 style="color:green">✅ Password berhasil diupdate!</h2>';
    echo '<p>Login sekarang dengan: <strong>admin / admin123</strong></p>';
    echo '<a href="login.php">👉 Ke halaman Login</a>';
} else {
    echo '<h2 style="color:red">❌ Gagal - pastikan tabel users sudah ada di database db_pegawai</h2>';
}
?>

