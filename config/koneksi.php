<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = '127.0.0.1';
$dbName = 'db_pegawai';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Please check your MySQL configuration.');
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function getInitials(string $fullName): string
{
    $parts = preg_split('/\s+/', trim($fullName)) ?: [];
    $initials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    return $initials ?: 'ST';
}

function ensureDefaultAdmin(PDO $pdo): void
{
    $count = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (:username, :password, :nama_lengkap)");
        $stmt->execute([
            ':username' => 'admin',
            ':password' => password_hash('admin123', PASSWORD_DEFAULT),
            ':nama_lengkap' => 'HR Administrator',
        ]);
    }
}

function requireAuth(string $loginPath = 'login.php'): void
{
    if (empty($_SESSION['user_id'])) {
        header("Location: {$loginPath}");
        exit;
    }
}
