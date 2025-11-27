<?php
session_start();
include '../../koneksi_database.php';

if (isset($_POST['submit'])) {
    $id_user = $_SESSION['id_user'];
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $password_konfirmasi = $_POST['password_konfirmasi'];

    // Ambil password dari database
    $query = mysqli_query($conn, "SELECT password FROM user WHERE id_user = '$id_user'");
    $data = mysqli_fetch_assoc($query);

    // Cek password lama
    if (!password_verify($password_lama, $data['password'])) {
        echo "<script>
            alert('Password lama salah');
            window.location.href='../app/dashboard_page.php?modal=gantiPassword';
        </script>";
        exit;
    }

    // Cek konfirmasi password
    if ($password_baru != $password_konfirmasi) {
        echo "<script>
            alert('Konfirmasi password tidak sesuai');
            window.location.href='../app/dashboard_page.php?modal=gantiPassword';
        </script>";
        exit;
    }

    // Update password
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE user SET password = '$password_hash' WHERE id_user = '$id_user'");

    echo "<script>
        alert('Password berhasil diubah');
        window.location.href='../app/dashboard_page.php?modal=profile';
    </script>";
} else {
    header("location: ../app/dashboard_page.php");
}
