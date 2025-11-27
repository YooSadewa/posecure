<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='../login.php';</script>";
    exit;
}

$id_user       = $_SESSION['id_user']; 
$tanggal       = $_POST['tanggal'];
$jam           = $_POST['jam'];
$jenis_insiden = $_POST['jenis_insiden'];
$keterangan    = $_POST['keterangan'];

$getID = mysqli_query($conn, "
    SELECT id_insiden 
    FROM insiden_keamanan
    ORDER BY CAST(SUBSTRING(id_insiden, 3) AS UNSIGNED) DESC
    LIMIT 1
");

$data = mysqli_fetch_assoc($getID);

if (!$data) {
    $id_insiden = "I-01";
} else {
    $lastID = $data['id_insiden'];
    $num = (int) substr($lastID, 2) + 1;
    $id_insiden = "I-" . sprintf("%02d", $num);
}

$namaFoto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $foto_tmp  = $_FILES['foto']['tmp_name'];
    $foto_ext  = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

    $namaFoto  = $id_insiden . '_' . time() . '.' . $foto_ext;
    $folderPath = '../../assets/warga_img/insiden_keamanan/';

    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    $filePath = $folderPath . $namaFoto;

    if (!move_uploaded_file($foto_tmp, $filePath)) {
        echo "<script>
                alert('Gagal mengupload foto!');
                window.location.href='../app/form_laporan_insiden.php';
              </script>";
        exit;
    }
}

$queryInsiden = mysqli_query($conn, "
    INSERT INTO insiden_keamanan (id_insiden, id_user, tanggal, jam, jenis_insiden, foto, keterangan)
    VALUES ('$id_insiden', '$id_user', '$tanggal', '$jam', '$jenis_insiden', '$namaFoto', '$keterangan')
");

// ⭐ PERBAIKAN DI SINI
if ($queryInsiden) {  // ✅ JIKA BERHASIL (tanpa tanda !)
    $_SESSION['alert'] = [
        'type' => 'success',
        'title' => 'Berhasil!',
        'message' => 'Laporan insiden berhasil ditambahkan!'
    ];

    header("Location: ../app/laporan_insiden_page.php");
    exit;
}

// Jika GAGAL
$_SESSION['alert'] = [
    'type' => 'error',
    'title' => 'Gagal!',
    'message' => 'Laporan insiden gagal ditambahkan! ' . mysqli_error($conn)
];

header("Location: ../app/laporan_insiden_page.php");
exit;
?>