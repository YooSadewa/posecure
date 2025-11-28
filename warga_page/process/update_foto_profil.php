<?php
include "../../koneksi_database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_user = $_POST['id_user'];

    // Cek apakah ada file diupload
    if (isset($_FILES['foto_baru']) && $_FILES['foto_baru']['tmp_name']) {

        $folderTujuan = "../../assets/warga_img/profile_img/";

        // Buat nama file unik (hindari bentrok)
        $namaFileBaru = "user_" . $id_user . "_" . time() . "_" . basename($_FILES['foto_baru']['name']);
        $pathFileTujuan = $folderTujuan . $namaFileBaru;

        // Pindahkan file ke folder tujuan
        if (move_uploaded_file($_FILES['foto_baru']['tmp_name'], $pathFileTujuan)) {

            // Simpan hanya nama file ke database
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
