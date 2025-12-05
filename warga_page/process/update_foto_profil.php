<?php
include "../../koneksi_database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_user = $_POST['id_user'];

    if (isset($_FILES['foto_baru']) && $_FILES['foto_baru']['tmp_name']) {

        $folderTujuan = "../../assets/warga_img/profile_img/";

        $namaFileBaru = "user_" . $id_user . "_" . time() . "_" . basename($_FILES['foto_baru']['name']);
        $pathFileTujuan = $folderTujuan . $namaFileBaru;

        if (move_uploaded_file($_FILES['foto_baru']['tmp_name'], $pathFileTujuan)) {

            $query = "UPDATE user SET foto = '$namaFileBaru' WHERE id_user = '$id_user'";

            if (mysqli_query($conn, $query)) {
                header("Location: ../app/dashboard_page.php");
                exit;
            } else {
                echo "Gagal update database: " . mysqli_error($conn);
            }

        } else {
            echo "Gagal menyimpan file ke folder.";
        }
    }
}
?>
