<?php
session_start();
include '../../koneksi_database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
  $kelurahan = mysqli_real_escape_string($conn, $_POST['kelurahan']);
  $rt = mysqli_real_escape_string($conn, $_POST['rt']);
  $rw = mysqli_real_escape_string($conn, $_POST['rw']);

  $update_user = mysqli_query($conn, "UPDATE user SET username = '$username' WHERE id_user = '$id_user'");

  if (!$update_user) {
    die("Gagal Update User: " . mysqli_error($conn));
  }

  $data_alamat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_alamat FROM petugas_keamanan WHERE id_user = '$id_user'"));

  if ($data_alamat) {
    $id_alamat = $data_alamat['id_alamat'];

    $query_alamat = "UPDATE alamat SET kecamatan = '$kecamatan', kelurahan = '$kelurahan', no_rt = '$rt', no_rw = '$rw' WHERE id_alamat = '$id_alamat'";

    $update_alamat = mysqli_query($conn, $query_alamat);

    if (!$update_alamat) {
      die("Gagal Update Alamat: " . mysqli_error($conn));
    }
  }
  echo "
<script>
    alert('Data petugas keamanan berhasil diperbarui.');
    window.location.href = '../app/dashboard_page.php';
</script>
";
} else {
  header("Location: ../app/dashboard_page.php");
  
}
