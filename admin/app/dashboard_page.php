<?php
// Sessions
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
  header("location: ../../petugas_page/app/login_page.php?pesan=belum_login");
  exit;
}

if ($_SESSION['role'] !== 'admin') {
  echo "Anda tidak memiliki akses ke halaman ini!";
  exit;
}

include '../../koneksi_database.php';
$query_total_petugas = "SELECT COUNT(*) as total FROM user WHERE role = 'petugas_keamanan'";
$total_petugas = mysqli_fetch_assoc(mysqli_query($conn, $query_total_petugas))['total'];

$jumlah_per_halaman = 10;
$halaman_aktif = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
$awal_data = ($jumlah_per_halaman * $halaman_aktif) - $jumlah_per_halaman;

$search_keyword = "";
$search_query = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
  $search_keyword = mysqli_escape_string($conn, $_GET["search"]);
  $search_query = "AND (
  a.kecamatan LIKE '%$search_keyword%' OR
  a.kelurahan LIKE '%$search_keyword%' OR
  u.username LIKE '%$search_keyword%'
  )";
}

$query_count_data = "SELECT COUNT(*) AS total
                      FROM user u
                      JOIN petugas_keamanan p ON u.id_user = p.id_user
                      JOIN alamat a ON p.id_alamat = a.id_alamat
                      WHERE u.role = 'petugas_keamanan' $search_query";
$total_data = mysqli_fetch_assoc(mysqli_query($conn, $query_count_data))['total'];

$jumlah_halaman = ceil($total_data / $jumlah_per_halaman);

$query_table = "SELECT u.id_user, u.username, u.nama, a.kecamatan, a.kelurahan, a.no_rt, a.no_rw, p.status_keaktifan FROM user u JOIN petugas_keamanan p ON u.id_user = p.id_user JOIN alamat a ON p.id_alamat = a.id_alamat WHERE u.role = 'petugas_keamanan' $search_query ORDER BY u.username ASC LIMIT $awal_data, $jumlah_per_halaman";
$result_table = mysqli_query($conn, $query_table);

$username = $_SESSION['username'];
$nama = $_SESSION['nama'];
$no_telp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT no_telp FROM user WHERE id_user = '{$_SESSION['id_user']}'"))['no_telp'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin</title>
  <link rel="icon" href="../../assets/img/logo.png" />
  <link
    rel="stylesheet"
    href="../../assets/bootstrap/css/bootstrap.min.css" />
  <script src="../../assets/bootstrap/js/bootstrap.min.js"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #e2e8f0;
    }

    .content {
      padding: 2rem;
      background-color: #e0e0e0;
      min-height: 100vh;
    }

    .card-custom {
      background: white;
      border-radius: 10px;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .header-title {
      font-size: 32px;
      font-weight: 700;
    }

    .caret-rotate {
      transition: transform 0.3s;
    }

    .caret-rotate.rotate-180 {
      transform: rotate(180deg);
    }

    .label-width {
      width: 140px !important;
      justify-content: left;
    }

    .label-width-password {
      width: 250px !important;
      justify-content: left;
    }

    .header-subtitle {
      font-size: 18px;
      color: #555;
    }

    #caretIcon.rotate-180 {
      transform: rotate(180deg);
    }
  </style>
</head>

<body>
  <div class="content w-100">
    <!-- Header -->
    <div
      class="d-flex flex-column flex-lg-row justify-content-start justify-content-lg-between align-items-start align-items-lg-center mb-4 row">
      <div class="col-md-6">
        <div>
          <img src="..\..\assets\img\blue_logo.png" alt="Logo" class="mb-4" />
        </div>
        <h1 class="header-title">Welcome Back, <?= $username; ?> 👋</h1>
      </div>
      <div class="col-md-6 text-md-start text-lg-end">
        <div class="position-relative d-block d-md-inline-block">
          <button id="profileButton" class="btn btn-light d-flex align-items-center gap-3 px-4 py-2 rounded-3 shadow-sm fw-semibold w-100 w-md-auto" ...>

            <?php if (isset($_SESSION['foto']) && !empty($_SESSION['foto'])) : ?>
              <img
                src="../../assets/admin_img/profile_img/<?= $_SESSION['foto']; ?>"
                alt="Foto Profil"
                class="rounded-circle object-fit-cover border border-secondary"
                style="width: 28px; height: 28px" />
            <?php else : ?>
              <div class="rounded-circle border border-secondary d-flex justify-content-center align-items-center bg-light" style="width: 28px; height: 28px">
                <i class="fas fa-user text-secondary" style="font-size: 14px;"></i>
              </div>
            <?php endif; ?>

            <span class="text-dark"><?= $username; ?></span>
            <i id="caretIcon" class="fa-solid fa-caret-down text-secondary ms-auto"></i>
          </button>

          <div
            id="profileDropdown"
            class="position-absolute w-100 mt-2 bg-white rounded-3 shadow border border-light py-2 d-none"
            style="min-width: 12rem; z-index: 999">
            <a
              href="#"
              class="d-flex align-items-center gap-2 px-4 py-2 text-decoration-none text-secondary"
              style="transition: background-color 0.2s"
              onmouseover="this.style.backgroundColor='#f8f9fa'"
              onmouseout="this.style.backgroundColor='transparent'"
              data-bs-toggle="modal"
              data-bs-target="#profileModal">
              <i class="fa-solid fa-user me-2"></i> Detail Profil
            </a>
            <hr class="my-1 border-secondary opacity-25" />
            <a
              href="#"
              onclick="konfirmasiLogout(event)"
              class="d-flex align-items-center gap-2 px-4 py-2 text-decoration-none text-danger"
              style="transition: background-color 0.2s"
              onmouseover="this.style.backgroundColor='#f8f9fa'"
              onmouseout="this.style.backgroundColor='transparent'">
              <i class="fa-solid fa-door-open me-2"></i> Logout
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade profile-modal" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-0">
            <h5 class="modal-title fw-bold" id="profileModalLabel">Detail Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form action="../process/update_profil_admin.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body px-4 pb-4">

              <div class="text-center mb-4">
                <?php if (isset($_SESSION['foto']) && !empty($_SESSION['foto'])) : ?>
                  <div class="profile-avatar mb-2 d-flex justify-content-center align-items-center mx-auto rounded-circle border border-secondary overflow-hidden" style="width: 100px; height: 100px; background-color: #f8f9fa;">
                    <img src="../../assets/admin_img/profile_img/<?= $_SESSION['foto']; ?>" alt="Foto Profil" class="w-100 h-100 object-fit-cover">
                  <?php else : ?>
                    <div class="profile-avatar">
                      <i class="fas fa-user fa-4x text-secondary"></i>
                    <?php endif; ?>

                    </div>

                    <a href="#" id="triggerEditFoto" class="d-block mt-2 text-decoration-none text-primary" onclick="tampilkanUpload(event)">
                      Edit Foto Profile
                    </a>

                    <div id="divUploadFoto" class="d-none mt-3">
                      <div class="input-group">
                        <input type="file" name="foto_admin" class="form-control form-control-sm" accept=".jpg, .jpeg, .png">
                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                      </div>
                      <small class="text-muted d-block text-start mt-1">Max: 2MB (JPG/PNG)</small>

                      <a href="#" class="text-danger small text-decoration-none mt-1 d-block" onclick="batalkanUpload(event)">
                        Batal
                      </a>
                    </div>
                  </div>

                  <div class="row text-center mb-4">
                    <div class="col-6">
                      <h6 class="fw-bold text-muted mb-1">Username</h6>
                      <p class="fs-5 fw-semibold mb-0"><?= $_SESSION['username']; ?></p>
                    </div>
                    <div class="col-6">
                      <h6 class="fw-bold text-muted mb-1">Nama</h6>
                      <p class="fs-5 fw-semibold mb-0"><?= $_SESSION['nama']; ?></p>
                    </div>
                  </div>

                  <div class="text-center mb-4">
                    <h6 class="fw-bold text-muted mb-1">Nomor Telp</h6>
                    <p class="fs-5 fw-semibold mb-0"><?= $no_telp; ?></p>
                  </div>

                  <div class="text-center">
                    <button type="button" class="btn btn-success px-4 py-2" data-bs-toggle="modal" data-bs-target="#gantiPasswordModal">
                      <i class="fas fa-key me-2"></i> Ganti Password
                    </button>
                  </div>

              </div>
          </form>

        </div>
      </div>
    </div>

    <!-- Modal ganti password -->
    <div
      class="modal fade modal-fullscreen-md-down"
      id="gantiPasswordModal"
      tabindex="-1"
      aria-labelledby="gantiPasswordLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="gantiPasswordLabel">
              Ganti Password
            </h1>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"></button>
          </div>

          <form action="../process/ganti_password_admin.php" method="POST">
            <div class="modal-body">
              <!-- Password Lama -->
              <div class="mb-3">
                <label for="passwordLama" class="form-label fw-semibold">
                  Password Lama <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input
                    type="password"
                    class="form-control"
                    id="passwordLama"
                    name="password_lama"
                    required />
                  <button
                    class="btn btn-outline-light border text-secondary"
                    type="button"
                    onclick="togglePassword('passwordLama', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <!-- Password Baru -->
              <div class="mb-3">
                <label for="passwordBaru" class="form-label fw-semibold">
                  Password Baru <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input
                    type="password"
                    class="form-control"
                    id="passwordBaru"
                    name="password_baru"
                    required />
                  <button
                    class="btn btn-outline-light border text-secondary"
                    type="button"
                    onclick="togglePassword('passwordBaru', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <!-- Konfirmasi Password Baru -->
              <div class="mb-3">
                <label for="passwordKonfir" class="form-label fw-semibold">
                  Konfirmasi Password Baru <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input
                    type="password"
                    class="form-control"
                    id="passwordKonfir"
                    name="password_konfir"
                    required />
                  <button
                    class="btn btn-outline-light border text-secondary"
                    type="button"
                    onclick="togglePassword('passwordKonfir', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Close
              </button>
              <button type="submit" class="btn btn-primary">Edit Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-12 col-md-8">
        <div class="card-custom h-100 d-flex flex-column">
          <div class="d-flex justify-content-between align-items-start">
            <h3 class="fw-bold fs-5 text-wrap">
              Total Akun Petugas Keamanan di Sistem Jadwal <br />Keamanan
              Lingkungan
            </h3>
            <i class="fa-solid fa-user-group fs-2 ms-3"></i>
          </div>

          <div class="d-flex align-items-center gap-2 mt-2">
            <p class="fs-2 fw-bold text-dark mb-0"><?= $total_petugas; ?></p>
            <p class="fs-2 fw-bold text-dark mb-0">Akun Terdaftar</p>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="card-custom h-100 d-flex flex-column">
          <div class="d-flex justify-content-between align-items-start">
            <h3 class="fw-bold fs-5 text-wrap">
              Cari Alamat Petugas <br />Keamanan
            </h3>
            <i class="fa-solid fa-search fs-2 ms-3"></i>
          </div>

          <div class="d-flex align-items-center gap-2 mt-2">
            <form action="" method="GET" class="w-100">
              <div class="input-group">
                <input
                  type="text"
                  name="search"
                  class="form-control"
                  placeholder="Kecamatan / Kelurahan..."
                  value="<?= htmlspecialchars($search_keyword); ?>"
                  aria-label="Cari..."
                  aria-describedby="cari" required />

                <button class="btn btn-primary" type="submit">
                  <i class="bi bi-arrow-right"></i>
                </button>

                <?php if (isset($_GET['search']) && $_GET['search'] != '') : ?>
                  <a href="dashboard_page.php" class="btn btn-danger" title="Hapus Pencarian">
                    <i class="fa-solid fa-xmark"></i>
                  </a>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Daftar Petugas Keamanan yang Terdaftar</h5>
          <button
            type="button"
            class="btn btn-success"
            data-bs-toggle="modal"
            data-bs-target="#modal_tambah">
            <i class="fas fa-plus"></i> Tambahkan Petugas
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped mb-0" id="tabel_warga">
            <thead>
              <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Kecamatan</th>
                <th>Kelurahan</th>
                <th>RW</th>
                <th>RT</th>
                <th>Status Keaktifan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if (mysqli_num_rows($result_table) > 0) {
                $no = $awal_data + 1;
                while ($row = mysqli_fetch_assoc($result_table)) {
              ?>
                  <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['kecamatan']); ?></td>
                    <td><?= htmlspecialchars($row['kelurahan']); ?></td>
                    <td><?= htmlspecialchars($row['no_rw']); ?></td>
                    <td><?= htmlspecialchars($row['no_rt']); ?></td>
                    <td>
                      <?php
                      $status = htmlspecialchars($row['status_keaktifan']);

                      if ($status === 'aktif') {
                        echo '<span class="badge bg-success px-5 py-2">Aktif</span>';
                      } elseif ($status === 'cuti') {
                        echo '<span class="badge bg-warning text-dark px-5 py-2">Cuti</span>';
                      } else {
                        echo '<span class="badge bg-secondary px-5 py-2">' . $status . '</span>';
                      }
                      ?>
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal_edit<?= $row['id_user']; ?>">
                          <i class="fas fa-edit"></i> Edit
                        </button>

                        <button class="btn btn-danger btn-sm" onclick="konfirmasiHapus('<?= $row['id_user']; ?>')">
                          <i class="fas fa-trash"></i> Hapus
                        </button>

                        <!-- hidden delete form -->
                        <form id="formHapus" action="../process/delete_petugas.php" method="POST" style="display: none;">
                          <input type="hidden" name="id_user" id="inputHapusId">
                        </form>
                      </div>
                    </td>
                  </tr>

                  <!-- Modal Edit Akun Petugas -->
                  <div
                    class="modal fade modal-fullscreen-md-down"
                    id="modal_edit<?= $row['id_user']; ?>"
                    tabindex="-1"
                    aria-labelledby="modal_tambah_label"
                    aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="modal_tambah_label">
                            Edit Petugas Keamanan
                          </h1>
                          <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        </div>
                        <form action="../process/update_petugas.php" method="POST">
                          <div class="modal-body">

                            <input type="hidden" name="id_user" value="<?= $row['id_user']; ?>">

                            <div class="input-group mb-3">
                              <span class="input-group-text label-width" style="min-width: 166px;" id="username">Username <span class="text-danger ms-1">*</span></span>
                              <input type="text" name="username" class="form-control" value="<?= $row['username']; ?>" aria-label="username" required />
                            </div>
                            <div class="input-group mb-3">
                              <span class="input-group-text label-width" style="min-width: 166px;" id="nama">Nama<span class="text-danger ms-1">*</span></span>
                              <input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" aria-label="nama" required />
                            </div>
                            <div class="input-group mb-3">
                              <span class="input-group-text label-width" style="min-width: 166px;" id="kecamatan">Kecamatan <span class="text-danger ms-1">*</span></span>
                              <input type="text" name="kecamatan" class="form-control" value="<?= $row['kecamatan']; ?>" aria-label="kecamatan" required />
                            </div>
                            <div class="input-group mb-3">
                              <span class="input-group-text label-width" style="min-width: 166px;" id="kelurahan">Kelurahan <span class="text-danger ms-1">*</span></span>
                              <input type="text" name="kelurahan" class="form-control" value="<?= $row['kelurahan']; ?>" aria-label="kelurahan" required />
                            </div>
                            <div class="input-group mb-3">
                              <span class="input-group-text label-width" style="min-width: 166px;" id="rw">RW <span class="text-danger ms-1">*</span></span>
                              <input type="text" name="rw" class="form-control" value="<?= $row['no_rw']; ?>" aria-label="rw" required />
                            </div>
                            <div class="input-group mb-3">
                              <span class="input-group-text label-width" style="min-width: 166px;" id="rt">RT <span class="text-danger ms-1">*</span></span>
                              <input type="text" name="rt" class="form-control" value="<?= $row['no_rt']; ?>" aria-label="rt" required />
                            </div>
                            <div class="input-group mb-3">
                              <span class="input-group-text" style="min-width: 160px;" id="status_keaktifan">Status Keaktifan <span class="text-danger ms-1">*</span></span>
                              <select class="form-select" name="status_keaktifan" aria-label="status_keaktifan" required>
                                <option value="aktif" <?= $row['status_keaktifan'] === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="cuti" <?= $row['status_keaktifan'] === 'cuti' ? 'selected' : ''; ?>>Cuti</option>
                              </select>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button
                              type="button"
                              class="btn btn-secondary"
                              data-bs-dismiss="modal">
                              Close
                            </button>
                            <button type="submit" class="btn btn-primary">Edit Data</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                <?php }
              } else {  ?>
                <tr>
                  <td colspan="7" class="text-center">Belum ada data petugas.</td>
                </tr>
              <?php
              } // Akhir If
              ?>

            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-end align-items-center mt-3 pe-3">
          <nav aria-label="Page navigation">
            <ul class="pagination mb-0">

              <?php if ($halaman_aktif > 1) : ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $halaman_aktif - 1; ?>&search=<?= $search_keyword; ?>" aria-label="Previous">
                    <span aria-hidden="true">Previous</span>
                  </a>
                </li>
              <?php else : ?>
                <li class="page-item disabled">
                  <span class="page-link">Previous</span>
                </li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
                <?php if ($i == $halaman_aktif) : ?>
                  <li class="page-item active"><span class="page-link"><?= $i; ?></span></li>
                <?php else : ?>
                  <li class="page-item">
                    <a class="page-link" href="?page=<?= $i; ?>&search=<?= $search_keyword; ?>"><?= $i; ?></a>
                  </li>
                <?php endif; ?>
              <?php endfor; ?>

              <?php if ($halaman_aktif < $jumlah_halaman) : ?>
                <li class="page-item">
                  <a class="page-link" href="?page=<?= $halaman_aktif + 1; ?>&search=<?= $search_keyword; ?>" aria-label="Next">
                    <span aria-hidden="true">Next</span>
                  </a>
                </li>
              <?php else : ?>
                <li class="page-item disabled">
                  <span class="page-link">Next</span>
                </li>
              <?php endif; ?>

            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Akun Petugas -->
  <div
    class="modal fade modal-fullscreen-md-down"
    id="modal_tambah"
    tabindex="-1"
    aria-labelledby="modal_tambah_label"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="modal_tambah_label">
            Daftarkan Petugas Keamanan
          </h1>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
        </div>
        <form action="../process/create_petugas.php" method="POST">
          <div class="modal-body">
            <div class="input-group mb-3">
              <span class="input-group-text label-width" id="username">Username <span class="text-danger ms-1">*</span></span>
              <input type="text" class="form-control" id="username" name="username" aria-label="username" required />
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text label-width" id="nama">Nama<span class="text-danger ms-1">*</span></span>
              <input type="text" class="form-control" id="nama" name="nama" aria-label="nama" required />
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text label-width" id="kecamatan">Kecamatan <span class="text-danger ms-1">*</span></span>
              <input type="text" class="form-control" id="kecamatan" name="kecamatan" aria-label="kecamatan" required />
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text label-width" id="kelurahan">Kelurahan <span class="text-danger ms-1">*</span></span>
              <input type="text" class="form-control" id="kelurahan" name="kelurahan" aria-label="kelurahan" required />
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text label-width" id="rw">RW <span class="text-danger ms-1">*</span></span>
              <input type="text" class="form-control" id="rw" name="rw" aria-label="rw" required />
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text label-width" id="rt">RT <span class="text-danger ms-1">*</span></span>
              <input type="text" class="form-control" id="rt" name="rt" aria-label="rt" required />
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal">
              Close
            </button>
            <button type="submit" class="btn btn-primary">Daftarkan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const profileButton = document.getElementById("profileButton");
    const profileDropdown = document.getElementById("profileDropdown");
    const caretIcon = document.getElementById("caretIcon");

    if (profileButton && profileDropdown && caretIcon) {
      profileButton.addEventListener("click", (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle("d-none");
        caretIcon.classList.toggle("rotate-180");
      });

      window.addEventListener("click", (e) => {
        if (
          !profileButton.contains(e.target) &&
          !profileDropdown.contains(e.target)
        ) {
          profileDropdown.classList.add("d-none");
          caretIcon.classList.remove("rotate-180");
        }
      });
    }

    function togglePassword(inputId, button) {
      const input = document.getElementById(inputId);
      const icon = button.querySelector('i');

      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    }

    // Menampilkan Input File
    function tampilkanUpload(e) {
      e.preventDefault(); // Mencegah link refresh halaman
      document.getElementById('triggerEditFoto').classList.add('d-none'); // Sembunyikan link
      document.getElementById('divUploadFoto').classList.remove('d-none'); // Munculkan input
    }

    // Membatalkan Upload
    function batalkanUpload(e) {
      e.preventDefault();
      document.getElementById('divUploadFoto').classList.add('d-none'); // Sembunyikan input
      document.getElementById('triggerEditFoto').classList.remove('d-none'); // Munculkan link

      // Reset nilai input file
      document.querySelector('input[name="foto_admin"]').value = '';
    }

    // Sweet alert
    <?php if (isset($_SESSION['alert'])): ?>
      Swal.fire({
        icon: '<?= $_SESSION['alert']['icon']; ?>',
        title: '<?= $_SESSION['alert']['title']; ?>',
        text: <?= isset($_SESSION['alert']['text']) ? "'{$_SESSION['alert']['text']}'" : 'undefined'; ?>,
        timer: <?= isset($_SESSION['alert']['timer']) ? $_SESSION['alert']['timer'] : 'undefined'; ?>,
        showConfirmButton: <?= isset($_SESSION['alert']['timer']) ? 'false' : 'true'; ?>
      });
      <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

    function konfirmasiLogout(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Yakin ingin logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '../process/logout.php';
        }
      });
    }

    function konfirmasiHapus(idUser) {
      event.preventDefault();

      Swal.fire({
        title: 'Hapus data petugas?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('inputHapusId').value = idUser;
          document.getElementById('formHapus').submit();
        }
      });
    }
  </script>

</body>

</html>