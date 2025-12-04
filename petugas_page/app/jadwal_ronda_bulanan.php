<?php
session_start();
include '../../koneksi_database.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'petugas_keamanan') {
  header("location: login_page.php");
  exit;
}

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

if ($bulan < 1 || $bulan > 12) {
  $bulan = date('n');
}

$tahun_sekarang = date('Y');
if ($tahun < ($tahun_sekarang - 10) || $tahun > ($tahun_sekarang + 1)) {
  $tahun = $tahun_sekarang;
}

$nama_bulan = [
  1 => 'Januari',
  2 => 'Februari',
  3 => 'Maret',
  4 => 'April',
  5 => 'Mei',
  6 => 'Juni',
  7 => 'Juli',
  8 => 'Agustus',
  9 => 'September',
  10 => 'Oktober',
  11 => 'November',
  12 => 'Desember'
];

$id_alamat = $_SESSION['id_alamat'];

$stmt_warga = $conn->prepare(
  "SELECT DISTINCT u.id_user, u.nama, w.hari_ronda
   FROM user u
   INNER JOIN warga w ON u.id_user = w.id_user
   WHERE w.hari_ronda IS NOT NULL AND w.id_alamat = '$id_alamat'
   ORDER BY u.nama"
);
$stmt_warga->execute();
$result_warga = $stmt_warga->get_result();
$daftar_warga = [];
while ($row = $result_warga->fetch_assoc()) {
  $daftar_warga[$row['id_user']] = [
    'nama' => $row['nama'],
    'hari_ronda' => $row['hari_ronda']
  ];
}
$stmt_warga->close();

$stmt_absensi = $conn->prepare(
  "SELECT DATE(tanggal) as tanggal, id_user
   FROM absensi
   WHERE MONTH(tanggal) = ? AND YEAR(tanggal) = ?"
);
$stmt_absensi->bind_param("ii", $bulan, $tahun);
$stmt_absensi->execute();
$result_absensi = $stmt_absensi->get_result();

$data_hadir = [];
while ($row = $result_absensi->fetch_assoc()) {
  $data_hadir[$row['tanggal']][] = $row['id_user'];
}
$stmt_absensi->close();

$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$tanggal_pertama = mktime(0, 0, 0, $bulan, 1, $tahun);
$hari_pertama = date('w', $tanggal_pertama); 

$hari_map = [
  'Sunday' => 'Minggu',
  'Monday' => 'Senin',
  'Tuesday' => 'Selasa',
  'Wednesday' => 'Rabu',
  'Thursday' => 'Kamis',
  'Friday' => 'Jumat',
  'Saturday' => 'Sabtu'
];

$total_hadir = 0;
$total_tidak_hadir = 0;
$total_jadwal = 0;

for ($hari = 1; $hari <= $jumlah_hari; $hari++) {
  $tanggal_str = sprintf("%04d-%02d-%02d", $tahun, $bulan, $hari);
  $timestamp = strtotime($tanggal_str);
  $nama_hari_eng = date('l', $timestamp);
  $nama_hari = $hari_map[$nama_hari_eng];

  foreach ($daftar_warga as $id => $data) {
    if (strtolower($data['hari_ronda']) === strtolower($nama_hari)) {
      $total_jadwal++;
      if (isset($data_hadir[$tanggal_str]) && in_array($id, $data_hadir[$tanggal_str])) {
        $total_hadir++;
      } else {
        $total_tidak_hadir++;
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kalender Absensi Ronda</title>
  <link rel="icon" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 0;
    }

    .calendar-day {
      min-height: 120px;
      border: 1px solid #dee2e6;
      border-top: none;
      border-left: none;
    }

    .calendar-day:nth-child(7n+1) {
      border-left: 1px solid #dee2e6;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    body.pdf-mode .no-print {
      display: none !important;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0;
        padding: 1rem;
      }

      .calendar-day {
        min-height: 80px;
      }
    }
  </style>
</head>

<body class="d-flex">
  <?php include('sidebar.php'); ?>

  <div class="content w-100">
    <?php include 'header.php' ?>

    <div class="bg-white rounded-3 p-4 shadow-sm">
      <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-4">
        <h3 class="fw-bold fs-5 mb-0">
          Kalender Absensi Ronda
        </h3>
        <div class="d-flex gap-2 flex-wrap align-items-center no-print">
          <form method="GET" action="" class="d-flex gap-2 align-items-center">
            <select name="bulan" class="form-select" style="width: auto;" onchange="this.form.submit()">
              <?php foreach ($nama_bulan as $key => $value): ?>
                <option value="<?= $key ?>" <?= ($bulan == $key) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($value) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <select name="tahun" class="form-select" style="width: auto;" onchange="this.form.submit()">
              <?php for ($t = $tahun_sekarang; $t >= $tahun_sekarang - 5; $t--): ?>
                <option value="<?= $t ?>" <?= ($tahun == $t) ? 'selected' : '' ?>>
                  <?= $t ?>
                </option>
              <?php endfor; ?>
            </select>
          </form>

          <a href="jadwal_ronda.php" class="btn btn-success">
            <i class="bi bi-calendar-check"></i> Lihat Jadwal
          </a>
          <button id="downloadPDF" class="btn btn-success px-3 fw-semibold">
            <i class="fa-solid fa-file-pdf"></i> Download PDF
          </button>
        </div>
      </div>

      <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <div>
          Menampilkan data <strong><?= htmlspecialchars($nama_bulan[$bulan]) ?> <?= htmlspecialchars($tahun) ?></strong>
          <br>
          <small>Warna hijau = Hadir, Warna merah = Tidak Hadir</small>
        </div>
      </div>

      <div id="laporanPDF">
        <div class="text-center mb-3 d-none" id="pdfHeader">
          <h4 class="fw-bold">KALENDER ABSENSI RONDA</h4>
          <h6>Periode: <?= htmlspecialchars($nama_bulan[$bulan]) ?> <?= htmlspecialchars($tahun) ?></h6>
          <hr>
        </div>

        <div class="bg-white rounded-3 overflow-hidden border">
          <div class="row g-0 bg-dark text-white text-center fw-semibold">
            <div class="col border-end border-secondary p-3">Minggu</div>
            <div class="col border-end border-secondary p-3">Senin</div>
            <div class="col border-end border-secondary p-3">Selasa</div>
            <div class="col border-end border-secondary p-3">Rabu</div>
            <div class="col border-end border-secondary p-3">Kamis</div>
            <div class="col border-end border-secondary p-3">Jumat</div>
            <div class="col p-3">Sabtu</div>
          </div>

          <div class="calendar-grid">
            <?php
            for ($i = 0; $i < $hari_pertama; $i++) {
              echo '<div class="calendar-day bg-light"></div>';
            }

            $today = date('Y-m-d');

            for ($hari = 1; $hari <= $jumlah_hari; $hari++) {
              $tanggal_str = sprintf("%04d-%02d-%02d", $tahun, $bulan, $hari);
              $timestamp = strtotime($tanggal_str);
              $nama_hari_eng = date('l', $timestamp);
              $nama_hari = $hari_map[$nama_hari_eng];

              $is_today = ($tanggal_str === $today) ? 'bg-warning bg-opacity-10' : 'bg-white';

              echo '<div class="calendar-day p-2 ' . $is_today . '">';
              echo '<div class="fw-bold small text-secondary mb-2">' . $hari . '</div>';
              echo '<div class="text-muted" style="font-size: 0.7rem; margin-bottom: 0.5rem;">' . $nama_hari . '</div>';

              $ada_jadwal = false;
              foreach ($daftar_warga as $id => $data) {
                if (strtolower($data['hari_ronda']) === strtolower($nama_hari)) {
                  $ada_jadwal = true;
                  $hadir = isset($data_hadir[$tanggal_str]) && in_array($id, $data_hadir[$tanggal_str]);

                  if ($hadir) {
                    echo '<div class="bg-success bg-opacity-25 border-start border-success border-3 px-2 py-1 mb-1 rounded d-flex align-items-center gap-1" style="font-size: 0.7rem;">';
                    echo '<i class="bi bi-check-circle-fill text-success" style="font-size: 0.6rem;"></i>';
                  } else {
                    echo '<div class="bg-danger bg-opacity-25 border-start border-danger border-3 px-2 py-1 mb-1 rounded d-flex align-items-center gap-1" style="font-size: 0.7rem;">';
                    echo '<i class="bi bi-x-circle-fill text-danger" style="font-size: 0.6rem;"></i>';
                  }

                  echo '<span>' . htmlspecialchars($data['nama']) . '</span>';
                  echo '</div>';
                }
              }

              if (!$ada_jadwal) {
                echo '<div class="text-muted text-center" style="font-size: 0.65rem;">Tidak ada jadwal</div>';
              }

              echo '</div>';
            }
            ?>
          </div>
        </div>

        <div class="text-end mt-3 d-none" id="pdfFooter">
          <small>Dicetak pada: <?= date('d-m-Y H:i:s') ?></small>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.getElementById("downloadPDF")?.addEventListener("click", () => {
      const element = document.getElementById("laporanPDF");
      const bulan = "<?= htmlspecialchars($nama_bulan[$bulan]) ?>";
      const tahun = "<?= htmlspecialchars($tahun) ?>";

      document.getElementById("pdfHeader").classList.remove("d-none");
      document.getElementById("pdfFooter").classList.remove("d-none");
      document.body.classList.add("pdf-mode");

      const opt = {
        margin: [10, 10, 15, 10],
        filename: `kalender-absensi-${bulan}-${tahun}.pdf`,
        image: {
          type: 'jpeg',
          quality: 0.98
        },
        html2canvas: {
          scale: 3,
          useCORS: true,
          letterRendering: true,
        },
        jsPDF: {
          unit: 'mm',
          format: 'a4',
          orientation: 'landscape'
        }
      };

      html2pdf().set(opt).from(element).save().then(() => {
        document.body.classList.remove("pdf-mode");
        document.getElementById("pdfHeader").classList.add("d-none");
        document.getElementById("pdfFooter").classList.add("d-none");
      });
    });
  </script>

  <script>
    const profileButton = document.getElementById("profileButton");
    const profileDropdown = document.getElementById("profileDropdown");
    const caretIcon = document.getElementById("caretIcon");

    profileButton?.addEventListener("click", () => {
      profileDropdown.classList.toggle("d-none");
      caretIcon.classList.toggle("rotate-180");
    });

    window.addEventListener("click", (e) => {
      if (!profileButton?.contains(e.target) && !profileDropdown?.contains(e.target)) {
        profileDropdown.classList.add("d-none");
        caretIcon.classList.remove("rotate-180");
      }
    });
  </script>
</body>

</html>