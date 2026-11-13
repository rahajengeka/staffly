<?php
// debug.php - Cek semua masalah login
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Debug Login</title>
<style>
body{font-family:sans-serif;padding:20px;max-width:700px;margin:0 auto;}
.ok{color:green;font-weight:bold;} .err{color:red;font-weight:bold;}
.box{background:#f5f5f5;border:1px solid #ddd;padding:15px;border-radius:8px;margin:12px 0;}
</style>
</head>
<body>
<h2>🔍 Debug Login</h2>

<?php
// 1. Cek koneksi database
echo '<div class="box"><b>1. Koneksi Database</b><br>';
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=db_pegawai;charset=utf8mb4',
        'root', '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo '<span class="ok">✅ Koneksi berhasil ke db_pegawai</span>';
} catch (PDOException $e) {
    echo '<span class="err">❌ Koneksi GAGAL: ' . $e->getMessage() . '</span>';
    echo '</div></body></html>';
    exit;
}
echo '</div>';

// 2. Cek tabel users ada
echo '<div class="box"><b>2. Cek Tabel Users</b><br>';
try {
    $res = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
    if ($res) {
        echo '<span class="ok">✅ Tabel users ada</span>';
    } else {
        echo '<span class="err">❌ Tabel users TIDAK ada! Perlu import database.sql</span>';
        echo '</div></body></html>';
        exit;
    }
} catch(Exception $e) {
    echo '<span class="err">❌ Error: ' . $e->getMessage() . '</span>';
}
echo '</div>';

// 3. Cek data user admin
echo '<div class="box"><b>3. Data User di Database</b><br>';
$stmt = $pdo->query("SELECT id, username, password, nama_lengkap FROM users");
$users = $stmt->fetchAll();
if (empty($users)) {
    echo '<span class="err">❌ Tabel users KOSONG! Tidak ada data user sama sekali.</span>';
} else {
    foreach ($users as $u) {
        echo "ID: {$u['id']} | Username: <b>{$u['username']}</b> | Nama: {$u['nama_lengkap']}<br>";
        echo "Hash: <code style='font-size:11px'>{$u['password']}</code><br>";
    }
}
echo '</div>';

// 4. Test password_verify
echo '<div class="box"><b>4. Test password_verify(admin123)</b><br>';
$stmt2 = $pdo->prepare("SELECT password FROM users WHERE username = 'admin'");
$stmt2->execute();
$row = $stmt2->fetch();
if ($row) {
    $cocok = password_verify('admin123', $row['password']);
    if ($cocok) {
        echo '<span class="ok">✅ Password COCOK! Harusnya bisa login.</span>';
    } else {
        echo '<span class="err">❌ Password TIDAK cocok. Hash perlu diupdate.</span>';
        // Langsung update password
        $hashBaru = password_hash('admin123', PASSWORD_BCRYPT);
        $upd = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $upd->execute([$hashBaru]);
        echo '<br><span class="ok">🔧 Auto-fix: Password sudah diupdate! Coba login sekarang.</span>';
        echo '<br>Hash baru: <code style="font-size:11px">' . $hashBaru . '</code>';
    }
} else {
    echo '<span class="err">❌ User admin tidak ditemukan!</span>';
    // Insert user admin
    $hashBaru = password_hash('admin123', PASSWORD_BCRYPT);
    $ins = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES ('admin', ?, 'Administrator HR')");
    $ins->execute([$hashBaru]);
    echo '<br><span class="ok">🔧 Auto-fix: User admin berhasil dibuat! Coba login sekarang.</span>';
}
echo '</div>';
?>

<div class="box">
<b>5. Aksi</b><br><br>
<a href="login.php" style="background:#3498db;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;">
  👉 Coba Login Sekarang (admin / admin123)
</a>
</div>

</body>
</html>

