<?php
session_start();
include '../../koneksi_database.php';

/* Validasi error
 - username tidak dimasukkan
 - password tidak dimasukkan
 - username tidak ditemukan
 - password salah
*/

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
    $_SESSION['login'] = true;
    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = $data['role'];

    if ($data["role"] === "petugas_keamanan") {
      $query_petugas = mysqli_query($conn, "SELECT id_alamat FROM petugas_keamanan WHERE id_user = '" . $data['id_user'] . "'");
      $data_petugas = mysqli_fetch_assoc($query_petugas);
      $_SESSION['id_alamat'] = $data_petugas['id_alamat'];
      header("location:../app/dashboard.php");
    } else if ($data["role"] === "admin") {
      header("location:../../admin/app/dashboard_page.php");
    }
  } else {
    header("location:../app/login_page.php?pesan=password_salah&value_username=" . $_POST['username']);
    exit;
  }
} else {
  header("location:../app/login_page.php?pesan=pengguna_tidak_ditemukan&value_username=" . $_POST['username']);
  exit;
}
