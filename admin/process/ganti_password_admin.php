<?php
session_start();
include '../../koneksi_database.php';

// cek hak ases jika user belum login atau user bukan admin maka tidak bisa akses ganti password dan user kembali ke halaman login.
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

// untuk memastikan halaman diakses lewat form POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  // mengambil id_user yang sedang login digunakan untuk menentukan password user mana yang akan diubah.
  $id_user = $_SESSION['id_user'];

  // mengambil input passowrd dari form.
  $password_lama   = $_POST['password_lama'];
  $password_baru   = $_POST['password_baru'];
  $password_konfirmasi = $_POST['password_konfir'];
  
  // mengambil password lama dari database berdasarkan id_user.
  // mysqli_fetch_assoc = mengubah hasil query menjadi array
  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM user WHERE id_user = '$id_user'"));
  
  // memverifikasi password lama.
  // password_verify() = untuk mencocokkan password lama yang diinputkan dengan password yang berada di database.
  if (password_verify($password_lama, $data['password'])) {

    //jika password baru sama dengan konfirmasi password
    if ($password_baru === $password_konfirmasi) {
      // maka password diubah menjadi kode acak 
      $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
      // lalu mengganti password lama dengan password baru di database.
      $update_password = mysqli_query($conn, "UPDATE user SET password = '$password_hash' WHERE id_user = '$id_user'");
      
      // jika update password gagal
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
