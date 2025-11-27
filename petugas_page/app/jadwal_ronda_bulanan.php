<?php
session_start();
if (!isset($_SESSION['regenerated'])) {
  session_regenerate_id(true);
  $_SESSION['regenerated'] = true;
}

include '../../koneksi_database.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
  header("location: login_page.php?pesan=belum_login");
  exit;
}

if ($_SESSION['role'] !== 'petugas_keamanan') {
  echo "Anda tidak memiliki akses ke halaman ini!";
  exit;
}

// Validasi bulan dan tahun
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
  1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
  5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
  9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;
$offset = ($page - 1) * $limit;

$id_alamat = $_SESSION['id_alamat'];

$stmt_jadwal = $conn->prepare(
  "SELECT 
      a.tanggal,
      u.nama,
      u.id_user,
      w.hari_ronda
   FROM absensi a
   INNER JOIN user u ON a.id_user = u.id_user
   LEFT JOIN warga w ON u.id_user = w.id_user
   WHERE MONTH(a.tanggal) = ? 
   AND YEAR(a.tanggal) = ?
   ORDER BY a.tanggal ASC, u.nama ASC"
);

$stmt_jadwal->bind_param("ii", $bulan, $tahun);

if (!$stmt_jadwal) {
  die("Error database: " . htmlspecialchars($conn->error));
}

$stmt_jadwal->execute();
$result_jadwal = $stmt_jadwal->get_result();

$jadwal_lengkap = [];
while ($row = $result_jadwal->fetch_assoc()) {
  $jadwal_lengkap[] = [
    'tanggal' => $row['tanggal'],
    'nama' => $row['nama'],
    'id_user' => $row['id_user'],
    'hari_ronda' => ucfirst($row['hari_ronda']),
    'hadir' => 'Hadir' // Karena data dari tabel absensi = pasti hadir
  ];
}
$stmt_jadwal->close();

// Hitung statistik
$total_jadwal = count($jadwal_lengkap);
$total_hadir = $total_jadwal; // Semua data hadir karena dari tabel absensi
$total_tidak_hadir = 0;

// Pagination
$total_pages = ($total_jadwal > 0) ? ceil($total_jadwal / $limit) : 1;

if ($page > $total_pages) {
  $page = $total_pages;
  $offset = ($page - 1) * $limit;
}

$jadwal_paged = array_slice($jadwal_lengkap, $offset, $limit);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Riwayat Absensi Warga</title>
  <link rel="icon" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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

    .card-custom {
      background: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .badge-hadir {
      background-color: #22c55e;
      color: white;
      padding: 0.25rem 0.75rem;
      border-radius: 0.375rem;
      font-size: 0.875rem;
    }

    .badge-tidak-hadir {
      background-color: #ef4444;
      color: white;
      padding: 0.25rem 0.75rem;
      border-radius: 0.375rem;
      font-size: 0.875rem;
    }

    .badge-hari {
      background-color: #3b82f6;
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
    }

    #tableContainer {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
      margin-bottom: 0;
    }
    

    /* Alert info style seperti gambar */
    .alert-info-custom {
      background-color: #D1ECF1;
      border: 1px solid #BEE5EB;
      border-radius: 0.375rem;
      padding: 1rem;
      color: #0C5460;
    }

    /* Empty state */
    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: #6B7280;
    }

    .empty-state i {
      font-size: 4rem;
      color: #9CA3AF;
      margin-bottom: 1rem;
    }

    /* CSS untuk PDF */
    body.pdf-mode .no-print {
      display: none !important;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0;
      }
    }
  </style>
</head>

<body class="d-flex">
  <?php include('sidebar.php'); ?>

  <div class="content w-100">
    <?php include 'header.php' ?>

    <div class="card-custom">
      <!-- Header dengan Judul dan Tombol -->
      <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-4">
        <h3 class="fw-bold fs-5 mb-0">Riwayat Absensi Warga</h3>
        <div class="d-flex gap-2 flex-wrap align-items-center no-print">
          <!-- Filter Bulan & Tahun -->
          <form method="GET" action="" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="page" value="1">
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

          <!-- Tombol -->
          <a href="jadwal_ronda.php" class="btn btn-success">Lihat Jadwal Ronda</a>
          <button id="downloadPDF" class="btn btn-success d-flex align-items-center gap-2">
            <i class="fa-solid fa-file-pdf"></i> Download PDF
          </button>
        </div>
      </div>

      <!-- Alert Info -->
      <div class="alert-info-custom mb-4">
        Menampilkan data <strong><?= htmlspecialchars($nama_bulan[$bulan]) ?> <?= htmlspecialchars($tahun) ?></strong> - Total: <strong><?= $total_jadwal ?></strong> record
        <br>
        <small>
          <i class="bi bi-info-circle"></i> Hanya menampilkan warga yang terjadwal ronda di bulan ini
        </small>
      </div>

      <!-- Tabel -->
      <div id="tableContainer" class="table-responsive">  
        <div id="laporanPDF">
          
          <!-- Header untuk PDF (hidden di web) -->
          <div class="text-center mb-3" style="display: none;" id="pdfHeader">
            <h4 class="fw-bold">RIWAYAT ABSENSI WARGA</h4>
            <h6>Periode: <?= htmlspecialchars($nama_bulan[$bulan]) ?> <?= htmlspecialchars($tahun) ?></h6>
            <hr>
          </div>

          <table class="table table-bordered align-middle table-striped table-hover align-midle">
            <thead class="table-light">
              <tr>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($jadwal_paged) > 0): ?>
                <?php
                $hari_map_indo = [
                  'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
                  'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
                ];
                
                foreach ($jadwal_paged as $data):
                  $hari_eng = date('l', strtotime($data['tanggal']));
                  $hari_indonesia = $hari_map_indo[$hari_eng];
                  $tanggal_indonesia = date('d-m-Y', strtotime($data['tanggal']));
                ?>
                  <tr>
                    <td><?= htmlspecialchars($tanggal_indonesia) ?></td>
                    <td><?= htmlspecialchars($data['nama']) ?></td>
                    <td>
                      <?php if ($data['hadir'] == 'Hadir'): ?>
                        <span class="badge-hadir">Hadir</span>
                      <?php else: ?>
                        <span class="badge-tidak-hadir">Tidak Hadir</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="3">
                    <div class="empty-state">
                      <i class="fa-solid fa-inbox"></i>
                      <p class="mb-0">Tidak ada jadwal ronda untuk bulan ini</p>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>

          <!-- Footer untuk PDF (hidden di web) -->
          <div class="text-end mt-3" style="display: none;" id="pdfFooter">
            <small>Dicetak pada: <?= date('d-m-Y H:i:s') ?></small>
          </div>

        </div>
      </div>

      <div class="d-flex justify-content-end mt-3 pe-3">
  <nav>
    <ul class="pagination mb-0">

      <!-- Tombol Previous -->
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" 
           href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&page=<?= $page - 1 ?>">
          Previous
        </a>
      </li>

      <!-- Nomor Halaman -->
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
          <a class="page-link" 
             href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&page=<?= $i ?>">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>

      <!-- Tombol Next -->
      <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" 
           href="?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&page=<?= $page + 1 ?>">
          Next
        </a>
      </li>

    </ul>
  </nav>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Script Download PDF -->
  <script>
    document.getElementById("downloadPDF")?.addEventListener("click", () => {
      const element = document.getElementById("laporanPDF");
      const bulan = "<?= htmlspecialchars($nama_bulan[$bulan]) ?>";
      const tahun = "<?= htmlspecialchars($tahun) ?>";
      
      document.getElementById("pdfHeader").style.display = "block";
      document.getElementById("pdfFooter").style.display = "block";
      document.body.classList.add("pdf-mode");

      const opt = {
        margin: [10, 10, 15, 10],  // tambah jarak supaya tidak kepotong
        filename: `riwayat-absensi-${bulan}-${tahun}.pdf`,
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { 
          scale: 4,              // dinaikkan supaya border tidak pecah
          useCORS: true,
        },
        jsPDF: { 
          unit: 'mm',
          format: 'a4',
          orientation: 'portrait'
        }
      };

      html2pdf().set(opt).from(element).save().then(() => {
        document.body.classList.remove("pdf-mode");
        document.getElementById("pdfHeader").style.display = "none";
        document.getElementById("pdfFooter").style.display = "none";
      });
    });
  </script>

  <!-- Script Profile Dropdown -->
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