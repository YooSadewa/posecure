<?php
session_start();
include '../../koneksi_database.php';

// cek hak ases jika user tidak login atau user bukan admin maka tidak boleh menghapus data.
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

// untuk mencegah akses langsung lewat URL jika tidak lewat form POST maka script di dalamnya tidak akan dijalankan.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // mengambil id user dari input form.
  $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
  
  // mencari alamat petugas.
  // mysqli_fetch_assoc = untuk mengambil data dalam bentuk array.
  $cek_alamat = mysqli_query($conn, "SELECT id_alamat FROM petugas_keamanan WHERE id_user = '$id_user'");
  $data = mysqli_fetch_assoc($cek_alamat);

  // untuk mengecek apakah data petugas ada.
  if ($data) {
    $id_alamat = $data['id_alamat'];

    // untuk menghitung berapa petugas yang berada di alamat yang sama.
    // hasilnya disimpan di $jumlah_petugas
    $cek_jumlah_petugas = mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM petugas_keamanan WHERE id_alamat = '$id_alamat'");
    $jumlah_petugas = mysqli_fetch_assoc($cek_jumlah_petugas)['jumlah'];

    // menghapus data petugas dari tabel petugas_keamanan.
    $del_petugas = mysqli_query($conn, "DELETE FROM petugas_keamanan WHERE id_user = '$id_user'");

    // menghapus data petugas dari tabel user.
    $del_user_petugas = mysqli_query($conn, "DELETE FROM user WHERE id_user = '$id_user'");

    // jika penghapusan data petugas dari tabel petugas keamanan dan tabel user berhasil.
    if ($del_petugas && $del_user_petugas) {

      // jika jumlah petugasnya hanya 1 maka alamat dan warga nya ikut terhapus.
      if ($jumlah_petugas == 1) {

        // mengambil id_user dari tabel warga di alamat tersebut.
        $get_warga = mysqli_query($conn, "SELECT id_user FROM warga WHERE id_alamat = '$id_alamat'");
        $warga_ids = [];
        while ($warga = mysqli_fetch_assoc($get_warga)) {
          $warga_ids[] = $warga['id_user'];
        }

         // menghapus seluruh data warga.
        $del_warga = mysqli_query($conn, "DELETE FROM warga WHERE id_alamat = '$id_alamat'");
        // menghapus data alamat
        $del_alamat = mysqli_query($conn, "DELETE FROM alamat WHERE id_alamat = '$id_alamat'");

        $del_user_warga = true;
        
        // menghapus akun login warga satu per satu karena user terpisah dengan tabel warga.
        foreach ($warga_ids as $warga_id) {
          $result = mysqli_query($conn, "DELETE FROM user WHERE id_user = '$warga_id'");
          if (!$result) {
            $del_user_warga = false;
          }
        }

        if ($del_warga && $del_alamat && $del_user_warga) {
          $jumlah_warga = count($warga_ids);
          $_SESSION['alert'] = [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => "Data petugas keamanan, alamat, dan $jumlah_warga warga berhasil dihapus!"
          ];
        } else {
          $_SESSION['alert'] = [
            'icon' => 'warning',
            'title' => 'Peringatan!',
            'text' => 'Petugas berhasil dihapus, tetapi ada masalah saat menghapus data terkait: ' . mysqli_error($conn)
          ];
        }
      } else {
        $_SESSION['alert'] = [
          'icon' => 'success',
          'title' => 'Berhasil!',
          'text' => 'Data petugas keamanan berhasil dihapus. Alamat dan warga tidak dihapus karena masih ada ' . ($jumlah_petugas - 1) . ' petugas lain di alamat ini.'
        ];
      }
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
