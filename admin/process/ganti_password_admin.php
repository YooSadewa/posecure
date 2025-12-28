<?php
session_start();
include '../../koneksi_database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_user = $_SESSION['id_user'];

  $password_lama   = $_POST['password_lama'];
  $password_baru   = $_POST['password_baru'];
  $password_konfirmasi = $_POST['password_konfir'];

  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM user WHERE id_user = '$id_user'"));

  if (password_verify($password_lama, $data['password'])) {
    if ($password_baru === $password_konfirmasi) {
      $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
      $update_password = mysqli_query($conn, "UPDATE user SET password = '$password_hash' WHERE id_user = '$id_user'");

      if (!$update_password) {
        $_SESSION['alert'] = [
          'icon' => 'error',
          'title' => 'Gagal!',
          'text' => 'Password gagal diperbarui.'
        ];
        header("Location: ../app/dashboard_page.php");
        exit;
      } else {
        $_SESSION['alert'] = [
          'icon' => 'success',
          'title' => 'Berhasil!',
          'text' => 'Password berhasil diperbarui.'
        ];
        header("Location: ../app/dashboard_page.php");
        exit;
      }
    } else {
      $_SESSION['alert'] = [
        'icon' => 'error',
        'title' => 'Gagal!',
        'text' => 'Konfirmasi password baru tidak cocok.'
      ];
      header("Location: ../app/dashboard_page.php");
      exit;
    }
  } else {
    $_SESSION['alert'] = [
      'icon' => 'error',
      'title' => 'Gagal!',
      'text' => 'Password lama salah, silahkan coba lagi.'
    ];
    header("Location: ../app/dashboard_page.php");
    exit;
  }
}
