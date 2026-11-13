-- ============================================================
-- DATABASE: db_pegawai
-- Skema Junior Web Developer - BNSP SKK-32-10/2024
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_pegawai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pegawai;

-- -------------------------------------------------------
-- Tabel users: untuk autentikasi admin/petugas
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  UNIQUE NOT NULL,
    password      VARCHAR(255) NOT NULL,
    nama_lengkap  VARCHAR(100) NOT NULL,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- Tabel employees: catatan data pegawai
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS employees (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nip         VARCHAR(30)  UNIQUE NOT NULL,
    nama        VARCHAR(100) NOT NULL,
    email       VARCHAR(100) NOT NULL,
    jabatan     VARCHAR(80)  NOT NULL,
    no_telepon  VARCHAR(20),
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- -------------------------------------------------------
-- Data awal: akun admin (password: admin123)
-- Hash dibuat dengan password_hash('admin123', PASSWORD_BCRYPT)
-- -------------------------------------------------------
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$10$VraJ2RJqbsG8noGPrFJJcuhifFexC7pd2paOdjBpR0E1qAOMq6k3u', 'Administrator HR');

-- -------------------------------------------------------
-- Data awal pegawai untuk pengujian tampilan tabel
-- -------------------------------------------------------
INSERT INTO employees (nip, nama, email, jabatan, no_telepon) VALUES
('199801152024011001', 'Budi Santoso',   'budi.santoso@perusahaan.id',   'Web Developer',  '081234567890'),
('199905202024012002', 'Siti Aminah',    'siti.aminah@perusahaan.id',    'UI/UX Designer', '081298765432'),
('200001012024013003', 'Ahmad Fauzi',    'ahmad.fauzi@perusahaan.id',    'Backend Developer', '082345678901'),
('199712032024014004', 'Dewi Rahayu',    'dewi.rahayu@perusahaan.id',    'Project Manager', '083456789012');

