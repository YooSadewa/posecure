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

$id_alamat = $_SESSION['id_alamat'];

$limit = 9;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$countQuery = mysqli_query($conn, "
    SELECT COUNT(DISTINCT id_user) AS total 
    FROM warga 
    WHERE hari_ronda IS NOT NULL 
    AND hari_ronda != '' 
    AND id_alamat = '$id_alamat'
");
$countData = mysqli_fetch_assoc($countQuery);
$totalData = $countData['total'];

// Hitung total halaman
$totalPage = max(1, ceil($totalData / $limit));

$query_warga = "
  SELECT user.id_user, user.nama 
  FROM user
  JOIN warga ON user.id_user = warga.id_user
  WHERE user.role = 'warga'
  AND warga.id_alamat = '$id_alamat'
  ORDER BY user.nama ASC
";
$result_warga = mysqli_query($conn, $query_warga);

$warga_list = [];
$warga_map = [];

if ($result_warga && mysqli_num_rows($result_warga) > 0) {
  while ($row = mysqli_fetch_assoc($result_warga)) {
    $warga_list[] = $row;
    $warga_map[strtolower($row['nama'])] = $row['id_user'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jadwal Ronda Keamanan</title>
  <link rel="icon" href="../../assets/img/logo.png">
  <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
  <script src="../../assets/bootstrap/js/bootstrap.min.js"></script>
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
  </style>

</head>

<body class="d-flex">
  <?php include 'sidebar.php' ?>

  <div class="content w-100">
    <?php include 'header.php' ?>

    <div class="card-custom">
      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold fs-5 mb-0">Jadwal Ronda Keamanan</h3>
        <div class="d-flex justify-content-center gap-2">
          <button type="button" class="btn btn-success px-3 py-2 flex-fill" data-bs-toggle="modal" data-bs-target="#modal_tambah">
            <i class="fa-solid fa-plus me-1"></i> Tambahkan Warga
          </button>
          <a href="jadwal_ronda_bulanan.php?id_alamat=<?= urlencode($id_alamat) ?>" class="btn btn-success px-3 py-2 flex-fill">Lihat Riwayat Absensi</a>
        </div>
      </div>

      <!-- Tabel -->
      <div id="tableContainer" class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Senin</th>
              <th>Selasa</th>
              <th>Rabu</th>
              <th>Kamis</th>
              <th>Jumat</th>
              <th>Sabtu</th>
              <th>Minggu</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $hariList = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"];

            // Ambil data berdasarkan hari DENGAN LIMIT dan OFFSET (per-hari & per-id_alamat)
            $data = [];
            foreach ($hariList as $h) {
              $h_esc = mysqli_real_escape_string($conn, $h);

              $sql = "
              SELECT user.nama
              FROM warga
              JOIN user ON warga.id_user = user.id_user
              WHERE warga.hari_ronda = '$h_esc'
                AND warga.id_alamat = '$id_alamat'
              ORDER BY user.nama ASC
              LIMIT $limit OFFSET $start
            ";

              $queryNama = mysqli_query($conn, $sql);
              $isi = [];

              if ($queryNama) {
                while ($row = mysqli_fetch_assoc($queryNama)) {
                  $isi[] = $row['nama'];
                }
              }

              // jika tidak ada data, tampilkan "-"
              if (count($isi) == 0) {
                $isi[] = "-";
              }

              // simpan $limit baris untuk hari ini
              for ($i = 0; $i < $limit; $i++) {
                $data[$i][$h] = isset($isi[$i]) ? $isi[$i] : "-";
              }
            }
            ?>

            <?php for ($i = 0; $i < $limit; $i++): ?>
              <tr>
                <?php foreach ($hariList as $h): ?>
                  <td><?= htmlspecialchars($data[$i][$h]) ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-end mt-3 pe-3">
        <nav>
        <ul class="pagination mb-0">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= max(1, $page - 1) ?>&id_alamat=<?= urlencode($id_alamat) ?>">Previous</a>
            </li>

          <?php
            $startPage = max(1, $page - 1);
            $endPage = min($totalPage, $page + 1);

            for ($i = $startPage; $i <= $endPage; $i++): ?>
              <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&id_alamat=<?= urlencode($id_alamat) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>


            <li class="page-item <?= ($page >= $totalPage) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= min($totalPage, $page + 1) ?>&id_alamat=<?= urlencode($id_alamat) ?>">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
  

  <!-- Modal Tambah -->
  <form action="../process/jadwal_tambah_warga_proses.php" method="POST">
    <input type="hidden" name="id_alamat" value="<?= htmlspecialchars($id_alamat) ?>">
    <div class="modal fade" id="modal_tambah" tabindex="-1" aria-labelledby="modal_tambah_label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal_tambah_label">Daftar Jadwal Ronda Warga</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

            <div class="modal-body">
            <div class="input-group mb-3">
                <label for="nama_warga" class="input-group-text" style="width: 120px;">Nama <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_warga" list="daftar_warga" placeholder="Ketik untuk mencari nama warga..." autocomplete="off" required>
                <input type="hidden" name="id_user" id="id_user">
            </div>

            <datalist id="daftar_warga">
              <?php foreach ($warga_list as $w): ?>
                <option value="<?= htmlspecialchars($w['nama']) ?>"></option>
              <?php endforeach; ?>
            </datalist>
            <div class="input-group mb-3">
              <label for="hari_ronda" class="input-group-text" style="width: 120px;">Pilih Hari <span class="text-danger">*</span></label>
              <select name="hari_ronda" id="hari_ronda" class="form-select" required>
                <option selected disabled value="">Pilih hari...</option>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
                <option value="Minggu">Minggu</option>
              </select>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="submit">Daftarkan</button>
          </div>

        </div>
      </div>
    </div>
  </form>


  <script>
    const inputNama = document.getElementById("nama_warga");
    const idUserHidden = document.getElementById("id_user");

    const wargaMap = <?= json_encode($warga_map) ?>;

    inputNama.addEventListener("input", function() {
      const nama = this.value.trim().toLowerCase();

      if (wargaMap[nama]) {
        idUserHidden.value = wargaMap[nama];
      } else {
        idUserHidden.value = "";
      }
    });

    document.querySelector('form[action="../process/jadwal_tambah_warga_proses.php"]').addEventListener('submit', function(e) {
      if (idUserHidden.value === "") {
        e.preventDefault();
        if (window.Swal) {
          Swal.fire({
            icon: 'error',
            title: 'Nama tidak terdaftar!',
            text: 'Pilih nama dari daftar warga yang terdaftar.'
          });
        } else {
          alert('Nama tidak terdaftar! Pilih nama dari daftar warga yang terdaftar.');
        }
        inputNama.focus();
      }
    });
  </script>


  <script>
    const profileButton = document.getElementById("profileButton");
    const profileDropdown = document.getElementById("profileDropdown");
    const caretIcon = document.getElementById("caretIcon");

    if (profileButton && profileDropdown && caretIcon) {
      profileButton.addEventListener("click", () => {
        profileDropdown.classList.toggle("d-none");
        caretIcon.classList.toggle("rotate-180");
      });

      window.addEventListener("click", (e) => {
        if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
          profileDropdown.classList.add("d-none");
          caretIcon.classList.remove("rotate-180");
        }
      });
    }
  </script>

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