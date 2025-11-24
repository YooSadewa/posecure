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
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date("m");
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date("Y");
$bulanList = [
    1 => "Januari",
    2 => "Februari",
    3 => "Maret",
    4 => "April",
    5 => "Mei",
    6 => "Juni",
    7 => "Juli",
    8 => "Agustus",
    9 => "September",
    10 => "Oktober",
    11 => "November",
    12 => "Desember"
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Insiden</title>
    <link rel="icon" href="../../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #E2E8F0;
        }

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

        .card-custom {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .filter-section {
            margin-top: 0.75rem;
        }

        .filter-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            justify-content: space-between;
        }

        .filter-left {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-controls select {
            width: 150px !important;
        }

        @media (max-width: 576px) {
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-left {
                width: 100%;
            }

            .filter-controls select {
                width: 100% !important;
            }

            #downloadPDF {
                width: 100%;
                margin-top: 10px;
            }
        }

        .btn-success {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }

        .btn-success:hover {
            background-color: #157347 !important;
            border-color: #157347 !important;
        }

        table th,
        table td {
            vertical-align: middle !important;
            text-align: center;
        }

        table th {
            background-color: #1E3A8A;
            color: #fff;
        }

        body.pdf-mode .no-print {
            display: none !important;
        }
    </style>
</head>

<body class="d-flex">

    <?php include('sidebar.php'); ?>
    <div class="content w-100">
        <?php include 'header.php' ?>

        <div class="card-custom">
            <h3 class="fw-bold fs-5 mb-4 text-dark">Tabel Laporan Insiden</h3>
            <div class="filter-section">
                <div class="filter-controls">
                    <div class="filter-left">
                        <select id="filterBulan" class="form-select form-select-sm shadow-sm">
                            <?php foreach ($bulanList as $num => $nama): ?>
                                <option value="<?= $num ?>" <?= ($num == $bulan ? 'selected' : '') ?>>
                                    <?= $nama ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select id="filterTahun" class="form-select form-select-sm shadow-sm">
                            <?php
                            $tahunSekarang = date("Y");
                            for ($i = 2022; $i <= $tahunSekarang; $i++):
                            ?>
                                <option value="<?= $i ?>" <?= ($i == $tahun ? 'selected' : '') ?>>
                                    <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <button id="downloadPDF"
                        class="btn btn-success fw-semibold d-flex align-items-center gap-2 shadow-sm px-3 no-print">
                        <i class="fa-solid fa-file-pdf"></i> Download PDF
                    </button>
                </div>
            </div>
            <div class="table-responsive mt-3">
                <div id="laporanPDF">
                    <table class="table table-bordered table-striped mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Jam, Tanggal</th>
                                <th>Jenis Insiden</th>
                                <th>Deskripsi Singkat</th>
                                <th>Nama Pelapor</th>
                                <th>Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $query = mysqli_query($conn, "
                                SELECT insiden_keamanan.*, user.nama 
                                FROM insiden_keamanan 
                                JOIN user ON insiden_keamanan.id_user = user.id_user
                                WHERE MONTH(insiden_keamanan.tanggal) = '$bulan'
                                AND YEAR(insiden_keamanan.tanggal) = '$tahun'
                                ORDER BY insiden_keamanan.tanggal DESC
                            ");

                            $no = 1;
                            while ($data = mysqli_fetch_assoc($query)):
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $data['jam']; ?>, <?= $data['tanggal']; ?></td>
                                    <td><?= $data['jenis_insiden']; ?></td>
                                    <td><?= $data['keterangan']; ?></td>
                                    <td><?= $data['nama']; ?></td>
                                    <td class="text-center">
                                        <?php if ($data["foto"]): ?>
                                            <button class="btn btn-sm badge-photo"
                                                style="background-color: #198754; border-color: #198754; color: #fff;"
                                                data-bs-toggle="modal" data-bs-target="#photoModal<?= $no; ?>">
                                                <i class="bi bi-eye me-1"></i> Lihat Foto
                                            </button>

                                            <!-- Modal Foto -->
                                            <div class="modal fade" id="photoModal<?= $no; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header text-white" style="background-color:#198754;">
                                                            <h5 class="modal-title"><i class="bi bi-image me-2"></i>Bukti Foto Insiden</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-center p-4">
                                                            <img src="data:image/jpeg;base64,<?= base64_encode($data['foto']); ?>" class="img-fluid rounded" alt="Bukti Foto">
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
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="alert alert-info d-flex align-items-center mt-4" role="alert">
                <i class="fa-solid fa-circle-info me-2"></i>
                <small>Klik tombol "Lihat Foto" untuk melihat bukti foto dari setiap laporan insiden.</small>
            </div>
        </div>
    </div>
    <div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: #198754;">
                    <h5 class="modal-title"><i class="fa-solid fa-image me-2"></i>Bukti Foto Insiden</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img id="modalPhoto" src="" class="img-fluid rounded" alt="Bukti Foto">
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById("filterBulan").addEventListener("change", applyFilter);
        document.getElementById("filterTahun").addEventListener("change", applyFilter);

        function applyFilter() {
            let bulan = document.getElementById("filterBulan").value;
            let tahun = document.getElementById("filterTahun").value;
            window.location.href = `laporan_insiden.php?bulan=${bulan}&tahun=${tahun}`;
        }
        const photoButtons = document.querySelectorAll(".badge-photo");
        const modalPhoto = document.getElementById("modalPhoto");

        photoButtons.forEach(button => {
            button.addEventListener("click", () => {
                const photoSrc = button.getAttribute("data-photo");
                modalPhoto.src = "../../uploads/" + photoSrc;
            });
        });
        document.getElementById("downloadPDF").addEventListener("click", () => {
            const element = document.getElementById("laporanPDF");
            document.body.classList.add("pdf-mode");

            const opt = {
                margin: 0.3,
                filename: 'laporan-insiden.pdf',
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                document.body.classList.remove("pdf-mode");
            });
        });
    </script>

</body>

</html>