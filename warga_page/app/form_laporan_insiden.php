<?php
session_start();
include "../../koneksi_database.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'warga') {
    header("location: login_page.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Laporan Insiden</title>
    <link rel="icon" href="../../assets/img/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .required {
            color: red;
            font-weight: bold;
        }

        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #e5e4e2;
        }

        body.modal-open {
            padding-right: 0 !important;
            overflow: hidden !important;
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
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Form Laporan Insiden
                        </h4>
                        <p class="mb-0 opacity-75 small">Laporkan kejadian insiden yang terjadi di lingkungan Kecamatan Dummy, Kelurahan Dummy, RT 00 RW 00</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form id="formLaporanInsiden" action="../process/laporan_insiden_proses.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_user" value="<?= $_SESSION['id_user']; ?>">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label fw-semibold">
                                        <i class="bi bi-person-fill text-primary me-1"></i>
                                        Nama Pelapor
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg bg-secondary-subtle" id="nama" name="nama" value="<?= $_SESSION['nama']; ?>" readonly required>
                                </div>

                                <div class="col-md-6">
                                    <label for="jenis_insiden" class="form-label fw-semibold">
                                        <i class="bi bi-tag-fill text-primary me-1"></i>
                                        Jenis Insiden
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" id="jenis_insiden" name="jenis_insiden" required>
                                        <option value="" selected disabled hidden>Pilih Jenis Insiden</option>
                                        <option value="kriminalitas">Kriminalitas</option>
                                        <option value="gangguan_ketertiban">Gangguan Ketertiban</option>
                                        <option value="ancaman_fisik_sosial">Ancaman Fisik/Sosial</option>
                                        <option value="bencana">Bencana</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="tanggal" class="form-label fw-semibold">
                                        <i class="bi bi-calendar-event text-primary me-1"></i>
                                        Tanggal Kejadian
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control form-control-lg" id="tanggal" name="tanggal" max="<?= date('Y-m-d') ?>" required>
                                </div>

                                <div class="col-md-4">
                                    <label for="jam" class="form-label fw-semibold">
                                        <i class="bi bi-clock text-primary me-1"></i>
                                        Jam
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" class="form-control form-control-lg" id="jam" name="jam" required>
                                </div>


                                <div class="col-md-4">
                                    <label for="foto" class="form-label fw-semibold">
                                        <i class="bi bi-image-fill text-primary me-1"></i>
                                        Bukti Foto
                                    </label>
                                    <input type="file" class="form-control form-control-lg" id="foto" name="foto" accept="image/*">
                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Format: JPG, PNG, atau JPEG (Maks. 5MB)
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="keterangan" class="form-label fw-semibold">
                                        <i class="bi bi-file-text-fill text-primary me-1"></i>
                                        Deskripsi Kejadian
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="keterangan" name="keterangan" class="form-control form-control-lg" rows="5" placeholder="Jelaskan kronologi kejadian secara singkat dan jelas..." required></textarea>
                                </div>
                            </div>

                            <div class="alert alert-info d-flex align-items-center mt-4" role="alert">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <small>Pastikan semua data yang diisi sudah benar dan lengkap sebelum mengirim laporan.</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="bi bi-send-fill me-2"></i>
                                    Kirim Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('tanggal').addEventListener('change', function() {
            const jamInput = document.getElementById('jam');
            const today = new Date().toISOString().split('T')[0];

            if (this.value === today) {
                // Hari ini: set max jam sekarang
                const now = new Date();
                jamInput.max = now.toTimeString().slice(0, 5);
            } else {
                // Hari lalu: bebas
                jamInput.removeAttribute('max');
            }
        });

        document.getElementById('jam').addEventListener('input', function() {
            if (this.max && this.value > this.max) {
                alert('Jam tidak boleh melebihi jam sekarang!');
                this.value = this.max;
            }
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>