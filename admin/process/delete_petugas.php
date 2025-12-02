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

    $get_warga = mysqli_query($conn, "SELECT id_user FROM warga WHERE id_alamat = '$id_alamat'");
    $warga_ids = [];
    while ($warga = mysqli_fetch_assoc($get_warga)) {
      $warga_ids[] = $warga['id_user'];
    }

    $del_petugas = mysqli_query($conn, "DELETE FROM petugas_keamanan WHERE id_user = '$id_user'");

    $del_warga = mysqli_query($conn, "DELETE FROM warga WHERE id_alamat = '$id_alamat'");

    $del_alamat = mysqli_query($conn, "DELETE FROM alamat WHERE id_alamat = '$id_alamat'");

    $del_user_petugas = mysqli_query($conn, "DELETE FROM user WHERE id_user = '$id_user'");

    $del_user_warga = true;
    foreach ($warga_ids as $warga_id) {
      $result = mysqli_query($conn, "DELETE FROM user WHERE id_user = '$warga_id'");
      if (!$result) {
        $del_user_warga = false;
      }
    }

    if ($del_petugas && $del_warga && $del_alamat && $del_user_petugas && $del_user_warga) {
      $jumlah_warga = count($warga_ids);
      $_SESSION['alert'] = [
        'icon' => 'success',
        'title' => 'Berhasil!',
        'text' => "Data petugas keamanan dan $jumlah_warga warga berhasil dihapus!"
      ];
    } else {
      $_SESSION['alert'] = [
        'icon' => 'error',
        'title' => 'Gagal!',
        'text' => 'Gagal menghapus akun petugas keamanan: ' . mysqli_error($conn)
      ];
    }
  } else {
    $_SESSION['alert'] = [
      'icon' => 'error',
      'title' => 'Gagal!',
      'text' => 'Data petugas keamanan tidak ditemukan.'
    ];
  }
  header("Location: ../app/dashboard_page.php");
  exit;
} else {
  header("Location: ../app/dashboard_page.php");
}
