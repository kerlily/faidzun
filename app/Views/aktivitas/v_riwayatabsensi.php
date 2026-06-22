<?= $this->extend('v_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <h4 class="mb-4">Riwayat Absensi Siswa</h4>
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

    <?php if (session()->getFlashdata('swal_error')): ?>
        <script>
            Swal.fire({
                toast: true,
                icon: 'error',
                html: '<?= implode('<br>', session()->getFlashdata('swal_error')) ?>',
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        </script>
    <?php endif; ?>
    <!-- Filter untuk kepala sekolah & operator -->
    <?php if (in_array($role, ['kepala sekolah', 'operator'])): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="fas fa-filter"></i> Filter Data</h5>

                <!-- Form Filter -->
                <form method="get" class="row g-3">
                    <div class="col-md-4">
                        <label for="filterKelas" class="form-label">Kelas</label>
                        <select class="form-control" id="filterKelas" name="kelas">
                            <option value="Semua" <?= $filterKelas == 'Semua' ? 'selected' : '' ?>>Semua</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?= esc($k['id_kelas']) ?>" <?= $filterKelas == $k['id_kelas'] ? 'selected' : '' ?>>
                                    <?= esc($k['nama_kelas']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="filterMapel" class="form-label">Mata Pelajaran</label>
                        <select class="form-control" id="filterMapel" name="mapel">
                            <option value="Semua" <?= $filterMapel == 'Semua' ? 'selected' : '' ?>>Semua</option>
                            <?php foreach ($mapelList as $m): ?>
                                <option value="<?= esc($m['kode_mapel']) ?>" <?= $filterMapel == $m['kode_mapel'] ? 'selected' : '' ?>>
                                    <?= esc($m['nama_mapel']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div>
                            <button class="btn btn-primary me-2" type="submit">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="<?= base_url('riwayat-absensi') ?>" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3 text-danger"><i class="fas fa-trash-alt"></i> Hapus Data Absensi Siswa per Kelas</h5>

                <!-- Form Hapus Per Kelas -->
                <form action="<?= base_url('/data-siswa/hapus_kelas') ?>" method="post" class="row g-3"
                    onsubmit="return confirm('Yakin ingin menghapus semua siswa dari kelas yang dipilih?')">

                    <div class="col-md-6">
                        <label for="id_kelas" class="form-label">Pilih Kelas</label>
                        <select name="id_kelas" id="id_kelas" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelasList as $k): ?>
                                <option value="<?= esc($k['id_kelas']) ?>"><?= esc($k['nama_kelas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Hapus Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Filter untuk siswa (hanya mapel) -->
    <?php elseif ($role === 'siswa'): ?>
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="form-inline">
                    <label class="mr-2" for="filterMapel">Mata Pelajaran:</label>
                    <select class="form-control mr-3 mb-2" id="filterMapel" name="mapel">
                        <option value="Semua" <?= $filterMapel == 'Semua' ? 'selected' : '' ?>>Semua</option>
                        <?php foreach ($mapelList as $m): ?>
                            <option value="<?= esc($m['kode_mapel']) ?>" <?= $filterMapel == $m['kode_mapel'] ? 'selected' : '' ?>>
                                <?= esc($m['nama_mapel']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button class="btn btn-primary mb-2" type="submit">Filter</button>
                    <a href="<?= base_url('riwayat-absensi') ?>" class="btn btn-secondary mb-2 ml-2">Reset</a>
                </form>
            </div>
        </div>
    <?php endif; ?>


    <!-- Tabel Riwayat -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Data <?php if (in_array($role, ['kepala sekolah', 'operator'])): ?> Semua <?php endif; ?>Riwayat Absensi</span>
        </div>
        <div class="card-body table-responsive">
            <table id="dataTable" class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Kelamin</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Tanggal</th>
                        <th>Materi</th>
                        <th>Status Kehadiran</th>
                        <?php if (in_array($role, ['kepala sekolah', 'operator'])): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($riwayat as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($row['nama']) ?></td>
                            <td><?= esc($row['jenis_kelamin']) ?></td>
                            <td><?= esc($row['nama_kelas']) ?></td>
                            <td><?= esc($row['nama_mapel']) ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= esc($row['materi']) ?></td>
                            <td>
                                <?php
                                $badge = 'secondary';
                                if ($row['keterangan'] == 'Masuk') $badge = 'success';
                                elseif ($row['keterangan'] == 'Izin') $badge = 'warning';
                                elseif ($row['keterangan'] == 'Sakit') $badge = 'info';
                                elseif ($row['keterangan'] == 'Alpa') $badge = 'danger';
                                ?>
                                <span class="badge badge-<?= $badge ?>"><?= esc($row['keterangan']) ?></span>
                            </td>
                            <?php if (in_array($role, ['kepala sekolah', 'operator'])): ?>
                                <td>
                                    <button
                                        class="btn btn-sm btn-warning"
                                        onclick="isiEdit(
                                            '<?= esc($row['id_absen']) ?>',
                                            '<?= esc($row['nama']) ?>',
                                            '<?= esc($row['nama_kelas']) ?>',
                                            '<?= esc($row['nama_mapel']) ?>',
                                            '<?= date('d-m-Y', strtotime($row['tanggal'])) ?>',
                                            '<?= esc($row['keterangan']) ?>'
                                        )"
                                        title="Edit Kehadiran">
                                        <i class="fas fa-edit"></i>
                                    </button>



                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Modal Edit Status Kehadiran -->
<div class="modal fade" id="modalEditStatus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= base_url('riwayat-absensi/edit-absensi') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Status Kehadiran</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <!-- ID Absensi -->
                    <input type="hidden" name="id_absensi" id="editIdAbsensi">

                    <div class="form-group">
                        <label>Nama Siswa</label>
                        <input type="text" id="viewNama" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Kelas</label>
                        <input type="text" id="viewKelas" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Mata Pelajaran</label>
                        <input type="text" id="viewMapel" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="text" id="viewTanggal" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Status Kehadiran</label>
                        <select name="status" id="editStatus" class="form-control" required>
                            <option value="Masuk">Masuk</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alpa">Alpa</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function isiEdit(id_absensi, nama, kelas, mapel, tanggal, status) {
        document.getElementById('editIdAbsensi').value = id_absensi;
        document.getElementById('viewNama').value = nama;
        document.getElementById('viewKelas').value = kelas;
        document.getElementById('viewMapel').value = mapel;
        document.getElementById('viewTanggal').value = tanggal;
        document.getElementById('editStatus').value = status;
        $('#modalEditStatus').modal('show');
    }
</script>

<?= $this->endSection() ?>