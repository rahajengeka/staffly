DROP DATABASE IF EXISTS db_pegawai;
CREATE DATABASE db_pegawai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pegawai;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nip VARCHAR(30) UNIQUE NOT NULL,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    jabatan VARCHAR(80) NOT NULL,
    no_telepon VARCHAR(20) NULL,
    foto VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sample initial employee records (default admin user is auto-seeded with PHP password_hash on login.php)
INSERT INTO employees (nip, nama, email, jabatan, no_telepon, foto) VALUES
('199801152024011001', 'Budi Santoso', 'budi.santoso@company.com', 'Web Developer', '081234567890', NULL),
('199905202024012002', 'Siti Aminah', 'siti.aminah@company.com', 'UI/UX Designer', '081298765432', NULL),
('199611082023031003', 'Raka Pratama', 'raka.pratama@company.com', 'Backend Engineer', '081345678901', NULL),
('200002192024052004', 'Nadia Kusuma', 'nadia.kusuma@company.com', 'HR Specialist', '085612349876', NULL);
