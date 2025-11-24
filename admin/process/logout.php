<?php
session_start();
$_SESSION = [];
session_unset();
session_destroy();
echo "<script>
        alert('Anda telah berhasil logout.');
        window.location.href = '../../petugas_page/app/login_page.php?pesan=logout';
        </script>";
exit;
