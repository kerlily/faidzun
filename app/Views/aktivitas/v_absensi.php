<?= $this->extend('v_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('swal_error')): ?>
        <script>
            Swal.fire({
                toast: true,
                icon: 'error',
                title: '<?= session()->getFlashdata('swal_error') ?>',
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        </script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('swal_success')): ?>
        <script>
            Swal.fire({
                toast: true,
                icon: 'success',
                title: '<?= session()->getFlashdata('swal_success') ?>',
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        </script>
    <?php endif; ?>
    
    <h4 class="mb-4">Daftar Absensi Mata Pelajaran</h4>

    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle"></i> Silakan lakukan absensi <strong>sebelum jam selesai</strong> untuk setiap mata pelajaran.
    </div>

    <form method="get" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="kelas">Filter Kelas:</label>
                <select name="kelas" id="kelas" class="form-control">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach ($filter_kelas as $kelas): ?>
                        <option value="<?= $kelas['id_kelas'] ?>" <?= ($selected_kelas == $kelas['id_kelas']) ? 'selected' : '' ?>>
                            <?= esc($kelas['nama_kelas']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="mapel">Filter Mapel:</label>
                <select name="mapel" id="mapel" class="form-control">
                    <option value="">-- Semua Mapel --</option>
                    <?php foreach ($filter_mapel as $mapel): ?>
                        <option value="<?= $mapel['kode_mapel'] ?>" <?= ($selected_mapel == $mapel['kode_mapel']) ? 'selected' : '' ?>>
                            <?= esc($mapel['nama_mapel']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary mt-2">Terapkan Filter</button>
                <a href="<?= site_url('absensi') ?>" class="btn btn-secondary ml-2">Reset</a>
                <a href="<?= site_url('/absensi/export') ?>" class="btn btn-success ml-2"><i class="bi bi-file-earmark-excel"></i>Export</a>
            </div>
        </div>
    </form>

    <?php
    date_default_timezone_set('Asia/Jakarta');
    $now = new DateTime();
    $hariInggris = strtolower($now->format('l'));
    $daftarHari = [
        'monday' => 'senin',
        'tuesday' => 'selasa',
        'wednesday' => 'rabu',
        'thursday' => 'kamis',
        'friday' => 'jumat',
        'saturday' => 'sabtu',
        'sunday' => 'minggu',
    ];

    $hariIni = $daftarHari[$hariInggris];
    
    // Ambil role user
    $role = session()->get('role');
    $isOperator = ($role === 'operator');
    ?>

    <div class="row">
        <?php if (empty($absensi)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-info-circle"></i> Tidak ada jadwal absensi yang tersedia.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($absensi as $item): ?>
                <?php $sudahAbsenList = $sudahAbsenList ?? []; ?>
                <?php $sudahAbsen = $sudahAbsenList[$item['id_jadwal']] ?? false; ?>
                <?php
                $jamMulai = DateTime::createFromFormat('H:i:s', $item['jam_mulai']);
                $jamSelesai = DateTime::createFromFormat('H:i:s', $item['jam_selesai']);
                $toleransiAkhir = (clone $jamSelesai)->modify('+15 minutes');

                $hariJadwal = strtolower($item['hari']);
                $status = '';

                if ($hariIni !== strtolower($hariJadwal)) {
                    $status = 'bukan_hari_ini';
                } elseif ($now < $jamMulai) {
                    $status = 'belum';
                } elseif ($now >= $jamMulai && $now <= $toleransiAkhir) {
                    $status = 'terbuka';
                } else {
                    $status = 'berakhir';
                }
                ?>

                <div class="col-md-6 mb-4">
                    <div class="card shadow border-left-<?= ($status === 'berakhir') ? 'danger' : (($status === 'terbuka') ? 'success' : 'secondary') ?> h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title mb-1"><?= esc($item['nama_mapel']) ?></h5>
                                <p class="mb-1">Guru: <?= esc($item['guru']) ?></p>
                                <p class="mb-1">Kelas: <?= esc($item['nama_kelas']) ?></p>
                                <p class="mb-1">Hari: <?= esc($item['hari']) ?></p>
                                <p class="mb-1">Jam: <?= esc($item['jam_mulai']) ?> - <?= esc($item['jam_selesai']) ?> WIB</p>

                                <?php if ($status === 'terbuka'): ?>
                                    <div class="text-success small mt-2">
                                        <i class="fas fa-unlock-alt"></i> Sesi absensi terbuka, silakan absen.
                                    </div>
                                <?php elseif ($status === 'berakhir'): ?>
                                    <div class="text-danger small mt-2">
                                        <i class="fas fa-exclamation-circle"></i> Waktu absensi telah berakhir!
                                    </div>
                                <?php elseif ($status === 'belum'): ?>
                                    <div class="text-secondary small mt-2">
                                        <i class="fas fa-lock"></i> Sesi absensi belum dibuka.
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted small mt-2">
                                        <i class="fas fa-calendar-times"></i> Jadwal ini bukan untuk hari ini.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- ====== BAGIAN TOMBOL ====== -->
                            <div>
                                <?php if ($status === 'terbuka' && !$isOperator): ?>
                                    <?php if ($item['boleh_absen']): ?>
                                        <a href="<?= site_url('absensi/mulai-absensi/' . $item['id_jadwal']) ?>" class="btn btn-sm btn-success">
                                            Mulai Absensi
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-warning" disabled>
                                            Sudah Absen Hari Ini
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        <i class="fas fa-lock"></i>
                                        <?php if ($isOperator && $status === 'terbuka'): ?>
                                            Tidak Tersedia
                                        <?php endif; ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <!-- ====== END BAGIAN TOMBOL ====== -->

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection() ?>