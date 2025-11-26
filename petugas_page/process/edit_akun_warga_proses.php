<?php
session_start();
include "../../koneksi_database.php";

$id_user    = $_POST['id_user'];
$nama       = $_POST['nama'];
$no_telp    = $_POST['no_telp'];
$blok_rumah = $_POST['blok_rumah'];
$no_kk      = $_POST['no_kk'];

/* Upload Foto */
$fotoQuery = "";
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto = addslashes(file_get_contents($foto_tmp));
    $fotoQuery = ", foto='$foto'";
}

/* Update tabel user */
$updateUser = mysqli_query($conn, "
    UPDATE user SET
        nama='$nama',
        no_telp='$no_telp'
        $fotoQuery
    WHERE id_user='$id_user'
");

if (!$updateUser) {
    die("Error update user: " . mysqli_error($conn));
}

/* update tabel warga */
$updateWarga = mysqli_query($conn, "
    UPDATE warga SET
        no_kk='$no_kk',
        blok_rumah='$blok_rumah'
    WHERE id_user='$id_user'
");

if (!$updateWarga) {
    $_SESSION['alert'] = [
    'type' => 'error',
    'title' => 'Gagal!',
    'message' => 'Data warga gagal diperbaharui!'
];

header("Location: ../app/daftar_akun_warga.php");
exit;
}

$_SESSION['alert'] = [
    'type' => 'success',
    'title' => 'Berhasil!',
    'message' => 'Data warga berhasil diperbaharui!'
];

header("Location: ../app/daftar_akun_warga.php");
exit;
?>
