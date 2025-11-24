<?php
$pesan_username = "";
$pesan_password = "";
$value_username = "";

if (isset($_GET['pesan'])) {
  // mengambil value inputan username dari user sebelumnya
  if (isset($_GET['value_username'])) {
    $value_username = htmlspecialchars($_GET['value_username']);

    // pesan validasi
  }
  if ($_GET['pesan'] === "username_wajib_isi") {
    $pesan_username = "Username wajib diisi";
  }
  if ($_GET['pesan'] === "password_wajib_isi") {
    $pesan_password = "Password wajib diisi";
  }
  if ($_GET['pesan'] === "pengguna_tidak_ditemukan") {
    $pesan_username = "Username tidak ditemukan";
  }
  if ($_GET['pesan'] === "password_salah") {
    $pesan_password = "Password Salah. Silahkan Coba lagi";
  }
  if ($_GET['pesan'] === "belum_login") {
    $pesan_username = "Silahkan melakukan login ke akun Anda terlebih dahulu";
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Page</title>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous" />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
    rel="stylesheet" />

  <style>
    body {
      font-family: "Poppins", sans-serif;
    }

    .login-container {
      min-height: 100vh;
    }

    .card {
      border-radius: 0;
    }

    .login-image {
      object-fit: cover;
      height: 100%;
      width: 100%;
    }
  </style>
</head>

<body class="bg-success">
  <div class="container">
    <div class="d-flex flex-column pt-5 align-items-center login-container">
      <div class="text-center text-dark mb-4">
        <img
          src="..\..\assets\img\white_logo.png"
          alt="Logo poSecure"
          class="mb-3 w-25 pb-4" />
        <h1 class="fw-semibold text-white pb-4">
          Petugas Keamanan & Admin Page
        </h1>
      </div>

      <div class="card shadow" style="max-width: 900px; width: 100%">
        <div class="row g-0">
          <div class="col-lg-6 d-none d-lg-block">
            <img
              src="..\..\assets\img\rumah.jpg"
              alt="Neighborhood houses"
              class="login-image" />
          </div>

          <div class="col-lg-6">
            <div class="card-body p-3 p-lg-5">
              <h3 class="fw-semibold mb-4">Login ke Akun Anda</h3>
              <form action="../process/login_process.php" method="POST">
                <div class="row mb-3">
                  <div class="col-sm-3">
                    <label for="username" class="col-form-label">Username</label>
                  </div>
                  <div class="col-sm-9 align">
                    <input
                      type="text"
                      class="form-control"
                      id="username"
                      name="username"
                      value="<?= $value_username; ?>"
                      placeholder="Masukkan Username" />
                    <div id="usernameHelp" class="form-text text-danger ms-1"><?= $pesan_username; ?></div>
                  </div>
                </div>
                <div class="row mb-5">
                  <div class="col-sm-3">
                    <label for="password" class="col-form-label">Password</label>
                  </div>
                  <div class="col-sm-9">
                    <input
                      type="password"
                      class="form-control"
                      id="password"
                      name="password"
                      placeholder="Masukkan Password" />
                    <div id="usernameHelp" class="form-text text-danger ms-1"><?= $pesan_password; ?></div>
                  </div>
                </div>

                <div class="d-flex justify-content-center mt-2 mt-lg-5">
                  <button type="submit" class="btn btn-primary px-4 fw-bold">
                    Masuk
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>