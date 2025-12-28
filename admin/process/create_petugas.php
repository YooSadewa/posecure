<?php

// untuk memulai atau mengaktifkan session agar bisa menyimpan dana membaca data session.
session_start();

// Untuk memanggil file koneksi database agar bisa menggunakan query.
include '../../koneksi_database.php';

// Untuk mengecek hak akses 
// Jika user belum login atau user role nya bukan admin maka diarahkan ke halaman login.
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit; //untuk mengehntikan eksekusi script agar tidak bisa lanjut.
}

// Untuk memastikan bahwa halaman ini diakses lewat form POST, bukan URL langsung. Jika bukan POST kode didalamnya tidak akan dijalankan.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Untuk mengambil data dari form input yang diambil adalah nama inputnya.
  // mysqli_real_escape_string = untuk mencegah SQL injection (serangan dengan menyisipkan perintah SQL berbahaya lewat input user untuk mengakali atau merusak database.)
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $nama = mysqli_real_escape_string($conn, $_POST['nama']);
  $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
  $kelurahan = mysqli_real_escape_string($conn, $_POST['kelurahan']);
  $rt = mysqli_real_escape_string($conn, $_POST['rt']);
  $rw = mysqli_real_escape_string($conn, $_POST['rw']);

  // password_hash = mengubah password menjadi kode acak yang aman sebelum disimpan di database.
  // PASSWORD_DEFAULT = untuk nyuruh PHP menggunakan algoritma hash paling aman secara otomatis. intinya udah sepaket sama password_hash
  $password_hash = password_hash($username, PASSWORD_DEFAULT);

  // Cek apakah username sudah ada di tabel databas atau belum (agar tidak duplikat)
  $cek_username = mysqli_query($conn, "SELECT username FROM user WHERE username = '$username'");
  
   // Jika jumlah barisnya lebih dari 0 maka username sudah terpakai.
   // mysqli_num_rows() dipakai untuk menghitung jumlah baris data hasil query SELECT.
  if (mysqli_num_rows($cek_username) > 0) {
    $_SESSION['alert'] = [
      'icon' => 'warning',
      'title' => 'Gagal!',
      'text' => 'Username sudah terpakai! Silakan gunakan username lain.'
    ];
    header("Location: ../app/dashboard_page.php");
    exit;
  }

 // untuk menyimpan id alamat karena di database tidak menggunakna autoincrement.
  $id_alamat = "";

  // cek apakah alamat sudah pernah disimpan atau belum di database.
  $cek_alamat = mysqli_query($conn, "SELECT id_alamat FROM alamat WHERE kecamatan = '$kecamatan' AND kelurahan = '$kelurahan' AND no_rt = '$rt' AND no_rw = '$rw'");
  
  // Jika sudah ada mengambil id_alamat yang sudah ada agar tidak duplikat data.
  if (mysqli_num_rows($cek_alamat) > 0) {
    $id_alamat = mysqli_fetch_assoc($cek_alamat)['id_alamat'];
  } else {

    // Jika belum ada maka mengambil id_alamat terakhir 
    $query_last = "SELECT id_alamat FROM alamat WHERE id_alamat LIKE 'A-%' ORDER BY CAST(SUBSTRING(id_alamat, 3) AS UNSIGNED) DESC LIMIT 1";
    $data_last = mysqli_fetch_assoc(mysqli_query($conn, $query_last));

    if ($data_last) {
      $angka_akhir_alamat = (int)substr($data_last['id_alamat'], 2);
    } else {
      $angka_akhir_alamat = 0;
    }

    // membuat id baru otomatis
    $id_alamat = 'A-' . str_pad($angka_akhir_alamat + 1, 2, '0', STR_PAD_LEFT);

    // Insert Alamat Baru
    $insert_alamat = "INSERT INTO alamat (id_alamat, kecamatan, kelurahan, no_rt, no_rw) VALUES ('$id_alamat', '$kecamatan', '$kelurahan', '$rt', '$rw')";

    if (!mysqli_query($conn, $insert_alamat)) {
      $_SESSION['alert'] = [
        'icon' => 'error',
        'title' => 'Gagal!',
        'text' => 'Gagal menambahkan data alamat: ' . mysqli_error($conn)
      ];
      header("Location: ../app/dashboard_page.php");
      exit;
    }
  }

  // user ==========================================
  $query_user = "SELECT id_user FROM user WHERE id_user LIKE 'P-%' ORDER BY CAST(SUBSTRING(id_user, 3) AS UNSIGNED) DESC LIMIT 1";
  $data_user = mysqli_fetch_assoc(mysqli_query($conn, $query_user));

  if ($data_user) {
    $angka_akhir_user = (int)substr($data_user['id_user'], 2);
  } else {
    $angka_akhir_user = 0;
  }
  $id_user   = 'P-' . str_pad($angka_akhir_user + 1, 2, '0', STR_PAD_LEFT);
  $insert_user = "INSERT INTO user (id_user, nama, username, password, role, foto, no_telp) VALUES ('$id_user', '$nama', '$username', '$password_hash', 'petugas_keamanan', '', '-')";

  if (!mysqli_query($conn, $insert_user)) {
    $_SESSION['alert'] = [
      'icon' => 'error',
      'title' => 'Gagal!',
      'text' => 'Gagal menambahkan data petugas keamanan: ' . mysqli_error($conn)
    ];
    header("Location: ../app/dashboard_page.php");
    exit;
  }

  // petugas keamanan ==========================================
  $insert_petugas = "INSERT INTO petugas_keamanan (id_user,status_keaktifan, id_alamat) VALUES ('$id_user', 'aktif', '$id_alamat')";

  if (!mysqli_query($conn, $insert_petugas)) {
    $_SESSION['alert'] = [
      'icon' => 'error',
      'title' => 'Gagal!',
      'text' => 'Gagal menambahkan detail petugas keamanan: ' . mysqli_error($conn)
    ];
    session_write_close(); // PERBAIKAN: Tambah ini
    header("Location: ../app/dashboard_page.php");
    exit;
  }

  // SUKSES
  $_SESSION['alert'] = [
    'icon' => 'success',
    'title' => 'Berhasil!',
    'text' => 'Data petugas keamanan berhasil ditambahkan!'
  ];

  header("Location: ../app/dashboard_page.php");
  exit;
}
