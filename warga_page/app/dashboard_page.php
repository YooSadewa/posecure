<?php
session_start();
include '../../koneksi_database.php';

if (!isset($_SESSION['id_user'])) {
  header("location: login_page.php");
  exit;
}

$id_user = $_SESSION['id_user'];
$id_alamat = $_SESSION['id_alamat'];

$tahunDipilih = isset($_GET['year']) ? $_GET['year'] : date('Y');

$qTotalInsiden = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM insiden_keamanan 
    WHERE YEAR(tanggal) = '$tahunDipilih'
");
$totalInsiden = mysqli_fetch_assoc($qTotalInsiden)['total'];

$jumlahPerBulan = [];
for ($b = 1; $b <= 12; $b++) {
  $q = mysqli_query($conn, "
        SELECT COUNT(*) AS jumlah 
        FROM insiden_keamanan JOIN warga ON insiden_keamanan.id_user = warga.id_user
        JOIN alamat ON warga.id_alamat = alamat.id_alamat
        WHERE MONTH(tanggal) = '$b' AND YEAR(tanggal) = '$tahunDipilih' AND warga.id_alamat = '$id_alamat'
    ");
  $d = mysqli_fetch_assoc($q);
  $jumlahPerBulan[] = $d['jumlah'];
}

$tahunSekarang = date('Y');
$tahunList = [$tahunSekarang, $tahunSekarang - 1, $tahunSekarang - 2];

$qRonda = mysqli_query($conn, "
    SELECT hari_ronda 
    FROM warga 
    WHERE id_user = '$id_user'
");
$dataRonda = mysqli_fetch_assoc($qRonda);
$hariRonda = $dataRonda['hari_ronda'];

$hariIndoKeInggris = [
  'minggu' => 'sunday',
  'senin' => 'monday',
  'selasa' => 'tuesday',
  'rabu' => 'wednesday',
  'kamis' => 'thursday',
  'jumat' => 'friday',
  'sabtu' => 'saturday'
];

$hariInggrisKeIndo = [
  'sunday' => 'Minggu',
  'monday' => 'Senin',
  'tuesday' => 'Selasa',
  'wednesday' => 'Rabu',
  'thursday' => 'Kamis',
  'friday' => 'Jumat',
  'saturday' => 'Sabtu'
];

$hariBesok = strtolower(date('l', strtotime('+1 day'))); 

$tampilkanAlert = false;
$namaHariRonda = '';

if (!empty($hariRonda) && $hariRonda !== 'NULL') {
  $hariRondaLower = strtolower(trim($hariRonda));

  if (isset($hariIndoKeInggris[$hariRondaLower])) {
    $hariRondaInggris = $hariIndoKeInggris[$hariRondaLower];

    if ($hariBesok === $hariRondaInggris) {
      $tampilkanAlert = true;
      $namaHariRonda = $hariInggrisKeIndo[$hariBesok];
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="icon" href="../../assets/img/logo.png">
  <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

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

    .chart-wrapper {
      position: relative;
      height: 400px;
      min-width: 800px;
    }

    @media (max-width: 768px) {
      .chart-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
    }

    .year-select-wrapper {
      position: relative;
      display: inline-block;
    }

    .year-select-wrapper::before {
      content: '\f073';
      font-family: 'bootstrap-icons';
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #049055;
      pointer-events: none;
      z-index: 1;
    }

    #yearSelect {
      padding-left: 35px;
      font-weight: 600;
      border: 2px solid #049055;
      color: #049055;
    }

    #yearSelect:focus {
      border-color: #049055;
      box-shadow: 0 0 0 0.25rem rgba(4, 144, 85, 0.25);
    }

    .alert-ronda {
      animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>
  <?php include 'navbar.php' ?>
  <?php include 'header.php' ?>

  <div class="container-fluid pb-2">
    <div class="row justify-content-center">
      <div class="col-lg-12">

        <?php if ($tampilkanAlert): ?>
          <div class="alert alert-warning alert-dismissible fade show alert-ronda shadow-sm" role="alert">
            <div class="d-flex align-items-center">
              <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
              <div>
                <h5 class="alert-heading mb-1">
                  <i class="bi bi-bell-fill me-2"></i>Pengingat Jadwal Ronda
                </h5>
                <p class="mb-0">
                  <strong>Besok, <?= $namaHariRonda ?></strong> adalah jadwal ronda Anda. Jangan lupa untuk berpartisipasi dalam kegiatan ronda malam!
                </p>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-header text-white text-center py-4 rounded-top-4" style="background-color: #1E3A8A;">
            <h4 class="mb-2 fw-bold">
              <i class="bi bi-graph-up me-2"></i>
              Grafik Laporan Insiden
            </h4>
            <p class="mb-0 opacity-75 small">Statistik insiden per bulan dalam satu tahun</p>
          </div>

          <div class="card-body p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
              <div>
                <h5 class="fw-semibold mb-1">
                  <i class="bi bi-bar-chart-line text-primary me-2"></i>
                  Tren Insiden Bulanan
                </h5>
                <p class="text-muted small mb-0">Data berdasarkan laporan yang masuk</p>
              </div>
              <div class="year-select-wrapper">
                <select class="form-select form-select-lg" id="yearSelect" onchange="ubahTahun()">
                  <?php foreach ($tahunList as $t): ?>
                    <option value="<?= $t ?>" <?= $t == $tahunDipilih ? 'selected' : '' ?>>
                      Tahun <?= $t ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="chart-scroll">
              <div class="chart-wrapper mx-auto">
                <canvas id="incidentChart"></canvas>
              </div>
            </div>

            <div class="alert alert-info d-flex align-items-center mt-4" role="alert">
              <i class="bi bi-info-circle-fill me-2"></i>
              <small>Grafik menampilkan jumlah insiden yang dilaporkan setiap bulan. Pilih tahun untuk melihat data periode lainnya.</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php' ?>

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

    function ubahTahun() {
      const year = document.getElementById("yearSelect").value;
      window.location.href = "?year=" + year;
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php if (isset($_SESSION['alert'])) : ?>
      <script>
          Swal.fire({
              icon: '<?= $_SESSION['alert']['type'] ?>',
              title: '<?= $_SESSION['alert']['title'] ?>',
              text: '<?= $_SESSION['alert']['message'] ?>',
              confirmButtonColor: '#1E3A8A'
          });
      </script>
      <?php unset($_SESSION['alert']); ?>
  <?php endif; ?>
  
</body>

</html>