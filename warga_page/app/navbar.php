<?php $activePage = basename($_SERVER['PHP_SELF']); ?>

<nav class="navbar navbar-expand-md navbar-dark position-fixed w-100" style="z-index: 1000; background-color: #1E3A8A;">
    <div class="container">
        <img src="../../assets/img/white_logo.png" alt="Logo poSecure" class="navbar-brand" style="width: 7em;">
        <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse collapse" id="navbarNav">
            <div class="d-md-none border-bottom border-secondary pb-3 mb-3 mt-2">
                <div class="d-flex align-items-center gap-3 px-3">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <i class="fa-solid fa-circle-user" style="font-size: 50px; color: #1E3A8A;"></i>
                    </div>
                    <div class="text-white">
                        <h6 class="mb-0" style="font-weight: 600;"> <?= $_SESSION['nama'] ?> </h6>
                        <small class="text-white-50">Warga</small>
                    </div>
                </div>
            </div>

            <ul class="navbar-nav ms-auto d-flex flex-column flex-md-row gap-2 gap-md-3 align-items-start align-items-md-center">
                <li class="nav-item">
                    <a href="dashboard_page.php"
                        class="nav-link d-flex align-items-center <?= ($activePage == 'dashboard_page.php') ? 'active' : '' ?>"
                        style="gap: 8px;">
                        <i class="fa-solid fa-house"></i>
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="form_absensi.php"
                        class="nav-link d-flex align-items-center <?= ($activePage == 'form_absensi.php') ? 'active' : '' ?>"
                        style="gap: 8px;">
                        <i class="fa-solid fa-clipboard-user"></i>
                        Lakukan Absensi
                    </a>
                </li>

                <li class="nav-item">
                    <a href="laporan_insiden_page.php"
                        class="nav-link d-flex align-items-center <?= ($activePage == 'laporan_insiden_page.php') ? 'active' : '' ?>"
                        style="gap: 8px;">
                        <i class="fa-solid fa-shield-halved"></i>
                        Laporkan Insiden!
                    </a>
                </li>

                <li class="nav-item d-md-none mt-2 w-100">
                    <div class="border-top border-secondary pt-2">
                        <a href="#"
                            class="nav-link d-flex align-items-center"
                            style="gap: 8px;" data-bs-toggle="modal" data-bs-target="#profileModal">
                            <i class="fa-solid fa-user"></i>
                            Detail Profil
                        </a>
                    </div>
                </li>

                <li class="nav-item d-md-none w-100">
                    <a href="../process/logout_proses.php" class="nav-link d-flex align-items-center text-danger" style="gap: 8px;">
                        <i class="fa-solid fa-door-open"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>