<?php
session_start();
include "../../koneksi_database.php";

// Validasi user sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>
            alert('Silakan login terlebih dahulu!');
            window.location.href='../app/login_page.php';
          </script>";
    exit;
}

// AMBIL DATA YANG DIKIRIM FORM
$id_user = $_POST['id_user'];
$tanggal = $_POST['tanggal'];
$fotoData = $_POST['foto_data'];

// Validasi id_user dari POST harus sama dengan SESSION
if ($id_user != $_SESSION['id_user']) {
    echo "<script>
            alert('Akses tidak sah! ID User tidak sesuai.');
            window.location.href='../app/login_page.php';
          </script>";
    exit;
}

// Validasi format tanggal
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $tanggal)) {
    echo "<script>
            alert('Format tanggal tidak valid!');
            window.location.href='../app/form_absensi.php';
          </script>";
    exit;
}

// VALIDASI FOTO
if (empty($fotoData)) {
    echo "<script>
            alert('Foto absensi wajib diambil!'); 
            window.location.href='../app/form_absensi.php';
          </script>";
    exit;
}

// Validasi format base64
if (!preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $fotoData)) {
    echo "<script>
            alert('Format foto tidak valid!');
            window.location.href='../app/form_absensi.php';
          </script>";
    exit;
}

// CEK APAKAH SUDAH ABSEN HARI INI (Prepared Statement)
$stmt_cek = $conn->prepare("SELECT id_absensi FROM absensi WHERE id_user = ? AND tanggal = ?");

if (!$stmt_cek) {
    echo "<script>
            alert('Error database: " . htmlspecialchars($conn->error) . "');
            window.location.href='../app/form_absensi.php';
          </script>";
    exit;
}

$stmt_cek->bind_param("ss", $id_user, $tanggal);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows > 0) {
    echo "<script>
            alert('Anda sudah melakukan absensi hari ini!');
            window.location.href='../app/dashboard_page.php';
          </script>";
    $stmt_cek->close();
    $conn->close();
    exit;
}
$stmt_cek->close();

// GENERATE ID ABSENSI UNIK (10 karakter sesuai char(10))
$randomNum = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
$id_absensi = 'ABS' . $randomNum;

// Cek apakah ID sudah ada (untuk memastikan unique)
$cekId = $conn->prepare("SELECT id_absensi FROM absensi WHERE id_absensi = ?");

if (!$cekId) {
    echo "<script>
            alert('Error database: " . htmlspecialchars($conn->error) . "');
            window.location.href='../app/form_absensi.php';
          </script>";
    exit;
}

$cekId->bind_param("s", $id_absensi);
$cekId->execute();
$result = $cekId->get_result();

// Loop sampai dapat ID yang unik
while ($result->num_rows > 0) {
    $randomNum = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
    $id_absensi = 'ABS' . $randomNum;

    $cekId->bind_param("s", $id_absensi);
    $cekId->execute();
    $result = $cekId->get_result();
}
$cekId->close();

// PROSES FOTO - HAPUS PREFIX BASE64
$fotoData = str_replace("data:image/png;base64,", "", $fotoData);
$fotoData = str_replace("data:image/jpeg;base64,", "", $fotoData);
$fotoData = str_replace("data:image/jpg;base64,", "", $fotoData);
$fotoData = str_replace(" ", "+", $fotoData);

// DECODE BASE64 KE BINARY
$fotoBinary = base64_decode($fotoData);

// Validasi hasil decode
if ($fotoBinary === false) {
    echo "<script>
            alert('Gagal memproses foto!');
            window.location.href='../app/form_absensi.php';
          </script>";
    exit;
}

// SIMPAN DATA KE DATABASE menggunakan prepared statement
$stmt = $conn->prepare("INSERT INTO absensi (id_absensi, tanggal, foto_absensi, id_user) VALUES (?, ?, ?, ?)");

if ($stmt) {
    // Bind parameters
    $null = NULL;
    $stmt->bind_param("ssbs", $id_absensi, $tanggal, $null, $id_user);

    // Kirim data BLOB
    $stmt->send_long_data(2, $fotoBinary);

    // Eksekusi query
    $hasil = $stmt->execute();

    // CEK BERHASIL / GAGAL
    if ($hasil) {
        echo "<script>
                alert('Absensi berhasil disimpan!');
                window.location.href='../app/dashboard_page.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan absensi: " . htmlspecialchars($stmt->error) . "');
                window.location.href='../app/form_absensi.php';
              </script>";
    }

    $stmt->close();
} else {
    echo "<script>
            alert('Error prepare statement: " . htmlspecialchars($conn->error) . "');
            window.location.href='../app/form_absensi.php';
          </script>";
}

$conn->close();
