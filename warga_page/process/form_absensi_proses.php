<?php
session_start();
include "../../koneksi_database.php";

// Validasi login
if (!isset($_SESSION['id_user'])) {
    echo "<script>
            alert('Silakan login terlebih dahulu!');
            window.location.href='../app/login_page.php';
          </script>";
    exit;
}

if (isset($_POST['foto_data'])) {
    $id_user = $_SESSION['id_user'];
    $tanggal = $_POST['tanggal'];
    $fotoData = $_POST['foto_data'];

    // Validasi foto
    if (empty($fotoData)) {
        echo "<script>
                alert('Foto absensi wajib diambil!');
                window.location.href='../app/form_absensi.php';
              </script>";
        exit;
    }

    // Cek sudah absen hari ini
    $cek = mysqli_query($conn, "SELECT * FROM absensi WHERE id_user='$id_user' AND tanggal='$tanggal'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>
                alert('Anda sudah melakukan absensi hari ini!');
                window.location.href='../app/dashboard_page.php';
              </script>";
        exit;
    }

    // Generate ID absensi
    $randomNum = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
    $id_absensi = 'ABS' . $randomNum;

    // Proses foto base64 -> simpan sebagai file
    $fotoData = preg_replace('/^data:image\/\w+;base64,/', '', $fotoData);
    $fotoData = str_replace(' ', '+', $fotoData);
    $fotoBinary = base64_decode($fotoData);

    // Nama file foto (ini yang disimpan ke database VARCHAR)
    $namaFoto = $id_absensi . '_' . time() . '.png';
    $folderPath = '../../assets/warga_img/absensi/';

    // Buat folder jika belum ada
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }

    $filePath = $folderPath . $namaFoto;

    // Simpan file foto ke server
    if (file_put_contents($filePath, $fotoBinary)) {
        // Insert NAMA FILE (VARCHAR) ke database, bukan binary
        $query = mysqli_query($conn, "INSERT INTO absensi (id_absensi, tanggal, foto_absensi, id_user) 
                                       VALUES ('$id_absensi', '$tanggal', '$namaFoto', '$id_user')");

        if ($query) {
            echo "<script>
                    alert('Absensi berhasil disimpan!');
                    window.location.href='../app/dashboard_page.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menyimpan absensi ke database!');
                    window.location.href='../app/form_absensi.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Gagal menyimpan foto!');
                window.location.href='../app/form_absensi.php';
              </script>";
    }
}

$conn->close();
