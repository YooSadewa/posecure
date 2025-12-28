<?php
session_start();
include '../../koneksi_database.php';

// Kalau belum login ATAU rolenya bukan admin → tendang ke halaman login.
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

// Kode di bawah hanya boleh dijalankan jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_user = $_SESSION['id_user'];

  // jika input file ada dan tidak ada eror upload 
  // Error 0 artinya tidak ada error (file ada), Error 4 artinya file kosong.
  if (isset($_FILES['foto_admin']) && $_FILES['foto_admin']['error'] === 0) {

    // untuk mengambil nama file, ukuran file dan lokasi file sementara.
    $nama_file   = $_FILES['foto_admin']['name'];
    $ukuran_file = $_FILES['foto_admin']['size'];
    $tmp_name    = $_FILES['foto_admin']['tmp_name'];

    // untuk mengambil ekstenso file lalu ubah ke string huruf kecil.
    $ekstensi_valid = ['jpg', 'jpeg', 'png'];
    $ekstensi_file  = explode('.', $nama_file);
    $ekstensi_file  = strtolower(end($ekstensi_file));

    // jika bukan file jpg/jpeg/png -> tolak upload file.
    if (!in_array($ekstensi_file, $ekstensi_valid)) {
      $_SESSION['alert'] = [
        'icon' => 'error',
        'title' => 'Gagal!',
        'text' => 'Format file tidak valid! Hanya JPG, JPEG, atau PNG.'
      ];
      header("Location: ../app/dashboard_page.php");
      exit;
    }

    // Jika ukuran file lebih dari 2 mb -> maka upload file ditolak.
    if ($ukuran_file > 2000000) {
      $_SESSION['alert'] = [
        'icon' => 'error',
        'title' => 'Gagal!',
        'text' => 'Ukuran file terlalu besar (Max 2MB)!'
      ];
      header("Location: ../app/dashboard_page.php");
      exit;
    }

    // Generate Nama File Baru, Format: admin_IDUSER_TIMESTAMP.ekstensi_filenya
    // membuat file baru agar unik untuk mencegah nama file bentrok.
    $nama_baru = "admin_" . $id_user . "_" . time() . "." . $ekstensi_file;
    // file akan disimpan di folder profile_img.
    $tujuan    = "../../assets/admin_img/profile_img/" . $nama_baru;

    // Upload File ke server
    // memindahkan file dari folder sementara ke folder tujuan.
    if (move_uploaded_file($tmp_name, $tujuan)) {

      // Hapus foto lama dari folder jika ada
      $query_lama = mysqli_query($conn, "SELECT foto FROM user WHERE id_user = '$id_user'");
      $data_lama = mysqli_fetch_assoc($query_lama);

      // mengambil nama foto dari database.
      // jika foto lama ada di folder maka dihapus supaya tidak menumpuk.
      if ($data_lama['foto'] != '' && file_exists("../../assets/admin_img/profile_img/" . $data_lama['foto'])) {
        unlink("../../assets/admin_img/profile_img/" . $data_lama['foto']);
      }

      // mengubah nama foto di database.
      $query = "UPDATE user SET foto = '$nama_baru' WHERE id_user = '$id_user'";

      if (mysqli_query($conn, $query)) {
        $_SESSION['foto'] = $nama_baru;
        $_SESSION['alert'] = [
          'icon' => 'success',
          'title' => 'Berhasil!',
          'text' => 'Foto profil berhasil diperbarui!.',
          'timer' => 1500
        ];
        header("Location: ../app/dashboard_page.php");
        exit;
      } else {
        $_SESSION['alert'] = [
          'icon' => 'error',
          'title' => 'Gagal!',
          'text' => 'Gagal mengupdate data profil: ' . mysqli_error($conn)
        ];
        header("Location: ../app/dashboard_page.php");
        exit;
      }
    } else {
      $_SESSION['alert'] = [
        'icon' => 'error',
        'title' => 'Gagal!',
        'text' => 'Gagal mengupload gambar ke server!'
      ];
      header("Location: ../app/dashboard_page.php");
      exit;
    }
  } else {
    $_SESSION['alert'] = [
      'icon' => 'error',
      'title' => 'Gagal!',
      'text' => 'Silakan pilih foto terlebih dahulu.'
    ];
    header("Location: ../app/dashboard_page.php");
    exit;
  }
} else {
  header("Location: ../app/dashboard_page.php");
}
