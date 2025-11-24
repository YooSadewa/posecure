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
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Laporan Insiden</title>
  <link rel="icon" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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

    .card-custom {
      background: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

    <!-- Filter dan tombol PDF -->
    <div class="card-custom">
      <div class="d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center mb-4">
        <h3 class="fw-bold fs-5 mb-0">Riwayat Absensi Warga</h3>
        <div class="d-flex gap-2 flex-wrap align-items-center">
          <select class="form-select w-auto">
            <option>Januari</option>
            <option>Februari</option>
            <option>Maret</option>
          </select>
          <select class="form-select w-auto">
            <option>2025</option>
            <option>2024</option>
            <option>2023</option>
          </select>
          <div class="d-flex gap-2">
            <a href="jadwal_ronda.php" class="btn btn-success">Lihat Jadwal Ronda</a>
            <button id="downloadPDF" class="btn btn-success fw-semibold d-flex align-items-center gap-2">
              <i class="fa-solid fa-file-pdf"></i> Download PDF
            </button>
          </div>
        </div>
      </div>

      <!-- Tabel -->
      <div id="tableContainer" class="table-responsive mt-4">
        <table class="table table-bordered table-hover align-middle table-striped table-hover">
          <thead class="table-light">
            <tr>
              <th scope="col">Tanggal</th>
              <th scope="col">Nama</th>
              <th scope="col">Keterangan</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <tr>
                <td>01-01-2025</td>
                <td>Dummy</td>
                <td>Hadir/Tidak Hadir</td>
              </tr>
            <?php endfor; ?>
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <tr>
                <td>02-01-2025</td>
                <td>Dummy</td>
                <td>Hadir/Tidak Hadir</td>
              </tr>
            <?php endfor; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
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

  <!-- Script Dropdown Profile -->
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>