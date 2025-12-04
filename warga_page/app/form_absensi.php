<?php
session_start();
include '../../koneksi_database.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'warga') {
    header("location: login_page.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$nama = $_SESSION['nama'];
$no_telp = $_SESSION['no_telp'];
$hari_ronda = $_SESSION['hari_ronda'];

$tanggal_hari_ini = date('Y-m-d');
$stmt_cek = $conn->prepare("SELECT id_absensi FROM absensi WHERE id_user = ? AND tanggal = ?");
$stmt_cek->bind_param("ss", $id_user, $tanggal_hari_ini);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows > 0) {
    $_SESSION['alert'] = [
        'type' => 'info',
        'title' => 'Sudah Absen',
        'message' => 'Anda sudah melakukan absensi hari ini!'
    ];
    $stmt_cek->close();
    $conn->close();
    header("location: dashboard_page.php");
    exit;
}

$query_warga = mysqli_query($conn, "SELECT w.*, u.nama 
                                    FROM warga w 
                                    JOIN user u ON w.id_user = u.id_user 
                                    WHERE w.id_user = '$id_user'");
$data_warga = mysqli_fetch_assoc($query_warga);
$blok_rumah = $data_warga['blok_rumah'];
$no_kk = $data_warga['no_kk'];

$hariIni = date('l');  

$hariMap = [
    'Monday'    => 'senin',
    'Tuesday'   => 'selasa',
    'Wednesday' => 'rabu',
    'Thursday'  => 'kamis',
    'Friday'    => 'jumat',
    'Saturday'  => 'sabtu',
    'Sunday'    => 'minggu'
];

$hariIniIndo = $hariMap[$hariIni];  

if (strtolower($hariIniIndo) !== strtolower($hari_ronda)) {
    $_SESSION['alert'] = [
        'type' => 'warning',
        'title' => 'Bukan Jadwal Anda',
        'message' => 'Hari ini (' . ucfirst($hariIniIndo) . ') bukan jadwal ronda Anda. Jadwal Anda: ' . ucfirst($hari_ronda)
    ];
    header("location: dashboard_page.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />
    <link rel="icon" href="../../assets/img/logo.png">
    <title>Form Absensi</title>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
    <link href="../../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <style>
        .required {
            color: red;
            font-weight: bold;
        }

        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #e5e4e2;
        }

        #video {
            transform: scaleX(-1);
        }

        #photo {
            transform: scaleX(-1);
        }

        .dropdown-menu {
            z-index: 9999 !important;
        }

        .modal {
            z-index: 1055;
        }

        .modal-backdrop {
            z-index: 1050;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php' ?>
    <?php include 'header.php' ?>
    <div class="container-fluid pb-2">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header text-white text-center py-4 rounded-top-4" style="background-color: #1E3A8A;">
                        <h4 class="mb-2 fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Form Absensi
                        </h4>
                        <p class="mb-0 opacity-75 small">Silakan lengkapi data absensi Anda</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form id="formAbsensi" action="../process/form_absensi_proses.php" method="POST">
                            <input type="hidden" name="id_user" value="<?php echo $id_user; ?>">
                            <input type="hidden" name="foto_data" id="fotoData">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="namakepalakeluarga" class="form-label fw-semibold">
                                        <i class="bi bi-person-fill text-primary me-1"></i>
                                        Nama Kepala Keluarga
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg bg-secondary-subtle" id="namakepalakeluarga" value="<?php echo htmlspecialchars($nama); ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label for="tanggal" class="form-label fw-semibold">
                                        <i class="bi bi-calendar-event text-primary me-1"></i>
                                        Tanggal
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control form-control-lg bg-secondary-subtle" id="tanggal" name="tanggal" readonly value="<?php echo date('Y-m-d'); ?>">
                                </div>

                                <div class="col-12">
                                    <label for="blokrumah" class="form-label fw-semibold">
                                        <i class="bi bi-house-fill text-primary me-1"></i>
                                        Blok Rumah
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg bg-secondary-subtle" id="blokrumah" name="blokrumah" value="<?php echo htmlspecialchars($blok_rumah); ?>" readonly>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-camera-fill text-primary me-1"></i>
                                        Bukti Foto Kehadiran
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div id="photoPreview" class="d-none mb-3">
                                        <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light">
                                            <img id="photoThumb" class="rounded" width="100" height="75" style="object-fit: cover;" alt="Preview foto">
                                            <div class="flex-grow-1">
                                                <p class="mb-1 fw-semibold text-success">
                                                    <i class="bi bi-check-circle-fill me-1"></i>
                                                    Foto berhasil diambil
                                                </p>
                                                <small class="text-muted">Klik "Foto Ulang" untuk mengambil foto baru</small>
                                            </div>
                                            <button type="button" class="btn btn-warning btn-sm" id="retakeBtn">
                                                <i class="bi bi-arrow-repeat me-1"></i>
                                                Foto Ulang
                                            </button>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary btn-lg w-100" id="openCamera">
                                        <i class="bi bi-camera-video me-2"></i>
                                        Buka Kamera
                                    </button>

                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Klik tombol di atas untuk mengambil foto secara real-time
                                    </div>

                                    <canvas id="canvas" class="d-none"></canvas>
                                </div>
                            </div>

                            <div class="alert alert-info d-flex align-items-center mt-4" role="alert">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <small>Pastikan semua data yang diisi sudah benar sebelum mengirim.</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="bi bi-send-fill me-2"></i>
                                    Kirim Absensi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php' ?>

    <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header text-white" style="background-color: #1E3A8A;">
                    <h5 class="modal-title">
                        <i class="bi bi-camera-video me-2"></i>
                        Ambil Foto Kehadiran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" id="closeCamera"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-4x3 bg-dark">
                        <video id="video" class="w-100 h-100" autoplay playsinline></video>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success btn-lg px-3" id="captureBtn">
                        <i class="bi bi-camera-fill me-2"></i>
                        Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-5">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">Tidak Dapat Melakukan Absensi</h5>
                    <p class="text-muted mb-4">Dikarenakan Anda tidak melakukan foto bukti absensi!</p>
                    <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK, Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3 mb-2">Absensi Berhasil!</h5>
                    <p class="text-muted mb-4">Data absensi Anda telah dikirim dengan sukses!</p>
                    <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const formAbsensi = document.getElementById("formAbsensi");
            const errorModal = new bootstrap.Modal(document.getElementById("errorModal"));
            const successModal = new bootstrap.Modal(document.getElementById("successModal"));
            const cameraModal = new bootstrap.Modal(document.getElementById("cameraModal"));

            const video = document.getElementById("video");
            const canvas = document.getElementById("canvas");
            const openCameraBtn = document.getElementById("openCamera");
            const captureBtn = document.getElementById("captureBtn");
            const closeCameraBtn = document.getElementById("closeCamera");
            const retakeBtn = document.getElementById("retakeBtn");
            const photoPreview = document.getElementById("photoPreview");
            const photoThumb = document.getElementById("photoThumb");

            let stream = null;
            let photoTaken = false;

            openCameraBtn.addEventListener("click", async function() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "user"
                        },
                        audio: false
                    });
                    video.srcObject = stream;
                    cameraModal.show();
                } catch (err) {
                    alert("Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.");
                    console.error("Error accessing camera:", err);
                }
            });

            captureBtn.addEventListener("click", function() {
                const context = canvas.getContext("2d");
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                context.save();
                context.scale(-1, 1);
                context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                context.restore();

                const photoData = canvas.toDataURL("image/png");
                photoThumb.src = photoData;

                document.getElementById("fotoData").value = photoData;

                photoPreview.classList.remove("d-none");
                openCameraBtn.classList.add("d-none");

                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                cameraModal.hide();

                photoTaken = true;
            });

            closeCameraBtn.addEventListener("click", function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                cameraModal.hide();
            });

            retakeBtn.addEventListener("click", async function() {
                photoPreview.classList.add("d-none");
                openCameraBtn.classList.remove("d-none");
                photoTaken = false;
                document.getElementById("fotoData").value = "";
                openCameraBtn.click();
            });

           formAbsensi.addEventListener("submit", function(e) {
                e.preventDefault();

                if (!photoTaken) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Dapat Melakukan Absensi',
                        text: 'Anda belum mengambil foto bukti absensi!',
                        confirmButtonText: 'OK, Mengerti',
                        confirmButtonColor: '#1E3A8A'
                    });
                } else {
                    this.submit();
                }
            });

            // Cleanup yang diperbaiki
            const cameraModalEl = document.getElementById("cameraModal");

            cameraModalEl.addEventListener("hidden.bs.modal", function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }

                // Force cleanup
                setTimeout(() => {
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');

                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                }, 100);
            });

            // Tambahan: pastikan dropdown berfungsi setelah modal ditutup
            cameraModalEl.addEventListener("hide.bs.modal", function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            });
        });
    </script>
    <?php if (isset($_SESSION['alert'])) : ?>
       <script>
        Swal.fire({
            icon: '<?= $_SESSION['alert']['type'] ?>',
            title: '<?= $_SESSION['alert']['title'] ?>',
            text: '<?= $_SESSION['alert']['message'] ?>',
            confirmButtonColor: '#1E3A8A'
        });
    </script>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
</body>

</html>