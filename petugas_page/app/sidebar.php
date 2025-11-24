<?php
// sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<button class="btn btn-success position-fixed top-0 end-0 m-3" 
        type="button" 
        id="sidebarToggle"
        style="z-index: 1000;">
    <i class="fa-solid fa-bars"></i>
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<nav class="sidebar d-flex flex-column justify-content-between position-fixed top-0 start-0 h-100 text-white py-4" id="sidebar">

    <div class="px-2">
        <button class="btn btn-close btn-close-white position-absolute top-0 end-0 m-3" 
                id="sidebarClose"></button>

        <div class="text-center mb-4">
            <img src="../../assets/img/white_logo.png" alt="Logo" class="mx-auto" style="width: 130px;">
            <hr class="border-white my-4 mx-3 opacity-100">
        </div>
        <div class="d-flex flex-column gap-1">
            <a href="dashboard.php"
                class="sidebar-link d-flex align-items-center gap-3 py-3 px-4 rounded-3 text-decoration-none <?= $current_page == 'Dashboard.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-house fs-5" style="width: 20px;"></i>
                Dashboard
            </a>
            <a href="daftar_akun_warga.php"
                class="sidebar-link d-flex align-items-center gap-3 py-3 px-4 rounded-3 text-decoration-none <?= $current_page == 'Daftar_Akun_Warga.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-users fs-5" style="width: 20px;"></i>
                Daftar Akun Warga
            </a>
            <a href="jadwal_ronda.php"
                class="sidebar-link d-flex align-items-center gap-3 py-3 px-4 rounded-3 text-decoration-none <?= $current_page == 'Jadwal_Ronda.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-days fs-5" style="width: 20px;"></i>
                Jadwal Ronda
            </a>
            <a href="laporan_insiden.php"
                class="sidebar-link d-flex align-items-center gap-3 py-3 px-4 rounded-3 text-decoration-none <?= $current_page == 'Laporan_Insiden.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-circle-exclamation fs-5" style="width: 20px;"></i>
                Laporan Insiden
            </a>
        </div>
    </div>
</nav>

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    .sidebar {
        width: 250px;
        background-color: #049055;
        z-index: 1050;
        transition: transform 0.3s ease-in-out;
    }

    .sidebar-link {
        color: rgba(255, 255, 255, 0.9);
        font-size: 15px;
        font-weight: 500;
        transition: background-color 0.2s, font-weight 0.2s;
    }

    .sidebar-link:hover {
        background-color: #1b4d3a;
        color: white;
    }

    .sidebar-link.active {
        background-color: #143829;
        font-weight: 600;
        color: white;
    }

    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }

    .sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    /* Desktop */
    @media (min-width: 1025px) {
        .sidebar {
            transform: translateX(0) !important;
        }

        #sidebarToggle {
            display: none;
        }
        
        #sidebarClose {
            display: none;
        }
    }

    /* Mobile */
    @media (max-width: 1024.98px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        body.sidebar-open {
            overflow: hidden;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const body = document.body;

        function openSidebar() {
            sidebar.classList.add('show');
            sidebarOverlay.classList.add('show');
            body.classList.add('sidebar-open');
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            body.classList.remove('sidebar-open');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', openSidebar);
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking on a link (mobile only)
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />