<?php
session_start();
include '../../koneksi_database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_user = $_SESSION['id_user'];

  // Error 0 artinya tidak ada error (file ada), Error 4 artinya file kosong.
  if (isset($_FILES['foto_admin']) && $_FILES['foto_admin']['error'] === 0) {

    $nama_file   = $_FILES['foto_admin']['name'];
    $ukuran_file = $_FILES['foto_admin']['size'];
    $tmp_name    = $_FILES['foto_admin']['tmp_name'];

    $ekstensi_valid = ['jpg', 'jpeg', 'png'];
    $ekstensi_file  = explode('.', $nama_file);
    $ekstensi_file  = strtolower(end($ekstensi_file));

    if (!in_array($ekstensi_file, $ekstensi_valid)) {
      echo "<script>
                    alert('Format file tidak valid! Hanya JPG, JPEG, atau PNG.'); 
                    window.location.href='../app/dashboard_page.php';
                  </script>";
      exit;
    }

    if ($ukuran_file > 2000000) {
      echo "<script>
                    alert('Ukuran file terlalu besar (Max 2MB)!'); 
                    window.location.href='../app/dashboard_page.php';
                  </script>";
      exit;
    }

    // Generate Nama File Baru, Format: admin_IDUSER_TIMESTAMP.ekstensi_filenya

    $nama_baru = "admin_" . $id_user . "_" . time() . "." . $ekstensi_file;
    $tujuan    = "../../assets/admin_img/profile_img/" . $nama_baru;

    // Upload File
    if (move_uploaded_file($tmp_name, $tujuan)) {

      // Hapus foto lama dari folder jika ada
      $query_lama = mysqli_query($conn, "SELECT foto FROM user WHERE id_user = '$id_user'");
      $data_lama = mysqli_fetch_assoc($query_lama);
      if ($data_lama['foto'] != '' && file_exists("../../assets/admin_img/profile_img/" . $data_lama['foto'])) {
        unlink("../../assets/admin_img/profile_img/" . $data_lama['foto']);
      }

      $query = "UPDATE user SET foto = '$nama_baru' WHERE id_user = '$id_user'";

      if (mysqli_query($conn, $query)) {
        $_SESSION['foto'] = $nama_baru;

        echo "<script>
                        alert('Foto profil berhasil diperbarui!'); 
                        window.location.href='../app/dashboard_page.php';
                      </script>";
      } else {
        die("Gagal Update Database: " . mysqli_error($conn));
      }
    } else {
      echo "<script>
                    alert('Gagal mengupload gambar ke server!'); 
                    window.location.href='../app/dashboard_page.php';
                  </script>";
    }
  } else {
    echo "<script>
                alert('Silakan pilih foto terlebih dahulu.'); 
                window.location.href='../app/dashboard_page.php';
              </script>";
  }
} else {
  header("Location: ../app/dashboard_page.php");
}
