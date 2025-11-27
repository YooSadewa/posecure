<?php
session_start();
include "../../koneksi_database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = $_POST['nama'];
    $hari = $_POST['hari'];

    if (empty($nama) || empty($hari)) {
        echo "gagal";
        exit;
    }

    // Insert ke database
    $query = "INSERT INTO jadwal_ronda (nama, hari) VALUES ('$nama', '$hari')";
    $result = mysqli_query($conn, $query);

}
?>
