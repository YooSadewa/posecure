<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_POST['id_user'])) {
    echo "<script>
            alert('ID User tidak ditemukan!');
            window.location.href = '../app/daftar_akun_warga.php';
          </script>";
    exit;
}

$id_user = $_POST['id_user'];

mysqli_query($conn, "DELETE FROM warga WHERE id_user = '$id_user'");

mysqli_query($conn, "DELETE FROM user WHERE id_user = '$id_user'");

echo "<script>
        alert('Data akun warga berhasil dihapus!');
        window.location.href = '../app/daftar_akun_warga.php';
      </script>";
?>
