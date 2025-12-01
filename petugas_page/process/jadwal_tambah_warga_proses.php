<?php
session_start();
include "../../koneksi_database.php";

if (isset($_POST['submit'])) {

    $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
    $hari_ronda = mysqli_real_escape_string($conn, $_POST['hari_ronda']);
    $id_alamat = mysqli_real_escape_string($conn, $_POST['id_alamat']);

    // Validasi id_user
    if (empty($id_user)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Nama tidak valid! Pilih nama dari daftar.'
        ];
        header("Location: ../app/jadwal_ronda.php?id_alamat=" . urlencode($id_alamat));
        exit;
    }

    // Cek apakah user ada di tabel warga DAN ambil hari_ronda sekaligus
    $cek = mysqli_query($conn, "SELECT hari_ronda FROM warga WHERE id_user = '$id_user'");

    if (mysqli_num_rows($cek) == 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Warga belum terdaftar dalam tabel warga!'
        ];
        header("Location: ../app/jadwal_ronda.php?id_alamat=" . urlencode($id_alamat));
        exit;
    }

    // Ambil hari_ronda yang sekarang
    $row = mysqli_fetch_assoc($cek);
    $hari_sekarang = $row['hari_ronda'];

    // Cek apakah hari baru SAMA dengan hari yang sudah ada
    if ($hari_sekarang !== null && $hari_sekarang !== '' && strcasecmp($hari_sekarang, $hari_ronda) == 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Tidak Bisa Menambahkan!',
            'message' => 'Anda sudah terdaftar di hari ' . $hari_ronda . '. Tidak bisa menambahkan lagi.'
        ];
        header("Location: ../app/jadwal_ronda.php?id_alamat=" . urlencode($id_alamat));
        exit;
    }
    
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
            'message' => 'Jadwal ronda berhasil ditambahkan!'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'message' => 'Terjadi kesalahan: ' . mysqli_error($conn)
        ];
    }

    header("Location: ../app/jadwal_ronda.php?id_alamat=" . urlencode($id_alamat));
    exit;
}
?>