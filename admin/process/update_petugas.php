<?php
session_start();
include '../../koneksi_database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_user   = mysqli_real_escape_string($conn, $_POST['id_user']);
  $username  = mysqli_real_escape_string($conn, $_POST['username']);
  $nama      = mysqli_real_escape_string($conn, $_POST['nama']);
  $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
  $kelurahan = mysqli_real_escape_string($conn, $_POST['kelurahan']);
  $rt        = mysqli_real_escape_string($conn, $_POST['rt']);
  $rw        = mysqli_real_escape_string($conn, $_POST['rw']);
  $status_keaktifan = mysqli_real_escape_string($conn, $_POST['status_keaktifan']);

  // untuk memperbaharui data nama pada tabel user.
  $update_user = mysqli_query($conn, "UPDATE user SET nama = '$nama', username = '$username' WHERE id_user = '$id_user'");

  // jika update tidak berhasil maka menyimpan pesan eror ke session dan redirect ke halaman dashboard.
  if (!$update_user) {
    $_SESSION['alert'] = [
      'icon' => 'error',
      'title' => 'Gagal!',
      'text' => 'Gagal mengupdate data petugas: ' . mysqli_error($conn)
    ];
    header("Location: ../app/dashboard_page.php");
    exit;
  }

  // Update status keaktifan di tabel petugas_keamanan
  $update_status = mysqli_query($conn, "UPDATE petugas_keamanan SET status_keaktifan = '$status_keaktifan' WHERE id_user = '$id_user'");

  if (!$update_status) {
    $_SESSION['alert'] = [
      'icon' => 'error',
      'title' => 'Gagal!',
      'text' => 'Gagal mengupdate status keaktifan: ' . mysqli_error($conn)
    ];
    header("Location: ../app/dashboard_page.php");
    exit;
  }

  // untuk mengambil alamat lama petugas untuk pindah alamat 
  $data_alamat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_alamat FROM petugas_keamanan WHERE id_user = '$id_user'"));
  if ($data_alamat) {
    $id_alamat_lama = $data_alamat['id_alamat'];
    $id_alamat_baru = "";

    // mengecek apakah alamat baru sudah ada untuk mencegah duplikat.
    $cek_exist = mysqli_query($conn, "SELECT id_alamat FROM alamat WHERE kecamatan = '$kecamatan' AND kelurahan = '$kelurahan' AND no_rt = '$rt' AND no_rw = '$rw'");

    if (mysqli_num_rows($cek_exist) > 0) {
      $d_exist = mysqli_fetch_assoc($cek_exist);
      $id_alamat_baru = $d_exist['id_alamat'];
    } else {
      $q_last = mysqli_query($conn, "SELECT id_alamat FROM alamat WHERE id_alamat LIKE 'A-%' ORDER BY CAST(SUBSTRING(id_alamat, 3) AS UNSIGNED) DESC LIMIT 1");
      $d_last = mysqli_fetch_assoc($q_last);
      $angka  = $d_last ? (int)substr($d_last['id_alamat'], 2) : 0;

      // jika alamat belum ada maka membuat alamat baru.
      $id_alamat_baru = 'A-' . str_pad($angka + 1, 2, '0', STR_PAD_LEFT);
      
      // menyimpan alamat baru ke database pada tabel alamat.
      $ins_alamat = mysqli_query($conn, "INSERT INTO alamat (id_alamat, kecamatan, kelurahan, no_rt, no_rw) VALUES ('$id_alamat_baru', '$kecamatan', '$kelurahan', '$rt', '$rw')");

      if (!$ins_alamat) {
        $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Gagal buat alamat baru'];
        header("Location: ../app/dashboard_page.php");
        exit;
      }
    }

    // Mengubah relasi alamat petugas keamanan dengan mengganti id_alamat lama menjadi id_alamat baru pada tabel petugas_keamanan berdasarkan id_user.
    $pindah_alamat = mysqli_query($conn, "UPDATE petugas_keamanan SET id_alamat = '$id_alamat_baru' WHERE id_user = '$id_user'");

    // jika gagal update pindah alamat maka muncul pesan itu.
    if (!$pindah_alamat) {
      $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Gagal', 'text' => 'Gagal update relasi alamat'];
      header("Location: ../app/dashboard_page.php");
      exit;
    }

    // jika alamat lama tidak sama dengan alamat baru maka cek total petugas keamanan dan total warga.
    if ($id_alamat_lama !== $id_alamat_baru) {
      $cek_sisa_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT count(*) as total FROM petugas_keamanan WHERE id_alamat = '$id_alamat_lama'"));
      $cek_sisa_w = mysqli_fetch_assoc(mysqli_query($conn, "SELECT count(*) as total FROM warga WHERE id_alamat = '$id_alamat_lama'"));

      $total_penghuni = $cek_sisa_p['total'] + $cek_sisa_w['total'];
      
      // untuk mengecek tidak ada petugas keamanan dan tidak ada warga.
      // jika total penghuni nya 0 maka halamatnya dihapus.
      if ($total_penghuni == 0) {
        mysqli_query($conn, "DELETE FROM alamat WHERE id_alamat = '$id_alamat_lama'");
      }
    }
  }

  $_SESSION['alert'] = [
    'icon' => 'success',
    'title' => 'Berhasil!',
    'text' => 'Data petugas keamanan berhasil diperbarui.'
  ];
  header("Location: ../app/dashboard_page.php");
  exit;
} else {
  header("Location: ../app/dashboard_page.php");
}