<?php  
session_start();
include "../../koneksi_database.php";

$nama       = $_POST['nama'];
$no_telp    = $_POST['no_telp'];
$blok_rumah = $_POST['blok_rumah'];
$no_kk      = $_POST['no_kk'];
$username   = $blok_rumah;
$password   = password_hash($blok_rumah, PASSWORD_DEFAULT);
$id_alamat = $_SESSION['id_alamat'];

/* Upload Foto */
$foto = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $folder = "../../assets/warga_img/profile_img/";
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('profile_') . '.' . $ext;
    $fullpath = $folder . $filename;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $fullpath)) {
        $foto = $filename;
    
    } else {
        $foto = null; 
    }
} else {
    $foto = null;
}

/* Membuat Id User */
$getID = mysqli_query($conn, "
    SELECT id_user 
    FROM user 
    ORDER BY CAST(SUBSTRING(id_user, 3) AS UNSIGNED) DESC
    LIMIT 1
");

$data  = mysqli_fetch_assoc($getID);

if (!$data) {
    $id_user = "W-01"; 
} else {
    $lastID = $data['id_user']; 
    $num = (int) substr($lastID, 2); 
    $num++;
    $id_user = "W-" . sprintf("%02d", $num); 
}


/* Insert User */
$queryUser = mysqli_query($conn, "
    INSERT INTO user (id_user, nama, username, password, role, foto, no_telp)
    VALUES ('$id_user', '$nama', '$username', '$password', 'warga', '$foto', '$no_telp')
");

if (!$queryUser) {
    die("Error user: " . mysqli_error($conn));
}


/* Insert Warga */
$queryWarga = mysqli_query($conn, "
    INSERT INTO warga (id_user, no_kk, blok_rumah, id_alamat)
    VALUES ('$id_user', '$no_kk', '$blok_rumah', '$id_alamat')
");

if (!$queryWarga) {
    $_SESSION['alert'] = [
    'type' => 'error',
    'title' => 'Gagal!',
    'message' => 'Data warga gagal ditambahkan!'
];

header("Location: ../app/daftar_akun_warga.php");
exit;
}

$_SESSION['alert'] = [
    'type' => 'success',
    'title' => 'Berhasil!',
    'message' => 'Data warga berhasil ditambahkan!'
];

header("Location: ../app/daftar_akun_warga.php");
exit;

?>