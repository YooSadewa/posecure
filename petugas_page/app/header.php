<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div class="row align-items-center mb-3 mb-md-0">
  <div class="col-md-6">
    <h1 class="header-title">
      Welcome Back,<br />
      Petugas Dummy 👋
    </h1>
    <p class="header-subtitle mt-2">Dashboard</p>
  </div>

  <div class="col-md-6 text-md-end">
    <div class="position-relative d-inline-block">
      <button
        id="profileButton"
        class="btn btn-light d-flex align-items-center gap-3 px-4 py-2 rounded-3 shadow-sm fw-semibold w-100 w-md-auto"
        style="transition: background-color 0.2s"
        onmouseover="this.style.backgroundColor='#f8f9fa'"
        onmouseout="this.style.backgroundColor='white'"
      >
        <img
          src="../../assets/img/bg_layangan.jpeg"
          alt="Foto Profil"
          class="rounded-circle object-fit-cover border border-secondary"
          style="width: 28px; height: 28px"
        />
        <span class="text-dark">Petugas Dummy</span>
        <i
          id="caretIcon"
          class="fa-solid fa-caret-down text-secondary ms-auto"
          style="transition: transform 0.2s"
        ></i>
      </button>

      <div
        id="profileDropdown"
        class="position-absolute w-100 mt-2 bg-white rounded-3 shadow border border-light py-2 d-none"
        style="min-width: 12rem; z-index: 999"
      >
        <a
          href="#"
          class="d-flex align-items-center gap-2 px-4 py-2 text-decoration-none text-secondary"
          style="transition: background-color 0.2s"
          onmouseover="this.style.backgroundColor='#f8f9fa'"
          onmouseout="this.style.backgroundColor='transparent'"
          data-bs-toggle="modal"
          data-bs-target="#profileModal"
        >
          <i class="fa-solid fa-user me-2"></i> Detail Profil
        </a>
        <hr class="my-1 border-secondary opacity-25" />
        <a
          href="#"
          class="d-flex align-items-center gap-2 px-4 py-2 text-decoration-none text-danger"
          style="transition: background-color 0.2s"
          onmouseover="this.style.backgroundColor='#f8f9fa'"
          onmouseout="this.style.backgroundColor='transparent'"
        >
          <i class="fa-solid fa-door-open me-2"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Profile -->
<div
  class="modal fade"
  id="profileModal"
  tabindex="-1"
  aria-labelledby="profileModalLabel"
  aria-hidden="true"
>
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
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body px-4 pb-4">
        <div class="text-center mb-2">
          <div
            class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center position-relative overflow-hidden"
            style="width: 100px; height: 100px"
          >
            <img
              id="previewImage"
              src=""
              alt="Foto Profil"
              class="w-100 h-100 object-fit-cover rounded-circle"
              style="display: none"
            />
            <i id="defaultIcon" class="fas fa-user fa-3x text-secondary"></i>
          </div>
          <a
            href="#"
            class="d-block mt-2 text-decoration-none text-primary fw-medium"
            onclick="document.getElementById('uploadFoto').click(); return false;"
          >
            Edit Foto Profil
          </a>
          <input
            type="file"
            id="uploadFoto"
            accept="image/*"
            style="display: none"
            onchange="previewFoto(event)"
          />
        </div>

        <div class="text-center mb-4">
          <h5 class="fw-bold mb-0 fs-2">Petugas Keamanan</h5>
          <small class="text-muted">Username</small>
        </div>

        <hr />

        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">No. Telp</div>
          <div class="col-auto">:</div>
          <div class="col">08xx-xxxx-xxxx</div>
        </div>

        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">No. RT</div>
          <div class="col-auto">:</div>
          <div class="col">002</div>
        </div>

        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">No. RW</div>
          <div class="col-auto">:</div>
          <div class="col">003</div>
        </div>

        <div class="row mb-3">
          <div class="col-4 col-md-3 fw-bold">Kelurahan</div>
          <div class="col-auto">:</div>
          <div class="col">Baloi Permai</div>
        </div>

        <div class="row mb-4">
          <div class="col-4 col-md-3 fw-bold">Kecamatan</div>
          <div class="col-auto">:</div>
          <div class="col">Batam Kota</div>
        </div>

        <!-- Tombol Ganti Password -->
        <div class="text-end">
          <button
            type="button"
            class="btn btn-success px-4 py-2"
            data-bs-toggle="modal"
            data-bs-target="#gantiPasswordModal"
          >
            <i class="fas fa-key me-2"></i>Ganti Password
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ganti Password -->
<div
  class="modal fade modal-fullscreen-md-down"
  id="gantiPasswordModal"
  tabindex="-1"
  aria-labelledby="gantiPasswordLabel"
  aria-hidden="true"
>
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
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        <form action="">
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
                required
              />
              <button
                class="btn btn-outline-light border text-secondary"
                type="button"
                onclick="togglePassword('passwordLama', this)"
              >
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
                required
              />
              <button
                class="btn btn-outline-light border text-secondary"
                type="button"
                onclick="togglePassword('passwordBaru', this)"
              >
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
                required
              />
              <button
                class="btn btn-outline-light border text-secondary"
                type="button"
                onclick="togglePassword('passwordKonfir', this)"
              >
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" class="btn btn-primary">Edit Password</button>
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

  /* Pastikan wrapper mengikuti lebar button */
  @media (max-width: 767.98px) {
    .col-md-6 .position-relative {
      display: block !important;
      width: 100%;
    }
  }
</style>

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

  // Preview foto profil
  function previewFoto(event) {
    const input = event.target;
    const file = input.files[0];
    const preview = document.getElementById("previewImage");
    const icon = document.getElementById("defaultIcon");

    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = "block";
        icon.style.display = "none";
      };
      reader.readAsDataURL(file);
    }
  }

  // Toggle password visibility
  function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector("i");

    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("bi-eye");
      icon.classList.add("bi-eye-slash");
    } else {
      input.type = "password";
      icon.classList.remove("bi-eye-slash");
      icon.classList.add("bi-eye");
    }
  }
</script>