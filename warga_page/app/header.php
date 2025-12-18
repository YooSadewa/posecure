<style>
  .video-style {
    top: 1/2;
    left: 1/2;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    translate: -1/2;
    object-fit: cover;
    z-index: 0;
  }

  .profile-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .table-container {
    overflow-y: auto;
    max-height: 165px;
  }

  .caption-header {
    font-size: medium;
    margin-bottom: 0;
  }

  #caretIcon.rotate-180 {
    transform: rotate(180deg);
  }

  .label-width-password {
    width: 250px !important;
    justify-content: left;
  }

  @media screen and (max-width: 768px) {
    .jumbotron {
      height: 150px;
    }

    .header-text {
      font-size: 36px;
    }

    .caption-header {
      font-size: x-small;
    }
  }
</style>

<?php
$hari_ronda = $_SESSION['hari_ronda'];
$nama = $_SESSION['nama'];
$id_warga = $_SESSION['id_user'];
$id_alamat = $_SESSION['id_alamat'];
include '../../koneksi_database.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'warga') {
  header("location: login_page.php");
  exit;
}

$query = mysqli_query($conn, "SELECT * FROM user JOIN warga ON user.id_user = warga.id_user WHERE user.nama = '$nama'");
$header_query = mysqli_query($conn, "SELECT * FROM user JOIN warga ON user.id_user = warga.id_user WHERE warga.hari_ronda = '$hari_ronda'");
if (!empty($hari_ronda)) {
  $anggota_query = mysqli_query($conn, "
        SELECT * FROM user 
        JOIN warga ON user.id_user = warga.id_user 
        JOIN alamat ON warga.id_alamat = alamat.id_alamat
        WHERE warga.hari_ronda = '$hari_ronda' AND alamat.id_alamat = '$id_alamat'
    ");
} else {
  $anggota_query = false;
}

$alamat_query = mysqli_query($conn, "SELECT * FROM alamat JOIN warga ON alamat.id_alamat = warga.id_alamat WHERE warga.id_user = '$id_warga'");
$data = mysqli_fetch_assoc($header_query);
$user_data = mysqli_fetch_assoc($query);
$alamat_data = mysqli_fetch_assoc($alamat_query);
$hari = !empty($data['hari_ronda']) ? $data['hari_ronda'] : "Hari Ronda Belum Ditentukan :(";
?>

<section class="container-fluid px-4 pb-0 pb-md-3" style="padding-top: 5em">
  <div class="row">
    <div
      class="col-md-9 col-12 p-0 d-flex align-items-stretch position-relative bg-dark"
      style="overflow: hidden; border-radius: 12px">
      <video
        autoplay
        muted
        loop
        playsinline
        class="position-absolute video-style">
        <source src="../../assets/vid/header_vid.mp4" type="video/mp4" />
        Your browser doesn't support the video tag.
      </video>
      <div
        class="position-absolute"
        style="background-color: black; z-index: 10"></div>
      <div
        class="position-relative d-flex flex-column align-items-center justify-content-center text-white font-weight-bold jumbotron"
        style="z-index: 20; width: 100%; font-size: 40px; gap: 2px">
        <h1 style="font-weight: bold" class="header-text text-center fs-1">
          SISTEM JADWAL KEAMANAN LINGKUNGAN
        </h1>
        <p class="font-weight-normal text-center caption-header">
          - Dengan Jadwal yang Tertata, Lingkungan Kita Lebih Terjaga -
        </p>
      </div>
    </div>
    <div
      class="col-md-3 col-12 px-0 pt-3 pt-md-0 ps-md-2"
      style="min-height: 200px">
      <section class="profile-container">
        <div class="dropdown d-none d-md-flex">
          <button
            id="profileButton"
            class="btn btn-light w-100 border d-flex align-items-center gap-3 px-4 py-2 rounded-3 shadow-sm fw-semibold"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="
              transition: background-color 0.2s;
              justify-content: space-between;
            "
            onmouseover="this.style.backgroundColor='#f8f9fa'"
            onmouseout="this.style.backgroundColor='white'">
            <img
              src="../../assets/warga_img/profile_img/<?= $user_data['foto'] ?>"
              alt="Foto Profil"
              class="rounded-circle object-fit-cover border border-secondary"
              style="width: 28px; height: 28px" />
            <span class="text-dark text-truncate d-inline-block text-nowrap" style="max-width: 170px;">
              <?= $_SESSION['nama'] ?>
            </span>
            <i
              id="caretIcon"
              class="fa-solid fa-caret-down text-secondary ms-auto"
              style="transition: transform 0.2s"></i>
          </button>

          <div
            id="profileDropdown"
            class="dropdown-menu w-100 mt-2 rounded-3 shadow border border-light py-2"
            style="min-width: 12rem; z-index: 1000">
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
              href="../process/logout_proses.php"
              class="d-flex align-items-center gap-2 px-4 py-2 text-decoration-none text-danger"
              style="transition: background-color 0.2s"
              onmouseover="this.style.backgroundColor='#f8f9fa'"
              onmouseout="this.style.backgroundColor='transparent'">
              <i class="fa-solid fa-door-open me-2"></i> Logout
            </a>
          </div>
        </div>
        <div class="table-container">
          <table class="table table-striped border" style="margin: 0">
            <thead style="position: sticky; top: -1px;">
              <tr>
                <th class="text-center" style="text-transform: capitalize;"><?= $hari ?></th>
              </tr>
            </thead>
            <tbody style="font-weight: 500">
              <?php if ($anggota_query && mysqli_num_rows($anggota_query) > 0): ?>
                <?php while ($row = mysqli_fetch_array($anggota_query)) : ?>
                  <tr>
                    <td class="<?= ($row['nama'] == $nama) ? 'bg-warning' : '' ?>">
                      <?= $row['nama'] ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</section>

<div
  class="modal fade"
  id="profileModal"
  tabindex="-1"
  aria-labelledby="profileModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="profileModalLabel">
          Detail Profile
        </h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 pb-4">
        <div class="text-center mb-2">
          <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center position-relative overflow-hidden"
            style="width:100px;height:100px;">

            <?php if ($user_data['foto']) { ?>
              <img id="previewImage"
                src="../../assets/warga_img/profile_img/<?= $user_data['foto'] ?>"
                class="w-100 h-100 object-fit-cover rounded-circle">
            <?php } else { ?>
              <i id="defaultIcon" class="fas fa-user fa-3x text-secondary"></i>
            <?php } ?>

          </div>

          <form action="../process/update_foto_profil.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_user" value="<?= $_SESSION['id_user'] ?>">

            <a href="#" onclick="document.getElementById('editFotoInput').click(); return false;"
              class="d-block mt-2 text-decoration-none text-primary fw-medium">
              Edit Foto Profil
            </a>

            <input type="file" id="editFotoInput" name="foto_baru" accept="image/*" hidden
              onchange="this.form.submit()">
          </form>
        </div>

        <div class="text-center mb-4">
          <h5 class="fw-bold mb-0 fs-2"><?= $user_data['nama'] ?></h5>
          <small class="text-muted"><?= $user_data['no_kk'] ?></small>
        </div>

        <hr />

        <div class="row mb-3">
          <div class="col-2 fw-bold">No. Telp</div>
          <div class="col-auto">:</div>
          <div class="col"><?= $user_data['no_telp'] ?></div>
        </div>

        <div class="row mb-4">
          <div class="col-2 fw-bold">Alamat</div>
          <div class="col-auto">:</div>
          <div class="col capitalize">
            <?= $alamat_data['kecamatan'] . ', ' . $alamat_data['kelurahan'] . ', RT ' . $alamat_data['no_rt'] . ' / RW ' . $alamat_data['no_rw'] . ', ' . $alamat_data['blok_rumah'] ?>
          </div>
        </div>

        <!-- Tombol Ganti Password -->
        <div class="text-end">
          <button
            type="button"
            class="btn btn-primary px-4 py-2"
            data-bs-toggle="modal"
            data-bs-target="#gantiPasswordModal"
            style="background-color: #1e3a8a; border: none">
            <i class="fas fa-key me-2"></i>Ganti Password
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div
  class="modal fade modal-fullscreen-md-down"
  id="gantiPasswordModal"
  tabindex="-1"
  aria-labelledby="gantiPasswordLabel"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="../process/ganti_password_proses.php" method="POST">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="gantiPasswordLabel">Ganti Password</h1>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
        </div>
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
              <button class="btn btn-outline-light border text-secondary" type="button" onclick="togglePassword('passwordLama', this)">
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
              <button class="btn btn-outline-light border text-secondary" type="button" onclick="togglePassword('passwordBaru', this)">
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
                name="password_konfirmasi"
                required />
              <button class="btn btn-outline-light border text-secondary" type="button" onclick="togglePassword('passwordKonfir', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Close
          </button>
          <button type="submit" class="btn btn-primary" name="submit">
            Edit Password
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  const profileButton = document.getElementById("profileButton");
  const profileDropdown = document.getElementById("profileDropdown");
  const caretIcon = document.getElementById("caretIcon");

  if (profileButton && profileDropdown && caretIcon) {
    profileButton.addEventListener("click", (e) => {
      e.stopPropagation();
      profileDropdown.classList.toggle("d-absolute");
      caretIcon.classList.toggle("rotate-180");
    });

    window.addEventListener("click", (e) => {
      if (
        !profileButton.contains(e.target) &&
        !profileDropdown.contains(e.target)
      ) {
        profileDropdown.classList.add("d-absolute");
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

  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const modalParam = urlParams.get('modal');

    if (modalParam === 'profile') {
      const profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
      profileModal.show();
    } else if (modalParam === 'gantiPassword') {
      const gantiPasswordModal = new bootstrap.Modal(document.getElementById('gantiPasswordModal'));
      gantiPasswordModal.show();
    }
  });
</script>
</div>