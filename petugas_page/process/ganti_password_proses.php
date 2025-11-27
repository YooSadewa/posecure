<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_POST['id_user'])) {
    echo "<script>alert('Akses tidak valid'); window.location.href='../pages/dashboard.php';</script>";
    exit;
}

$id_user            = $_POST['id_user'];
$password_lama      = mysqli_real_escape_string($conn, $_POST['password_lama']);
$password_baru      = mysqli_real_escape_string($conn, $_POST['password_baru']);
$konfirmasi_password = mysqli_real_escape_string($conn, $_POST['konfirmasi_password']);

// Ambil password lama dari database
$q = mysqli_query($conn, "SELECT password FROM user WHERE id_user='$id_user'");
$data = mysqli_fetch_assoc($q);

// Validasi password lama
if (!password_verify($password_lama, $data['password'])) {
    echo "<script>alert('Password lama salah!'); window.history.back();</script>";
    exit;
}

// Validasi password baru konfirmasi
if ($password_baru !== $konfirmasi_password) {
    echo "<script>alert('Konfirmasi password baru tidak sama!'); window.history.back();</script>";
    exit;
}

// Encrypt password baru
$password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

// Update password
$update = mysqli_query($conn, "
    UPDATE user SET password='$password_hash' WHERE id_user='$id_user'
");

if ($update) {
    echo "<script>alert('Password berhasil diganti!'); window.location.href='../app/dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui password!'); window.history.back();</script>";
}
?>
