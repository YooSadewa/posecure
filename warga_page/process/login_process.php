<?php
include '../../koneksi_database.php';
$username = $_POST['username'];
$password = $_POST['password'];

$user = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username' AND role = 'warga'");
if ($user->num_rows > 0) {
    $data = mysqli_fetch_assoc($user);
    if (password_verify($password, $data['password'])) {
        $id_user = $data['id_user'];
        $data_query = mysqli_query($conn, "SELECT * FROM warga WHERE id_user = '$id_user'");
        $data_warga = mysqli_fetch_assoc($data_query);
        session_start();
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['no_telp'] = $data['no_telp'];
        $_SESSION['hari_ronda'] = $data_warga['hari_ronda'];
        $_SESSION['foto'] = $data['foto'];
        $_SESSION['id_alamat'] = $data_warga['id_alamat'];
        echo "<script>
                alert('Login Berhasil');
                window.location.href = '../app/dashboard_page.php';
            </script>";
    } else {
        echo "<script>
                alert('Username atau Password salah');
                window.location.href = '../app/login_page.php';
            </script>";
        exit;
    }
} else {
    echo "<script>
        alert('Username atau Password salah');
        window.location.href = '../app/login_page.php';
      </script>";
    exit;
}
