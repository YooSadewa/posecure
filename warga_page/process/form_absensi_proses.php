<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='../login.php';</script>";
    exit;
}

if (isset($_POST['foto_data'])) {
    $id_user = $_SESSION['id_user'];
    $tanggal = $_POST['tanggal'];
    $fotoData = $_POST['foto_data'];

    // Validasi foto
   if (empty($fotoData)) {
    $_SESSION['alert'] = [
        'icon' => 'warning',
        'title' => 'Foto Tidak Ada',
        'text' => 'Foto absensi wajib diambil!'
    ];
    header("Location: ../app/form_absensi.php");
    exit;
    }

    // Cek sudah absen hari ini
    $stmt_cek = $conn->prepare("SELECT id_absensi FROM absensi WHERE id_user = ? AND tanggal = ?");

    if (!$stmt_cek) {
        $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Error Database',
            'text' => 'Terjadi kesalahan sistem!'
        ];
        header("Location: ../app/form_absensi.php");
        exit;
    }

    $stmt_cek->bind_param("ss", $id_user, $tanggal);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();

    if ($result_cek->num_rows > 0) {
        $_SESSION['alert'] = [
            'icon' => 'info',
            'title' => 'Sudah Absen',
            'text' => 'Anda sudah melakukan absensi hari ini!'
        ];
        $stmt_cek->close();
        $conn->close();
        header("Location: ../app/dashboard_page.php");
        exit;
    }
    $stmt_cek->close();

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
        $stmt = $conn->prepare("INSERT INTO absensi (id_absensi, tanggal, foto_absensi, id_user) VALUES (?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("ssss", $id_absensi, $tanggal, $namaFoto, $id_user);
            $hasil = $stmt->execute();  

            if ($hasil) {  
                $_SESSION['alert'] = [
                    'icon' => 'success',  
                    'title' => 'Berhasil!',
                    'text' => 'Absensi berhasil disimpan!'
                ];
                $stmt->close();
                $conn->close();
                header("Location: ../app/dashboard_page.php");
                exit;
            } else {  

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                
                $_SESSION['alert'] = [
                    'icon' => 'error', 
                    'title' => 'Gagal Menyimpan',
                    'text' => 'Gagal menyimpan data ke database!'
                ];
                $stmt->close();
                $conn->close();
                header("Location: ../app/form_absensi.php");
                exit;
            }
        } else {

            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $_SESSION['alert'] = [
                'icon' => 'error',
                'title' => 'Error Database',
                'text' => 'Error prepare statement!'
            ];
            $conn->close();
            header("Location: ../app/form_absensi.php");
            exit;
        }
        } else {
        
            $_SESSION['alert'] = [
            'icon' => 'error',
            'title' => 'Gagal Menyimpan Foto',
            'text' => 'Tidak dapat menyimpan file foto ke server!'
            ];
            $conn->close();
            header("Location: ../app/form_absensi.php");
            exit;
        }
    }