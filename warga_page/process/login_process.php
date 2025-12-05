<?php
include '../../koneksi_database.php';
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

$user = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username' AND role = 'warga'");
if ($user->num_rows > 0) {
    $data = mysqli_fetch_assoc($user);
    if (password_verify($password, $data['password'])) {
        $id_user = $data['id_user'];
        $data_query = mysqli_query($conn, "SELECT * FROM warga WHERE id_user = '$id_user'");
        $data_warga = mysqli_fetch_assoc($data_query);

        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['no_telp'] = $data['no_telp'];
        $_SESSION['hari_ronda'] = $data_warga['hari_ronda'];
        $_SESSION['foto'] = $data['foto'];
        $_SESSION['id_alamat'] = $data_warga['id_alamat'];
        $_SESSION['role'] = $data['role'];

        $_SESSION['alert'] = [
            'icon' => 'success',
            'title' => 'Login Berhasil!',
            'timer' => 1500
        ];

        header("Location: ../app/dashboard_page.php");
        exit;
    } else {
        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Login Gagal!',
            'text' => 'Username atau Password salah'
        ];

        header("Location: ../app/login_page.php");
        exit;
    }
} else {
    $_SESSION['alert'] = [
        'icon' => 'error',
        'title' => 'Login Gagal!',
        'text' => 'Username atau Password salah'
    ];

    header("Location: ../app/login_page.php");
    exit;
}
