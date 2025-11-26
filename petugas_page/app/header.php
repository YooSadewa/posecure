<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include "../../koneksi_database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas_keamanan') {
  header("location: ../login/login_page.php?pesan=belum_login");
  exit;
}

$id_user = $_SESSION['id_user'];

// Deteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);
$page_titles = [
  'dashboard.php' => 'Dashboard',
  'daftar_akun_warga.php' => 'Daftar Akun Warga',
  'jadwal_ronda.php' => 'Jadwal Ronda',
  'jadwal_ronda_bulanan.php' => 'Riwayat Absensi Ronda',
  'laporan_insiden.php' => 'Laporan Insiden Keamanan',
];
$page_subtitle = isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'Dashboard';

$query = "
SELECT 
    user.id_user,
    user.nama,
    user.username,
    user.foto,
    user.no_telp,
    alamat.kecamatan,
    alamat.kelurahan,
    alamat.no_rt,
    alamat.no_rw
FROM user
INNER JOIN petugas_keamanan ON user.id_user = petugas_keamanan.id_user
INNER JOIN alamat ON petugas_keamanan.id_alamat = alamat.id_alamat
WHERE user.id_user = '$id_user'
";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$foto_profil = $data['foto']
  ? "data:image/jpeg;base64," . base64_encode($data['foto'])
  : "../../assets/img/default_user.png";
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- HEADER -->
<div class="row align-items-center mb-3 mb-md-0">
  <div class="col-md-6">
    <h1 class="header-title">
      Welcome Back,<br />
      <?= $data['nama'] ?> 👋
    </h1>
    <p class="header-subtitle mt-2"><?= $page_subtitle ?></p>
  </div>

  <div class="col-md-6 text-md-end">
    <div class="position-relative d-inline-block">
      <button id="profileButton" class="btn btn-light d-flex align-items-center gap-3 px-4 py-2 rounded-3 shadow-sm fw-semibold">
        <img src="<?= $foto_profil ?>" class="rounded-circle object-fit-cover border border-secondary" style="width:28px;height:28px;">
        <span class="text-dark"><?= $data['nama'] ?></span>
        <i id="caretIcon" class="fa-solid fa-caret-down text-secondary ms-auto"></i>
      </button>

      <div id="profileDropdown"
        class="position-absolute w-100 mt-2 bg-white rounded-3 shadow border border-light py-2 d-none"
        style="min-width:12rem; z-index:999;">
        <a href="#" class="d-flex align-items-center gap-2 px-4 py-2 text-decoration-none text-secondary"
          data-bs-toggle="modal" data-bs-target="#profileModal">
          <i class="fa-solid fa-user me-2"></i> Detail Profil
        </a>
        <hr class="my-1 border-secondary opacity-25">
        <a href="../process/logout_proses.php"
          class="d-flex align-items-center gap-2 px-4 py-2 text-decoration-none text-danger">
          <i class="fa-solid fa-door-open me-2"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<!-- MODAL PROFILE -->
<div class="modal fade" id="profileModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">Detail Profile</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body px-4 pb-4">

        <!-- FOTO PROFIL -->
        <div class="text-center mb-2">
          <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center position-relative overflow-hidden"
            style="width:100px;height:100px;">
            <img id="previewImage"
              src="<?= $data['foto'] ? 'data:image/jpeg;base64,' . base64_encode($data['foto']) : '' ?>"
              class="w-100 h-100 object-fit-cover rounded-circle"
              style="<?= $data['foto'] ? '' : 'display:none' ?>">

            <i id="defaultIcon" class="fas fa-user fa-3x text-secondary"
              style="<?= $data['foto'] ? 'display:none' : '' ?>"></i>
          </div>

          <!-- BUTTON EDIT FOTO PROFIL -->
          <form action="../process/update_foto_profil.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_user" value="<?= $data['id_user'] ?>">

            <a href="#" onclick="document.getElementById('editFotoInput').click(); return false;"
              class="d-block mt-2 text-decoration-none text-primary fw-medium">
              Edit Foto Profil
            </a>

            <input type="file" id="editFotoInput" name="foto_baru" accept="image/*" hidden onchange="this.form.submit()">
          </form>
        </div>

        <div class="text-center mb-4">
          <h5 class="fw-bold fs-2"><?= $data['nama'] ?></h5>
          <small class="text-muted"><?= $data['username'] ?></small>
        </div>

        <hr>

        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">No. Telp</div>
          <div class="col-auto">:</div>
          <div class="col"><?= $data['no_telp'] ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">No. RT</div>
          <div class="col-auto">:</div>
          <div class="col"><?= $data['no_rt'] ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">No. RW</div>
          <div class="col-auto">:</div>
          <div class="col"><?= $data['no_rw'] ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">Kelurahan</div>
          <div class="col-auto">:</div>
          <div class="col"><?= trim($data['kelurahan']) ?></div>
        </div>
        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">Kecamatan</div>
          <div class="col-auto">:</div>
          <div class="col"><?= trim($data['kecamatan']) ?></div>
        </div>

        <div class="text-end">
          <button class="btn btn-success px-4 py-2" data-bs-toggle="modal" data-bs-target="#gantiPasswordModal">
            <i class="fas fa-key me-2"></i>Ganti Password
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .header-title {
    font-size: 32px;
    font-weight: 700;
  }

  .header-subtitle {
    font-size: 18px;
    color: #555;
  }

  #caretIcon.rotate-180 {
    transform: rotate(180deg);
  }
</style>

<script>
  const profileButton = document.getElementById("profileButton");
  const profileDropdown = document.getElementById("profileDropdown");
  const caretIcon = document.getElementById("caretIcon");

  profileButton.addEventListener("click", e => {
    e.stopPropagation();
    profileDropdown.classList.toggle("d-none");
    caretIcon.classList.toggle("rotate-180");
  });

  window.addEventListener("click", e => {
    if (!profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
      profileDropdown.classList.add("d-none");
      caretIcon.classList.remove("rotate-180");
    }
  });
</script>