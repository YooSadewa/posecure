<?php
include "../../koneksi_database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_user = $_POST['id_user'];

    if (isset($_FILES['foto_baru']) && $_FILES['foto_baru']['tmp_name']) {

        $foto_tmp = $_FILES['foto_baru']['tmp_name'];

        // Konversi file ke binary
        $foto_data = addslashes(file_get_contents($foto_tmp));

        $query = "UPDATE user SET foto = '$foto_data' WHERE id_user = '$id_user'";

        if (mysqli_query($conn, $query)) {
            header("Location: ../app/dashboard.php");
            exit;
        } else {
            echo "Gagal upload foto: " . mysqli_error($conn);
        }
    }
}
?>
