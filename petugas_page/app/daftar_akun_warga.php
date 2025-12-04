<?php

session_start();
include "../../koneksi_database.php";

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("location: login_page.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] !== 'petugas_keamanan') {
    echo "Anda tidak memiliki akses ke halaman ini!";
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>poSecure - Daftar Akun Warga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #E0E0E0;
        }

        /* Main content dengan margin kiri seperti jadwal ronda */
        .content {
            margin-left: 250px;
            padding: 2rem;
            background-color: #E0E0E0;
            min-height: 100vh;
        }

        @media (max-width: 1024px) {
            .content {
                margin-left: 0;
            }
        }

        /* Card styling seperti jadwal ronda */
        .card-custom {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Kolom kiri form sejajar */
        .input-group-text {
            width: 130px;
            justify-content: left;
            font-weight: 500;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<body class="d-flex">

    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content w-100">

        <?php include 'header.php'; ?>

        <div class="card-custom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
                <h3 class="fw-bold fs-5 mb-0">Daftar Akun Warga</h3>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal_tambah">
                    <i class="fas fa-plus me-1"></i> Tambahkan Warga
                </button>
            </div>

            <!-- Tabel -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light align-middle">
                        <tr>
                            <th>No.</th>
                            <th>Blok Rumah</th>
                            <th>Nomor KK</th>
                            <th>Nama Kepala Keluarga</th>
                            <th>No Telp</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-left">
                        <?php
                        $id_alamat = $_SESSION['id_alamat'];

                        // Pagination
                        $limit = 10;
                        $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $start = ($page - 1) * $limit;

                        // Hitung total data
                        $count = mysqli_query($conn, "SELECT COUNT(*) AS total FROM warga WHERE id_alamat = '$id_alamat'");
                        $total_data = mysqli_fetch_assoc($count)['total'];
                        $total_page = ceil($total_data / $limit);

                        // Ambil data sesuai halaman
                        $query = mysqli_query($conn, "
                            SELECT warga.*, user.nama, user.no_telp 
                            FROM warga 
                            JOIN user ON warga.id_user = user.id_user 
                            WHERE warga.id_alamat = '$id_alamat'
                            LIMIT $start, $limit
                        ");
                        $no = $start + 1;
                        while ($data = mysqli_fetch_assoc($query)) :
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $data["blok_rumah"]; ?></td>
                                <td><?= $data["no_kk"]; ?></td>
                                <td><?= $data["nama"]; ?></td>
                                <td><?= $data["no_telp"]; ?></td>
                                <td>
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        <button class="btn btn-warning btn-sm flex-fill text-nowrap" data-bs-toggle="modal" data-bs-target="#modal_edit<?= $data['id_user']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm flex-fill text-nowrap" data-bs-toggle="modal" data-bs-target="#modal_hapus<?= $data['id_user']; ?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade modal-fullscreen-sm-down" id="modal_edit<?= $data['id_user']; ?>" tabindex="-1" aria-labelledby="modal_edit_label" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="modal_edit_label">Edit Akun Warga</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <form action="../process/edit_akun_warga_proses.php" method="POST" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_user" id="edit_id_user" value="<?= $data['id_user']; ?>">

                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">Nomor KK <span class="text-danger ms-2">*</span></span>
                                                    <input type="number" class="form-control" name="no_kk" id="edit_no_kk" value="<?= $data['no_kk']; ?>">
                                                </div>
                                                
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">Nama <span class="text-danger ms-2">*</span></span>
                                                    <input type="text" class="form-control" name="nama" id="edit_nama" value="<?= $data['nama']; ?>">
                                                </div>

                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">Blok Rumah <span class="text-danger ms-2">*</span></span>
                                                    <input type="text" class="form-control" name="blok_rumah" id="edit_blok_rumah" value="<?= $data['blok_rumah']; ?>">
                                                </div>
                                                
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">No Telp <span class="text-danger ms-2">*</span></span>
                                                    <input type="number" class="form-control" name="no_telp" id="edit_no_telp" value="<?= $data['no_telp']; ?>">
                                                </div>

                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">Foto Profil<span class="text-danger ms-2">*</span></span>
                                                    <input type="file" class="form-control" name="foto" id="edit_foto">
                                                </div>

                                            </div>

                                            <div class="modal-footer flex-wrap">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Hapus -->
                            <div class="modal fade modal-fullscreen-sm-down" id="modal_hapus<?= $data['id_user']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="../process/hapus_akun_warga_proses.php" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Hapus Akun Warga</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body text-center">
                                                <p>Apakah Anda yakin ingin menghapus data warga ini?</p>

                                                <input type="hidden" name="id_user" value="<?= $data['id_user']; ?>">
                                            </div>

                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-3 pe-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>

                        <?php for ($i = 1; $i <= $total_page; $i++) : ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_page) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modal_tambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../process/tambah_akun_warga_proses.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Daftar Akun Warga</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Nomor KK <span class="text-danger ms-2">*</span></span>
                            <input type="number" class="form-control" name="no_kk" required>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text">Nama <span class="text-danger ms-2">*</span></span>
                            <input type="text" class="form-control" name="nama" required>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text">Blok Rumah <span class="text-danger ms-2">*</span></span>
                            <input type="text" class="form-control" name="blok_rumah" required>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text">No Telp <span class="text-danger ms-2">*</span></span>
                            <input type="number" class="form-control" name="no_telp" required>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text">Foto Profil <span class="text-danger ms-2">*</span></span>
                            <input type="file" class="form-control" name="foto" required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="submit">Daftarkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($_SESSION['alert'])) : ?>
        <script>
            Swal.fire({
                icon: '<?= $_SESSION['alert']['type'] ?>',
                title: '<?= $_SESSION['alert']['title'] ?>',
                text: '<?= $_SESSION['alert']['message'] ?>',

            });
        </script>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
</body>
</html>