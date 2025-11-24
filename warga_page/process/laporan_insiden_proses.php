<?php
session_start();
include "../../koneksi_database.php";

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='../login.php';</script>";
    exit;
}

$id_user       = $_SESSION['id_user']; // ambil dari session
$tanggal       = $_POST['tanggal'];
$jam           = $_POST['jam'];
$jenis_insiden = $_POST['jenis_insiden'];
$keterangan    = $_POST['keterangan'];

// Upload Foto
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $foto     = addslashes(file_get_contents($foto_tmp));
}

// Membuat Id Insiden otomatis
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

// Insert ke database
$queryInsiden = mysqli_query($conn, "
    INSERT INTO insiden_keamanan (id_insiden, id_user, tanggal, jam, jenis_insiden, foto, keterangan)
    VALUES ('$id_insiden', '$id_user', '$tanggal', '$jam', '$jenis_insiden', '$foto', '$keterangan')
");

if (!$queryInsiden) {
    die("Error insert: " . mysqli_error($conn));
}

// Redirect ke halaman laporan
echo "<script>
        alert('Laporan Insiden berhasil disimpan.');
        window.location.href = '../app/laporan_insiden_page.php';
      </script>";
