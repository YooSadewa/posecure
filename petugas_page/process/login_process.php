<?php
session_start();
include '../../koneksi_database.php';

if (empty($_POST["username"])) {
  header("location:../app/login_page.php?pesan=username_wajib_isi");
  exit;
} else if (empty($_POST["password"])) {
  header("location:../app/login_page.php?pesan=password_wajib_isi&value_username=" . $_POST['username']);
  exit;
}

$username = mysqli_real_escape_string($conn, $_POST["username"]);
$password = mysqli_real_escape_string($conn, $_POST["password"]);

$query = "SELECT * FROM user WHERE username = '$username' AND role IN ('admin', 'petugas_keamanan')";

$result = mysqli_query($conn, $query);
$row = mysqli_num_rows($result);

if ($row > 0) {
  $data = mysqli_fetch_assoc($result);
  if (password_verify($password, $data["password"])) {

    if ($data["role"] === "petugas_keamanan") {
      $query_petugas = mysqli_query($conn, "SELECT id_alamat, status_keaktifan FROM petugas_keamanan WHERE id_user = '" . $data['id_user'] . "'");

      if (mysqli_num_rows($query_petugas) > 0) {
        $data_petugas = mysqli_fetch_assoc($query_petugas);

        if ($data_petugas['status_keaktifan'] !== 'aktif') {
          $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Login Gagal!',
            'text' => 'Akun Anda berstatus cuti. Silahkan hubungi admin poSecure untuk perubahan status.',
            'timer' => 3000
          ];
          header("location:../app/login_page.php?pesan=akun_tidak_aktif&value_username=" . $_POST['username']);
          exit;
        }

        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['foto'] = $data['foto'];
        $_SESSION['id_alamat'] = $data_petugas['id_alamat'];
        $_SESSION['alert'] = [
          'icon' => 'success',
          'title' => 'Login Berhasil!',
          'timer' => 1500
        ];
        header("location:../app/dashboard.php");
      } else {
        $_SESSION['alert'] = [
          'icon' => 'error',
          'title' => 'Login Gagal!',
          'text' => 'Data petugas tidak ditemukan.',
          'timer' => 1500
        ];
        header("location:../app/login_page.php?pesan=data_petugas_tidak_ditemukan&value_username=" . $_POST['username']);
        exit;
      }
    }
    else if ($data["role"] === "admin") {
      $_SESSION['login'] = true;
      $_SESSION['id_user'] = $data['id_user'];
      $_SESSION['username'] = $data['username'];
      $_SESSION['nama'] = $data['nama'];
      $_SESSION['role'] = $data['role'];
      $_SESSION['foto'] = $data['foto'];
      $_SESSION['alert'] = [
        'icon' => 'success',
        'title' => 'Login Berhasil!',
        'timer' => 1500
      ];
      header("location:../../admin/app/dashboard_page.php");
    }
  } else {
    $_SESSION['alert'] = [
      'icon' => 'error',
      'title' => 'Login Gagal!',
      'text' => 'Password yang Anda masukkan salah.',
      'timer' => 1500
    ];
    header("location:../app/login_page.php?pesan=password_salah&value_username=" . $_POST['username']);
    exit;
  }
} else {
  $_SESSION['alert'] = [
    'icon' => 'error',
    'title' => 'Login Gagal!',
    'text' => 'Username tidak ditemukan.',
    'timer' => 1500
  ];
  header("location:../app/login_page.php?pesan=pengguna_tidak_ditemukan&value_username=" . $_POST['username']);
  exit;
}