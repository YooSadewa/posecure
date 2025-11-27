<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_SESSION['id_user'])) {
    header("location: login_page.php");
    exit;
}

$id_alamat = $_SESSION['id_alamat'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Insiden</title>
    <link rel="icon" href="../../assets/img/logo.png">
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #e5e4e2;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge-photo {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .badge-photo:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="container-fluid pb-2">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header text-white text-center py-4 rounded-top-4" style="background-color: #1E3A8A;">
                        <h4 class="mb-2 fw-bold">
                            <i class="bi bi-table me-2"></i>
                            Tabel Laporan Insiden
                        </h4>
                        <p class="mb-0 opacity-75 small">Data laporan insiden yang masuk dari warga</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h5 class="fw-semibold mb-1">
                                    <i class="bi bi-list-ul text-primary me-2"></i>
                                    Daftar Laporan
                                </h5>
                                <p class="text-muted small mb-0">Total laporan tercatat</p>
                            </div>
                            <a href="form_laporan_insiden.php" class="btn btn-success btn-lg">
                                <i class="bi bi-plus-circle me-2"></i> Tambah Laporan
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead style="background-color: #1E3A8A;" class="text-white">
                                    <tr>
                                        <th class="text-center" style="width: 5%;">No</th>
                                        <th style="width: 20%;">Jam, Tanggal</th>
                                        <th style="width: 35%;">Deskripsi Singkat</th>
                                        <th style="width: 20%;">Nama Pelapor</th>
                                        <th class="text-center" style="width: 20%;">Bukti Foto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = mysqli_query($conn, "
                                    SELECT insiden_keamanan.*, user.`nama`
                                    FROM insiden_keamanan
                                    JOIN user ON insiden_keamanan.id_user = user.id_user
                                    JOIN warga ON user.id_user = warga.id_user
                                    JOIN alamat ON warga.id_alamat = alamat.id_alamat
                                    WHERE warga.id_alamat = '$id_alamat'
                                    ORDER BY tanggal DESC, jam DESC
                                ");
                                    $no = 1;
                                    while ($data = mysqli_fetch_assoc($query)) :
                                    ?>
                                        <tr>
                                            <td class="text-center fw-semibold"><?= $no; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-calendar-event text-primary me-2"></i>
                                                    <div>
                                                        <div class="fw-semibold"><?= $data["jam"]; ?></div>
                                                        <small class="text-muted"><?= $data["tanggal"]; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    <?= $data["keterangan"]; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                                    <span><?= $data["nama"]; ?></span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($data["foto"]): ?>
                                                    <button class="btn btn-primary btn-sm badge-photo" data-bs-toggle="modal" data-bs-target="#photoModal<?= $no; ?>">
                                                        <i class="bi bi-eye me-1"></i> Lihat Foto
                                                    </button>

                                                    <!-- Modal Foto -->
                                                    <div class="modal fade" id="photoModal<?= $no; ?>" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header text-white" style="background-color: #1E3A8A;">
                                                                    <h5 class="modal-title"><i class="bi bi-image me-2"></i>Bukti Foto Insiden</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-center p-4">
                                                                    <img src="../../assets/warga_img/insiden_keamanan/<?= $data['foto'] ?>" class="img-fluid rounded" alt="Bukti Foto">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada foto</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php $no++;
                                    endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info d-flex align-items-center mt-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <small>Klik tombol "Lihat Foto" untuk melihat bukti foto dari setiap laporan insiden.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
<?php include 'footer.php'; ?>
</body>

</html>