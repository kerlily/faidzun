<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?= base_url('logo/logo.png') ?>" type="image/png">
    <title>E-Learning | SMP 10 Pekalongan</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url() ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url() ?>css/sb-admin-2.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <!-- Lightbox2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.4/dist/css/lightbox.min.css" rel="stylesheet" />

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        a {
            text-decoration: none;
        }

        a:hover {
            opacity: 0.8;
        }
    </style>

</head>

<body id="page-top">
    <?php $role = session()->get('role'); ?>
    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php $uri = service('uri'); ?>
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('/dashboard') ?>" style="background-color: white;">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-chalkboard-teacher fa-2x text-primary" style="transform: rotate(-15deg);"></i>
                </div>

                <div class="sidebar-brand-text mx-3">
                    <span class="fw-bold text-dark">E-</span><span class="fw-bold text-primary">Learning</span>
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Dashboard -->
            <li class="nav-item menu-item <?= $uri->getSegment(1) === 'dashboard' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('/dashboard') ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <?php if (in_array($role, ['kepala sekolah', 'operator'])): ?>
                <!-- Heading -->
                <div class="sidebar-heading">Manajemen Data</div>

                <!-- Manajemen Dropdown -->
                <li class="nav-item menu-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseManajemen" aria-expanded="true" aria-controls="collapseManajemen">
                        <i class="fas fa-fw fa-users-cog"></i>
                        <span>Manajemen</span>
                    </a>
                    <div id="collapseManajemen" class="collapse" aria-labelledby="headingManajemen" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item <?= $uri->getSegment(1) === 'guru' ? 'active' : '' ?>" href="<?= site_url('/guru') ?>">Guru</a>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'kelas' ? 'active' : '' ?>" href="<?= site_url('/kelas') ?>">Kelas</a>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'siswa' ? 'active' : '' ?>" href="<?= site_url('/siswa') ?>">Siswa</a>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'mapel' ? 'active' : '' ?>" href="<?= site_url('/mapel') ?>">Mata Pelajaran</a>
                        </div>
                    </div>
                </li>
            <?php endif; ?>

            <!-- Heading -->
            <div class="sidebar-heading">Aktivitas</div>

            <!-- Aktivitas Dropdown -->
            <li class="nav-item menu-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAktivitas" aria-expanded="true" aria-controls="collapseAktivitas">
                    <i class="fas fa-fw fa-book-open"></i>
                    <span>Pembelajaran</span>
                </a>
                <div id="collapseAktivitas" class="collapse" aria-labelledby="headingAktivitas" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <?php if (in_array($role, ['kepala sekolah', 'operator', 'guru', 'guru bk'])): ?>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'jadwal-mengajar' ? 'active' : '' ?>" href="<?= site_url('/jadwal-mengajar') ?>">Jadwal Mengajar</a>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'penugasan' ? 'active' : '' ?>" href="<?= site_url('/penugasan') ?>">Penugasan</a>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'absensi' ? 'active' : '' ?>" href="<?= site_url('/absensi') ?>">Absensi</a>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'riwayat-absensi' ? 'active' : '' ?>" href="<?= site_url('/riwayat-absensi') ?>">Riwayat Absensi</a>
                        <?php endif; ?>
                        <?php if (in_array($role, ['siswa'])): ?>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'tugas-saya' ? 'active' : '' ?>" href="<?= site_url('/tugas-saya') ?>">Tugas Saya</a>
                            <a class="collapse-item <?= $uri->getSegment(1) === 'riwayat-absensi' ? 'active' : '' ?>" href="<?= site_url('/riwayat-absensi') ?>">Riwayat Absensi</a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">Lainnya</div>

            <!-- Profil -->
            <li class="nav-item menu-item <?= $uri->getSegment(1) === 'profil' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('/profil') ?>">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Profil</span>
                </a>
            </li>

            <!-- Panduan Penggunaan -->
            <li class="nav-item menu-item <?= $uri->getSegment(1) === 'panduan' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('/panduan') ?>">
                    <i class="fas fa-fw fa-info-circle"></i>
                    <span>Panduan</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" autocomplete="off">
                        <div class="input-group">
                            <input type="text" id="sidebarSearchInput" class="form-control bg-light border-0 small"
                                placeholder="Cari Menu..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Container untuk hasil pencarian -->
                        <div id="searchResults" class="list-group position-absolute mt-1" style="z-index: 9999; max-height: 300px; overflow-y: auto; width: 100%; display: none;"></div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <i class="fas fa-user fa-fw text-gray-600"></i>
                                <span class="ml-2 d-none d-lg-inline text-gray-600 small">
                                    <?= session()->get('nama'); ?><br>
                                    <small class="text-muted"><?= session()->get('role'); ?></small>
                                </span>
                                <i class="fas fa-chevron-down fa-sm ml-1 text-gray-600"></i>
                            </a>

                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <!-- Logout -->
                                <a class="dropdown-item" href="<?= site_url('/logout') ?>">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>

                        </li>
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <?= $this->renderSection('topBar') ?>
                    </div>
                    <div class="col">
                        <!-- sweet alert -->
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php if (in_array($role, ['kepala sekolah', 'operator', 'guru'])): ?>
                <!-- Chat Toggle Button -->
                <button id="chatToggle" class="btn btn-primary rounded-circle shadow"
                    style="position: fixed; bottom: 70px; right: 20px; z-index: 1050; width: 60px; height: 60px;">
                    <i class="fas fa-comment-dots"></i>
                </button>

                <!-- Chat Popup Box -->
                <div id="chatBox" class="card shadow-lg border-0" style="position: fixed; bottom: 90px; right: 20px; width: 320px; display: none; z-index: 1051; border-radius: 12px;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                        <strong class="mb-0">Chat Guru BK</strong>
                        <button type="button" class="close text-white" id="chatClose" style="font-size: 1.2rem;">&times;</button>
                    </div>
                    <div class="card-body p-3" style="max-height: 330px; overflow-y: auto;">
                        <?php if (!empty($guruBKList)) : ?>
                            <?php foreach ($guruBKList as $guru) : ?>
                                <div class="media mb-3 border rounded p-2 align-items-center" style="transition: 0.3s;">
                                    <img src="<?= base_url('profil/' . ($guru['foto'] ?? 'user.png')) ?>"
                                        alt="<?= esc($guru['nama']) ?>"
                                        class="rounded-circle mr-3"
                                        style="width: 50px; height: 50px; object-fit: cover;">
                                    <div class="media-body">
                                        <h6 class="mt-0 mb-1"><?= esc($guru['nama']) ?></h6>
                                        <?php
                                        $nomor = preg_replace('/[^0-9]/', '', $guru['no_hp']);
                                        if (strpos($nomor, '08') === 0) {
                                            $nomor = '62' . substr($nomor, 1);
                                        }

                                        date_default_timezone_set('Asia/Jakarta');
                                        $jam = (int) date('H');

                                        if ($jam >= 5 && $jam < 11) {
                                            $salam = 'pagi';
                                        } elseif ($jam >= 11 && $jam < 15) {
                                            $salam = 'siang';
                                        } elseif ($jam >= 15 && $jam < 18) {
                                            $salam = 'sore';
                                        } else {
                                            $salam = 'malam';
                                        }

                                        $namaGuru = esc($guru['nama']);
                                        $pesan = urlencode("Selamat $salam, $namaGuru");
                                        ?>
                                        <a href="https://wa.me/<?= $nomor ?>?text=<?= $pesan ?>"
                                            class="text-success text-decoration-none small d-inline-flex align-items-center"
                                            target="_blank">
                                            <i class="fab fa-whatsapp mr-1"></i> <?= esc($guru['no_hp']) ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <p class="text-muted text-center mb-0">Tidak ada guru BK tersedia.</p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endif; ?>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Dibuat oleh &copy; HILAL <b><a href="https://iwima.ac.id/" target="_blank">IWIMA</a></b> 2025</span>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- ==================== SEMUA SCRIPT DIKUMPULKAN DI BAWAH ==================== -->
    
    <!-- jQuery (WAJIB PALING PERTAMA) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="<?= base_url() ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery Easing -->
    <script src="<?= base_url() ?>vendor/jquery-easing/jquery.easing.min.js"></script>
    
    <!-- SB Admin 2 -->
    <script src="<?= base_url() ?>js/sb-admin-2.min.js"></script>

    <!-- DataTables JS (setelah jQuery) -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Lightbox2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.4/dist/js/lightbox.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- ==================== SCRIPT CUSTOM ==================== -->
    <script>
        // DataTables initialization - hanya jika elemen #dataTable ada
        $(document).ready(function() {
            if ($('#dataTable').length) {
                $('#dataTable').DataTable({
                    responsive: true
                });
            }
        });

        // Menu list untuk pencarian
        const userRole = "<?= session()->get('role'); ?>";

        const allMenus = {
            'kepala sekolah': [
                { name: "Dashboard", url: "<?= site_url('/dashboard') ?>" },
                { name: "Manajemen Guru", url: "<?= site_url('/guru') ?>" },
                { name: "Manajemen Siswa", url: "<?= site_url('/siswa') ?>" },
                { name: "Mata Pelajaran", url: "<?= site_url('/mapel') ?>" },
                { name: "Jadwal Mengajar", url: "<?= site_url('/jadwal-mengajar') ?>" },
                { name: "Absensi", url: "<?= site_url('/absensi') ?>" },
                { name: "Penugasan", url: "<?= site_url('/penugasan') ?>" },
                { name: "Tugas Saya", url: "<?= site_url('/tugas-saya') ?>" },
                { name: "Riwayat Absensi", url: "<?= site_url('/riwayat-absensi') ?>" },
                { name: "Profil", url: "<?= site_url('/profil') ?>" },
                { name: "Panduan", url: "<?= site_url('/panduan') ?>" }
            ],
            'operator': [
                { name: "Dashboard", url: "<?= site_url('/dashboard') ?>" },
                { name: "Manajemen Guru", url: "<?= site_url('/guru') ?>" },
                { name: "Manajemen Siswa", url: "<?= site_url('/siswa') ?>" },
                { name: "Mata Pelajaran", url: "<?= site_url('/mapel') ?>" },
                { name: "Jadwal Mengajar", url: "<?= site_url('/jadwal-mengajar') ?>" },
                { name: "Absensi", url: "<?= site_url('/absensi') ?>" },
                { name: "Penugasan", url: "<?= site_url('/penugasan') ?>" },
                { name: "Tugas Saya", url: "<?= site_url('/tugas-saya') ?>" },
                { name: "Pengumpulan Tugas", url: "<?= site_url('/tugas-saya') ?>" },
                { name: "Riwayat Absensi", url: "<?= site_url('/riwayat-absensi') ?>" },
                { name: "Profil", url: "<?= site_url('/profil') ?>" },
                { name: "Panduan", url: "<?= site_url('/panduan') ?>" }
            ],
            'guru': [
                { name: "Dashboard", url: "<?= site_url('/dashboard') ?>" },
                { name: "Penugasan", url: "<?= site_url('/penugasan') ?>" },
                { name: "Absensi", url: "<?= site_url('/absensi') ?>" },
                { name: "Riwayat Absensi", url: "<?= site_url('/riwayat-absensi') ?>" },
                { name: "Riwayat Absensi", url: "<?= site_url('/riwayat-absensi') ?>" },
                { name: "Profil", url: "<?= site_url('/profil') ?>" },
                { name: "Panduan", url: "<?= site_url('/panduan') ?>" }
            ],
            'guru bk': [
                { name: "Dashboard", url: "<?= site_url('/dashboard') ?>" },
                { name: "Penugasan", url: "<?= site_url('/penugasan') ?>" },
                { name: "Riwayat Absensi", url: "<?= site_url('/riwayat-absensi') ?>" },
                { name: "Absensi", url: "<?= site_url('/absensi') ?>" },
                { name: "Riwayat Absensi", url: "<?= site_url('/riwayat-absensi') ?>" },
                { name: "Profil", url: "<?= site_url('/profil') ?>" },
                { name: "Panduan", url: "<?= site_url('/panduan') ?>" }
            ],
            'siswa': [
                { name: "Dashboard", url: "<?= site_url('/dashboard') ?>" },
                { name: "Tugas Saya", url: "<?= site_url('/tugas-saya') ?>" },
                { name: "Riwayat Absensi", url: "<?= site_url('/riwayat-absensi') ?>" },
                { name: "Profil", url: "<?= site_url('/profil') ?>" },
                { name: "Panduan", url: "<?= site_url('/panduan') ?>" }
            ]
        };

        const menuList = allMenus[userRole.toLowerCase()] || [];

        // Pencarian menu
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('sidebarSearchInput');
            const searchResults = document.getElementById('searchResults');

            if (searchInput && searchResults) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    searchResults.innerHTML = '';

                    if (query.length > 0) {
                        const filtered = menuList.filter(menu =>
                            menu.name.toLowerCase().includes(query)
                        );

                        if (filtered.length > 0) {
                            filtered.forEach(menu => {
                                const item = document.createElement('a');
                                item.href = menu.url;
                                item.className = 'list-group-item list-group-item-action';
                                item.textContent = menu.name;
                                searchResults.appendChild(item);
                            });
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="list-group-item text-muted">Menu tidak ditemukan</div>';
                            searchResults.style.display = 'block';
                        }
                    } else {
                        searchResults.style.display = 'none';
                    }
                });

                document.addEventListener('click', function(event) {
                    if (searchResults && !searchResults.contains(event.target) && event.target !== searchInput) {
                        searchResults.style.display = 'none';
                    }
                });
            }
        });

        // Chat toggle
        document.addEventListener('DOMContentLoaded', function() {
            const chatToggle = document.getElementById('chatToggle');
            const chatBox = document.getElementById('chatBox');
            const chatClose = document.getElementById('chatClose');

            if (chatToggle && chatBox) {
                chatToggle.addEventListener('click', function() {
                    chatBox.style.display = chatBox.style.display === 'none' ? 'block' : 'none';
                });
            }

            if (chatClose && chatBox) {
                chatClose.addEventListener('click', function() {
                    chatBox.style.display = 'none';
                });
            }
        });

        // ==================== CHART INITIALIZATION DENGAN PENGECEKAN ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah chart-area canvas ada
            const areaCanvas = document.getElementById('myAreaChart');
            if (areaCanvas && typeof Chart !== 'undefined') {
                const ctx = areaCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                        datasets: [{
                            label: 'Aktivitas',
                            data: [0, 10, 5, 15, 20, 30, 25, 40, 35, 50, 45, 60],
                            backgroundColor: 'rgba(78, 115, 223, 0.2)',
                            borderColor: 'rgba(78, 115, 223, 1)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }

            // Cek apakah chart-pie canvas ada
            const pieCanvas = document.getElementById('myPieChart');
            if (pieCanvas && typeof Chart !== 'undefined') {
                const ctx = pieCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
                        datasets: [{
                            data: [70, 10, 10, 10],
                            backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'],
                            hoverBackgroundColor: ['#2e59d9', '#17a673', '#dda20a', '#c93a2b'],
                            hoverBorderColor: 'rgba(234, 236, 244, 1)'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        });
    </script>

</body>

</html>