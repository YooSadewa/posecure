<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_POST['id_user'])) {
     $_SESSION['alert'] = [
        'type' => 'error',
        'title' => 'Gagal!',
        'message' => 'ID user tidak ditemukan!'
    ];
    header("Location: ../app/daftar_akun_warga.php");
    exit;
}

$id_user = $_POST['id_user'];

mysqli_query($conn, "DELETE FROM absensi WHERE id_user = '$id_user'");
mysqli_query($conn, "DELETE FROM insiden_keamanan WHERE id_user = '$id_user'");
mysqli_query($conn, "DELETE FROM warga WHERE id_user = '$id_user'");
mysqli_query($conn, "DELETE FROM user WHERE id_user = '$id_user'");

$_SESSION['alert'] = [
    'type' => 'success', 
    'title' => 'Berhasil!',
    'message' => 'Data akun warga berhasil dihapus!'
];

header("Location: ../app/daftar_akun_warga.php");
exit;

