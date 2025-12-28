<?php

// fungsinya untuk mengaktifkan session PHP agar kita bisa menggunakan $_SESSION 
// SESSION adalah cara PHP untuk menyimpan data sementara di server selama user masih menggunakan website tersebut.
// $_SESSION adalah variabel global PHP untuk menyimpan data user.
session_start();

// fungsinya untuk memanggil file koneksi_database agar bisa menjalankan query.
include "../../koneksi_database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Menyimpan input data warga yang diambil dari form 
    $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_telp    = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $blok_rumah = mysqli_real_escape_string($conn, $_POST['blok_rumah']);
    $no_kk      = mysqli_real_escape_string($conn, $_POST['no_kk']);
    $username   = $blok_rumah;
    $password   = password_hash($blok_rumah, PASSWORD_DEFAULT);
    $id_alamat = $_SESSION['id_alamat']; // fungsinya agar setiap data yang dikelola petugas keamanan terkait pada wilayah tempat si petugas keamanan tersbeut bekerja. 

    /* Upload Foto - WAJIB */
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== 0) {
        echo "<script>
            alert('Foto wajib diupload!');
            window.history.back();
          </script>";
        exit;
    }

    $nama_file   = $_FILES['foto']['name'];
    $ukuran_file = $_FILES['foto']['size'];
    $tmp_name    = $_FILES['foto']['tmp_name'];

    $ekstensi_valid = ['jpg', 'jpeg', 'png'];
    $ekstensi_file  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    if (!in_array($ekstensi_file, $ekstensi_valid)) {
        echo "<script>
            alert('Format foto harus JPG, JPEG, atau PNG!');
            window.history.back();
          </script>";
        exit;
    }

    if ($ukuran_file > 2000000) {
        echo "<script>
            alert('Ukuran foto maksimal 2MB!');
            window.history.back();
          </script>";
        exit;
    }

    $folder   = "../../assets/warga_img/profile_img/";
    $filename = "warga_" . time() . "_" . rand(100, 999) . "." . $ekstensi_file;
    $fullpath = $folder . $filename;

    if (!move_uploaded_file($tmp_name, $fullpath)) {
        echo "<script>
            alert('Upload foto gagal!');
            window.history.back();
          </cript>";
        exit;
    }

    // inilah nama foto yang siap disimpan ke database
    $foto = $filename;


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

    $blok_rumah = $_POST['blok_rumah'];

    $cekBlok = mysqli_query($conn, "
    SELECT blok_rumah FROM warga WHERE blok_rumah = '$blok_rumah'
");

    $cekNik = mysqli_query($conn, "
    SELECT no_kk FROM warga WHERE no_kk = '$no_kk'
");

    $cekNoTelp = mysqli_query($conn, "
    SELECT no_telp FROM user WHERE no_telp = '$no_telp'
");

    if (mysqli_num_rows($cekBlok) > 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Duplikat Blok!',
            'message' => 'Blok rumah sudah ada. Tidak dapat menambahkan data yang sama.'
        ];
        header("Location: ../app/daftar_akun_warga.php");
        exit;
    }

    if (mysqli_num_rows($cekNik) > 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Duplikat No KK!',
            'message' => 'Nomor KK sudah ada. Tidak dapat menambahkan data yang sama.'
        ];
        header("Location: ../app/daftar_akun_warga.php");
        exit;
    }

    if (mysqli_num_rows($cekNoTelp) > 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Duplikat No Telp!',
            'message' => 'Nomor telepon sudah ada. Tidak dapat menambahkan data yang sama.'
        ];
        header("Location: ../app/daftar_akun_warga.php");
        exit;
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
}
