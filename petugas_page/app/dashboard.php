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
$hariIni = strtolower(date('l'));
$convertHari = [
    'monday' => 'senin',
    'tuesday' => 'selasa',
    'wednesday' => 'rabu',
    'thursday' => 'kamis',
    'friday' => 'jumat',
    'saturday' => 'sabtu',
    'sunday' => 'minggu'
];

$hariRonda = $convertHari[$hariIni];
$qJadwalRonda = mysqli_query($conn, "
    SELECT user.nama, warga.hari_ronda
    FROM warga
    JOIN user ON warga.id_user = user.id_user
    WHERE warga.hari_ronda = '$hariRonda'
    ORDER BY user.nama ASC
");

$id_user = $_SESSION['id_user'];
$qAlamat = mysqli_query($conn, "
    SELECT alamat.kecamatan, alamat.kelurahan, alamat.no_rt, alamat.no_rw
    FROM petugas_keamanan 
    JOIN alamat ON petugas_keamanan.id_alamat = alamat.id_alamat
    WHERE petugas_keamanan.id_user = '$id_user'
");

$alamat = mysqli_fetch_assoc($qAlamat);
$qTotalWarga = mysqli_query($conn, "SELECT COUNT(*) AS total FROM user WHERE role = 'warga'");
$totalWarga = mysqli_fetch_assoc($qTotalWarga)['total'];
$qTotalInsiden = mysqli_query($conn, "SELECT COUNT(*) AS total FROM insiden_keamanan");
$totalInsiden = mysqli_fetch_assoc($qTotalInsiden)['total'];
$jumlahPerBulan = [];
for ($b = 1; $b <= 12; $b++) {
    $q = mysqli_query($conn, "
        SELECT COUNT(*) AS jumlah 
        FROM insiden_keamanan 
        WHERE MONTH(tanggal) = '$b'
    ");
    $d = mysqli_fetch_assoc($q);
    $jumlahPerBulan[] = $d['jumlah'];
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Petugas</title>

    <link rel="icon" href="../../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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
    </style>
</head>

<body class="d-flex">

    <?php include('sidebar.php'); ?>

    <div class="content w-100">
        <?php include 'header.php' ?>

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-4">
                <div class="card-custom h-100">
                    <h3 class="fw-bold fs-5">Alamat</h3>
                    <p class="text-secondary mt-2 small lh-base mb-0">
                        Kecamatan <?= $alamat['kecamatan']; ?>,<br>
                        Kelurahan <?= $alamat['kelurahan']; ?>,<br>
                        RT <?= $alamat['no_rt']; ?> RW <?= $alamat['no_rw']; ?>
                    </p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card-custom h-100 d-flex flex-column align-items-center justify-content-center text-center">
                    <h3 class="fw-bold fs-5">Total Akun Warga</h3>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <i class="fa-solid fa-users fs-5 text-secondary"></i>
                        <p class="fs-2 fw-bold text-dark mb-0">
                            <?= $totalWarga ?> <span class="fs-6 fw-medium">Akun</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card-custom h-100 d-flex flex-column align-items-center justify-content-center text-center">
                    <h3 class="fw-bold fs-5">Total Laporan Insiden</h3>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <i class="fa-solid fa-chart-line fs-5 text-secondary"></i>
                        <p class="fs-2 fw-bold text-dark mb-0">
                            <?= $totalInsiden ?> <span class="fs-6 fw-medium">Laporan</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4" style="min-height: 400px;">
            <div class="col-12 col-md-8">
                <div class="card-custom h-100">
                    <h3 class="fw-bold fs-5 mb-4">Grafik Insiden Keamanan</h3>
                    <div style="height: 300px;">
                        <canvas id="incidentChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card-custom h-100">
                    <h3 class="fw-bold fs-5 mb-4">Jadwal <?= ucfirst($hariRonda) ?></h3>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered table-striped mb-0 small">
                            <tbody>
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($qJadwalRonda) > 0):
                                    while ($row = mysqli_fetch_assoc($qJadwalRonda)): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['nama'] ?></td>
                                        </tr>
                                    <?php
                                    endwhile;
                                else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Tidak ada warga ronda hari ini</td>
                                    </tr>
                                <?php endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const dataInsiden = <?= json_encode($jumlahPerBulan); ?>;

        new Chart(document.getElementById('incidentChart'), {
            type: 'line',
            data: {
                labels: [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ],
                datasets: [{
                    label: 'Jumlah Insiden',
                    data: dataInsiden,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16,185,129,0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: '#10B981',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 15,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>