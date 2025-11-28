<?php
session_start();
include "../../koneksi_database.php";

if (isset($_POST['submit'])) {

    $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
    $hari_ronda = mysqli_real_escape_string($conn, $_POST['hari_ronda']);

    // Validasi id_user
    if (empty($id_user)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Nama tidak valid! Pilih nama dari daftar.'
        ];
        header("Location: ../app/jadwal_ronda.php");
        exit;
    }

    // Cek apakah user ada di tabel warga
    $cek = mysqli_query($conn, "SELECT * FROM warga WHERE id_user = '$id_user'");

    if (mysqli_num_rows($cek) == 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Warga belum terdaftar dalam tabel warga!'
        ];
        header("Location: ../app/jadwal_ronda.php");
        exit;
    }

    // ===== TAMBAHKAN VALIDASI INI ===== 
    // Cek apakah warga sudah memiliki jadwal di hari yang sama
    $cek_duplikat = mysqli_query($conn, "
        SELECT * FROM warga 
        WHERE id_user = '$id_user' 
        AND hari_ronda = '$hari_ronda'
    ");

    if (mysqli_num_rows($cek_duplikat) > 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Warga sudah terjadwal di hari ' . $hari_ronda . '!'
        ];
        header("Location: ../app/jadwal_ronda.php");
        exit;
    }
    // ===== AKHIR VALIDASI =====

    // Proses update jadwal ronda
    $query = mysqli_query($conn, "
        UPDATE warga 
        SET hari_ronda = '$hari_ronda'
        WHERE id_user = '$id_user'
    ");

    if ($query) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'title' => 'Berhasil!',
            'message' => 'Jadwal ronda berhasil diperbarui!'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Terjadi kesalahan: ' . mysqli_error($conn)
        ];
    }

    header("Location: ../app/jadwal_ronda.php");
    exit;
}?>