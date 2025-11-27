<?php
session_start();
include "../../koneksi_database.php";

if (isset($_POST['submit'])) {

    $nama = mysqli_real_escape_string($conn, $_POST['id_user']);
    $hari_ronda = mysqli_real_escape_string($conn, $_POST['hari_ronda']);

    // Generate id_user baru otomatis
    $query_last = mysqli_query($conn, "SELECT id_user FROM user WHERE id_user LIKE 'W-%' ORDER BY id_user DESC LIMIT 1");
    if(mysqli_num_rows($query_last) > 0) {
        $last = mysqli_fetch_assoc($query_last);
        $num = intval(substr($last['id_user'], 2)) + 1;
        $id_user = 'W-' . str_pad($num, 2, '0', STR_PAD_LEFT);
    } else {
        $id_user = 'W-01';
    }

    // Insert ke user dulu
    $query_user = mysqli_query($conn, "INSERT INTO user (id_user, nama, role) VALUES ('$id_user', '$nama', 'warga')");

    if($query_user) {
        // Ambil id_alamat pertama dari tabel alamat
        $ambil_alamat = mysqli_query($conn, "SELECT id_alamat FROM alamat LIMIT 1");
        $data_alamat = mysqli_fetch_assoc($ambil_alamat);
        $id_alamat = $data_alamat['id_alamat'];

        // Insert ke warga dengan id_alamat
        $query_warga = mysqli_query($conn, "INSERT INTO warga (id_user, hari_ronda, id_alamat) VALUES ('$id_user', '$hari_ronda', '$id_alamat')");

        if ($query_warga) { 
            $_SESSION['alert'] = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'message' => 'Laporan insiden berhasil ditambahkan!'
            ];
            
            header("Location: ../app/jadwal_ronda.php");
            exit;
        } else {
            // Jika insert warga gagal
            $_SESSION['alert'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'message' => 'Gagal menambahkan data warga: ' . mysqli_error($conn)
            ];
            
            header("Location: ../app/jadwal_ronda.php");
            exit;
        }
    } else {
        // Jika insert user gagal
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Gagal menambahkan user: ' . mysqli_error($conn)
        ];
        
        header("Location: ../app/jadwal_ronda.php");
        exit;
    }
}
?>