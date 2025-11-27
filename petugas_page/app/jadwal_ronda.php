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

// Pagination config
$limit = 9;

// Halaman aktif
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$countQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM warga");
$countData = mysqli_fetch_assoc($countQuery);
$totalData = $countData['total'];

// Hitung total halaman
$totalPage = ($totalData > 0) ? ceil($totalData / $limit) : 1;


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
          <a href="jadwal_ronda_bulanan.php" class="btn btn-success px-3 py-2 flex-fill"> Lihat Riwayat Absensi</a>
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

            // Ambil data berdasarkan hari
            $data = [];
            foreach ($hariList as $h) {
              $query = mysqli_query($conn, "SELECT warga.hari_ronda, user.nama FROM warga JOIN user ON warga.id_user = user.id_user WHERE warga.hari_ronda='$h'");
              $isi = [];
              while ($row = mysqli_fetch_assoc($query)) {
                $isi[] = $row['nama'];
              }

              // jika tidak ada data, tampilkan "-"
              if (count($isi) == 0) {
                $isi[] = "-";
              }

              // simpan 9 baris
              for ($i = 0; $i < 9; $i++) {
                $data[$i][$h] = isset($isi[$i]) ? $isi[$i] : "-";
              }
            }
            ?>

          <tbody>
            <?php for ($i = 0; $i < 9; $i++): ?>
              <tr>
                <?php foreach ($hariList as $h): ?>
                  <td><?= $data[$i][$h] ?></td>
                <?php endforeach; ?>
              </tr>
            <?php endfor; ?>
          </tbody>

          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3 pe-3">
        <nav>
          <ul class="pagination mb-0">

            <!-- Tombol Previous -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">Previous</a>
            </li>

            <?php
            // Tentukan range angka
            $startPage = max(1, $page - 1);
            $endPage = min($totalPage, $page + 1);

            for ($i = $startPage; $i <= $endPage; $i++): ?>
              <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>

            <!-- Tombol Next -->
            <li class="page-item <?= ($page >= $totalPage) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= min($totalPage, $page + 1) ?>">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>

  <?php
  // ambil list warga sekali saja
  $query_warga = "SELECT id_user, nama FROM user WHERE role = 'warga' ORDER BY nama ASC";
  $result_warga = mysqli_query($conn, $query_warga);

  $warga_list = [];
  while ($w = mysqli_fetch_assoc($result_warga)) {
    $warga_list[] = $w;
  }

  // buat map nama(lowercase) => id_user untuk JS
  $warga_map = [];
  foreach ($warga_list as $w) {
    $warga_map[mb_strtolower($w['nama'])] = $w['id_user']; 
  }
  ?>

  <!-- Modal Tambah -->
  <form action="../process/jadwal_tambah_warga_proses.php" method="POST">
    <div class="modal fade" id="modal_tambah" tabindex="-1" aria-labelledby="modal_tambah_label" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal_tambah_label">Daftar Jadwal Ronda Warga</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <!-- Input Nama dengan Search -->
            <div class="input-group mb-3">
              <label for="nama_warga" class="input-group-text" style="width: 120px;">Nama <span class="text-danger">*</span></label>

              <input type="text" class="form-control" id="nama_warga" list="daftar_warga"
                placeholder="Ketik untuk mencari nama warga..." autocomplete="off" required>

              <datalist id="daftar_warga">
                <?php foreach ($warga_list as $w): ?>
                  <!-- sertakan data-id agar JS bisa ambil id_user -->
                  <option data-id="<?= htmlspecialchars($w['nama']) ?>" value="<?= htmlspecialchars($w['nama']) ?>"></option>
                <?php endforeach; ?>
              </datalist>

              <!-- Hidden input untuk id_user -->
              <input type="hidden" name="id_user" id="id_user">
              <small class="text-muted"></small>
            </div>

            <!-- Select Hari Ronda -->
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

    // Map nama(lowercase) -> id_user dari PHP (case-insensitive)
    const wargaMap = <?= json_encode($warga_map) ?>;

    inputNama.addEventListener("input", function() {
      const nama = this.value.trim().toLowerCase();

      if (wargaMap[nama]) {
        idUserHidden.value = wargaMap[nama];
      } else {
        idUserHidden.value = "";
      }
    });

    // Validasi sebelum submit (jaga kalau JS diaktifkan)
    document.querySelector('form[action="../process/jadwal_tambah_warga_proses.php"]').addEventListener('submit', function(e) {
      if (idUserHidden.value === "") {
        e.preventDefault();
        // gunakan SweetAlert jika sudah di-include
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
    // Dropdown profil
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