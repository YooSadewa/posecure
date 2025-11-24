<?php
session_start();

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
            <?php for ($i = 1; $i <= 9; $i++): ?>
              <tr>
                <td>Dummy</td>
                <td>Dummy</td>
                <td>Dummy</td>
                <td>Dummy</td>
                <td>Dummy</td>
                <td>Dummy</td>
                <td>Dummy</td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end mt-3 pe-3">
        <nav>
          <ul class="pagination mb-0">
            <li class="page-item"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </div>

  <!-- Modal Tambah -->
  <div class="modal fade modal-fullscreen-md-down" id="modal_tambah" tabindex="-1" aria-labelledby="modal_tambah_label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="modal_tambah_label">Daftar Jadwal Ronda Warga</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <form>
            <div class="input-group mb-3">
              <label class="input-group-text" style="width: 120px;">Nama <span class="text-danger ms-1">*</span></label>
              <input type="text" class="form-control" id="nama" placeholder="Masukkan nama warga">
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" style="width: 120px;">Pilih Hari <span class="text-danger ms-1">*</span></label>
              <select id="hari" class="form-select">
                <option selected disabled>Pilih hari...</option>
                <option>Senin</option>
                <option>Selasa</option>
                <option>Rabu</option>
                <option>Kamis</option>
                <option>Jumat</option>
                <option>Sabtu</option>
                <option>Minggu</option>
              </select>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-primary">Daftarkan</button>
        </div>
      </div>
    </div>
  </div>

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
</body>

</html>