<?= $this->extend('v_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('swal_success')): ?>
        <script>Swal.fire({ toast: true, icon: 'success', title: '<?= session()->getFlashdata('swal_success') ?>', position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });</script>
    <?php endif; ?>
    <?php if (session()->getFlashdata('swal_error')): ?>
        <script>Swal.fire({ toast: true, icon: 'error', html: '<?= implode('<br>', session()->getFlashdata('swal_error')) ?>', position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });</script>
    <?php endif; ?>

    <h4 class="mb-4">Riwayat Absensi Siswa</h4>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Klik kelas untuk melihat jadwal, klik jadwal untuk melihat detail absensi siswa.
    </div>

    <?php
    // Susun struktur: kelas -> jadwal(mapel+tanggal) -> siswa
    $tree = [];
    foreach ($riwayat as $row) {
        $idKelas  = $row['id_kelas'];
        $idJadwal = $row['id_jadwal'] . '_' . $row['tanggal'];

        if (!isset($tree[$idKelas])) {
            $tree[$idKelas] = [
                'nama_kelas' => $row['nama_kelas'],
                'jadwal'     => [],
            ];
        }
        if (!isset($tree[$idKelas]['jadwal'][$idJadwal])) {
            $tree[$idKelas]['jadwal'][$idJadwal] = [
                'nama_mapel' => $row['nama_mapel'],
                'guru'       => $row['guru'] ?? '-',
                'id_guru'    => $row['id_guru'] ?? null,
                'tanggal'    => $row['tanggal'],
                'materi'     => $row['materi'],
                'rekap'      => ['Masuk' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0],
                'siswa'      => [],
            ];
        }
        $tree[$idKelas]['jadwal'][$idJadwal]['siswa'][] = $row;
        if (isset($tree[$idKelas]['jadwal'][$idJadwal]['rekap'][$row['keterangan']])) {
            $tree[$idKelas]['jadwal'][$idJadwal]['rekap'][$row['keterangan']]++;
        }
    }
    ?>

    <?php if (empty($tree)): ?>
        <div class="alert alert-warning text-center">
            <i class="fas fa-info-circle"></i> Tidak ada data riwayat absensi.
        </div>
    <?php else: ?>

    <div class="row" id="levelKelas">
        <?php foreach ($tree as $idKelas => $kelas): ?>
            <?php
            $totalAlpa = 0;
            foreach ($kelas['jadwal'] as $j) $totalAlpa += $j['rekap']['Alpa'];
            $border = $totalAlpa > 0 ? 'danger' : 'success';
            ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow border-left-<?= $border ?> h-100" style="cursor:pointer"
                    onclick="toggleKelas('kelas-<?= $idKelas ?>', this)">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1"><?= esc($kelas['nama_kelas']) ?></h5>
                            <p class="mb-0 text-muted small"><?= count($kelas['jadwal']) ?> sesi absensi tercatat</p>
                        </div>
                        <div>
                            <i class="fas fa-chevron-down text-muted toggle-icon-<?= $idKelas ?>"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Level 2: Jadwal per kelas -->
            <div class="col-12 mb-3 d-none" id="kelas-<?= $idKelas ?>">
                <div class="pl-3 border-left border-primary">
                    <h6 class="text-primary mb-3"><i class="fas fa-chevron-right"></i> <?= esc($kelas['nama_kelas']) ?> — Pilih Jadwal</h6>
                    <div class="row">
                        <?php foreach ($kelas['jadwal'] as $idJadwal => $jadwal): ?>
                            <?php $border2 = $jadwal['rekap']['Alpa'] > 0 ? 'danger' : 'success'; ?>
                            <div class="col-md-6 mb-3">
                                <div class="card shadow border-left-<?= $border2 ?>" style="cursor:pointer"
                                    onclick="toggleJadwal('jadwal-<?= md5($idKelas.$idJadwal) ?>', this)">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-1"><?= esc($jadwal['nama_mapel']) ?></h6>
                                            <p class="mb-1 small">Guru: <?= esc($jadwal['guru']) ?></p>
                                            <p class="mb-1 small">Tanggal: <?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?></p>
                                            <p class="mb-1 small">Materi: <?= esc($jadwal['materi']) ?></p>
                                            <div class="mt-1">
                                                <span class="badge badge-success">Masuk: <?= $jadwal['rekap']['Masuk'] ?></span>
                                                <span class="badge badge-warning">Izin: <?= $jadwal['rekap']['Izin'] ?></span>
                                                <span class="badge badge-info">Sakit: <?= $jadwal['rekap']['Sakit'] ?></span>
                                                <span class="badge badge-danger">Alpa: <?= $jadwal['rekap']['Alpa'] ?></span>
                                            </div>
                                        </div>
                                        <div>
                                            <i class="fas fa-chevron-down text-muted"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Level 3: Detail siswa -->
                                <div class="d-none mt-2" id="jadwal-<?= md5($idKelas.$idJadwal) ?>">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Siswa</th>
                                                    <th>L/P</th>
                                                    <th>Status</th>
                                                    <?php if (in_array($role, ['kepala sekolah', 'operator', 'guru', 'guru bk'])): ?>
                                                        <th>Aksi</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $no = 1; foreach ($jadwal['siswa'] as $siswa): ?>
                                                    <?php
                                                    $badgeMap = ['Masuk' => 'success', 'Izin' => 'warning', 'Sakit' => 'info', 'Alpa' => 'danger'];
                                                    $b = $badgeMap[$siswa['keterangan']] ?? 'secondary';
                                                    ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td><?= esc($siswa['nama']) ?></td>
                                                        <td><?= esc($siswa['jenis_kelamin']) ?></td>
                                                        <td><span class="badge badge-<?= $b ?>"><?= esc($siswa['keterangan']) ?></span></td>
                                                       <?php if (in_array($role, ['kepala sekolah', 'operator']) || (in_array($role, ['guru', 'guru bk']) && ($jadwal['id_guru'] ?? '') == session()->get('id_user'))): ?>
                                                         <td>
                                                            <button class="btn btn-sm btn-warning"
                                                                onclick="event.stopPropagation(); isiEdit(
                                                                    '<?= esc($siswa['id_absen']) ?>',
                                                                    '<?= esc($siswa['nama']) ?>',
                                                                    '<?= esc($jadwal['nama_kelas'] ?? $kelas['nama_kelas']) ?>',
                                                                    '<?= esc($jadwal['nama_mapel']) ?>',
                                                                    '<?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?>',
                                                                    '<?= esc($siswa['keterangan']) ?>'
                                                                )">
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
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEditStatus" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="<?= site_url('riwayat-absensi/edit-absensi') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Status Kehadiran</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
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
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleKelas(id, card) {
    var el = document.getElementById(id);
    el.classList.toggle('d-none');
    var icon = card.querySelector('[class*="toggle-icon"]');
    if (icon) icon.classList.toggle('fa-chevron-up');
    if (icon) icon.classList.toggle('fa-chevron-down');
}

function toggleJadwal(id, card) {
    var el = document.getElementById(id);
    el.classList.toggle('d-none');
    var icon = card.querySelector('.fa-chevron-down, .fa-chevron-up');
    if (icon) icon.classList.toggle('fa-chevron-up');
    if (icon) icon.classList.toggle('fa-chevron-down');
}

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