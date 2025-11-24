<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <script src="../../assets/bootstrap/js/bootstrap.min.js"></script>
    <style>
        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(../../assets/img/bg_login.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            background-color: black;
            min-height: 100vh;
        }

        .form-container {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark w-100" style="background-color: #1E3A8A;">
        <div class="container-fluid ps-md-5">
            <img src="../../assets/img/white_logo.png" alt="Logo poSecure" class="navbar-brand" style="width: 8.4em; padding-left: 26.4px;">
        </div>
    </nav>

    <div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 56px);">
        <div class="row w-100 mx-0 px-3 px-md-0">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
                <div class="text-center text-white mb-4">
                    <h1 class="fw-bold mb-2 fs-md-3">SISTEM JADWAL KEAMANAN LINGKUNGAN</h1>
                    <p class="fs-6">- Dengan Jadwal yang Tertata, Lingkungan Kita Lebih Terjaga -</p>
                </div>

                <div class="form-container p-4 p-md-5 mx-auto" style="max-width: 500px;">
                    <form class="text-white" action="../process/login_process.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Blok Rumah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Contoh: A415 (Nama Blok, Nomor Rumah)">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password">
                        </div>
                        <div class="d-flex gap-2 justify-content-md-start justify-content-center">
                            <button type="submit" class="btn btn-primary px-4">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>