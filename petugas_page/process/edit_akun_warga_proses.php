<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas_keamanan') {
    header("Location: ../app/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_user    = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_telp    = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $blok_rumah = mysqli_real_escape_string($conn, $_POST['blok_rumah']);
    $no_kk      = mysqli_real_escape_string($conn, $_POST['no_kk']);

   // upload foto
    $fotoQuery = "";

    if (isset($_FILES['foto']) && $_FILES['foto']['name'] !== '') {

        if ($_FILES['foto']['error'] !== 0) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'File foto bermasalah!'
            ];
            header("Location: ../app/daftar_akun_warga.php");
            exit;
        }

        $nama_file   = $_FILES['foto']['name'];
        $ukuran_file = $_FILES['foto']['size'];
        $tmp_name    = $_FILES['foto']['tmp_name'];

        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (!in_array($ekstensi_file, $ekstensi_valid)) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Format foto harus JPG, JPEG, atau PNG!'
            ];
            header("Location: ../app/daftar_akun_warga.php");
            exit;
        }

        if ($ukuran_file > 2000000) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Ukuran foto maksimal 2MB!'
            ];
            header("Location: ../app/daftar_akun_warga.php");
            exit;
        }

        $folder   = "../../assets/warga_img/profile_img/";
        $filename = "warga_" . $id_user . "_" . time() . "." . $ekstensi_file;
        $fullpath = $folder . $filename;

        if (!move_uploaded_file($tmp_name, $fullpath)) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Upload foto gagal!'
            ];
            header("Location: ../app/daftar_akun_warga.php");
            exit;
        }

        // Ambil foto lama
        $query_lama = mysqli_query($conn, "SELECT foto FROM user WHERE id_user='$id_user'");
        $data_lama  = mysqli_fetch_assoc($query_lama);

        if (!empty($data_lama['foto']) && file_exists($folder . $data_lama['foto'])) {
            unlink($folder . $data_lama['foto']);
        }

        // set query update foto
        $fotoQuery = ", foto='$filename'";
    }

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
}
