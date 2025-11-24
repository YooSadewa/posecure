<?php
session_start();
include '../../koneksi_database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);

  $cek_alamat = mysqli_query($conn, "SELECT id_alamat FROM petugas_keamanan WHERE id_user = '$id_user'");
  $data       = mysqli_fetch_assoc($cek_alamat);

  if ($data) {
    $id_alamat = $data['id_alamat'];

    mysqli_query($conn, "DELETE FROM petugas_keamanan WHERE id_user = '$id_user'");

    mysqli_query($conn, "DELETE FROM user WHERE id_user = '$id_user'");

    mysqli_query($conn, "DELETE FROM alamat WHERE id_alamat = '$id_alamat'");
  }

  echo "<script>
        alert('Data akun petugas keamanan berhasil dihapus!');
        window.location.href = '../app/dashboard_page.php';
      </script>";
} else {
  header("Location: ../app/dashboard_page.php");
}
