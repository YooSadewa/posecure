<?php 
session_start(); // untuk mengaktifkan atau memulai session.
session_unset(); // untuk menghapus semua data session.
session_destroy(); // untuk menghapus session sepenuhnya dari server.

session_start(); // memulai session baru yang kosong.

$_SESSION['alert'] = [
        'icon' => 'success',
        'title' => 'Berhasil logout!',
        'text' => 'Anda telah berhasil logout.',
        'timer' => 1500
];

header("Location: ../../petugas_page/app/login_page.php");
exit;