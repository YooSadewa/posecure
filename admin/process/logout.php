<?php
session_start();
session_unset();
session_destroy();

session_start();

$_SESSION['alert'] = [
        'icon' => 'success',
        'title' => 'Berhasil logout!',
        'text' => 'Anda telah berhasil logout.',
        'timer' => 1500
];

header("Location: ../../petugas_page/app/login_page.php");
exit;