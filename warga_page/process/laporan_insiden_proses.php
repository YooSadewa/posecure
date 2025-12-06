<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='../login.php';</script>";
    exit;
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['alert'] = [
        'type' => 'error',
        'title' => 'Gagal!',
        'message' => 'Invalid CSRF token!'
    ];
    header("Location: ../app/form_laporan_insiden.php");
    exit;
}
unset($_SESSION['csrf_token']);

$id_user       = $_SESSION['id_user'];
$tanggal       = $_POST['tanggal'];
$jam           = $_POST['jam'];
$jenis_insiden = $_POST['jenis_insiden'];
$keterangan    = $_POST['keterangan'];

$tanggal       = mysqli_real_escape_string($conn, $tanggal);
$jam           = mysqli_real_escape_string($conn, $jam);
$jenis_insiden = htmlspecialchars(mysqli_real_escape_string($conn, $jenis_insiden), ENT_QUOTES, 'UTF-8');
$keterangan    = htmlspecialchars(mysqli_real_escape_string($conn, trim($keterangan)), ENT_QUOTES, 'UTF-8');

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
    if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Ukuran foto terlalu besar! Maksimal 5MB.'
        ];
        header("Location: ../app/form_laporan_insiden.php");
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['foto']['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mimeType, $allowedTypes)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Format foto tidak valid! Hanya JPG, JPEG, dan PNG.'
        ];
        header("Location: ../app/form_laporan_insiden.php");
        exit;
    }

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

// Jika Berhasil
if ($queryInsiden) {
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
