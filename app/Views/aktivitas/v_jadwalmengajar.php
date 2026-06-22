<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<?php $role = session()->get('role'); ?>

<?php if (session()->getFlashdata('swal_error')): ?>
    <script>Swal.fire({ icon: 'error', title: 'Gagal!', html: '<?= session()->getFlashdata('swal_error') ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 5000 });</script>
<?php endif; ?>
<?php if (session()->getFlashdata('swal_success')): ?>
    <script>Swal.fire({ icon: 'success', title: 'Sukses!', text: '<?= session()->getFlashdata('swal_success') ?>', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });</script>
<?php endif; ?>

<div class="d-flex justify-content-center align-items-center mb-4">
    <h1 class="h1 mb-0">Jadwal Mengajar</h1>
</div>

<?php if ($role === 'kepala sekolah'): ?>
    <div class="alert alert-info py-2 mb-3">
        <i class="fas fa-info-circle"></i> Anda hanya dapat melihat jadwal mengajar. Hubungi <strong>Operator</strong> untuk menambah atau menghapus jadwal.
    </div>
<?php endif; ?>

<!-- ====== TABEL JADWAL ====== -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead class="thead-dark bg-dark text-white">
                    <tr>
                        <th style="width:150px;">Nama Guru</th>
                        <th>Senin</th>
                        <th>Selasa</th>
                        <th>Rabu</th>
                        <th>Kamis</th>
                        <th>Jumat</th>
                        <th>Sabtu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jadwal)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tidak ada jadwal tersedia.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($jadwal as $nama_guru => $harian): ?>
                            <tr>
                                <td class="text-start fw-bold"><?= esc($nama_guru) ?></td>
                                <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari): ?>
                                    <td>
                                        <?php if (isset($harian[$hari])): ?>
                                            <?php foreach ($harian[$hari] as $slot): ?>
                                                <div class="bg-success text-white rounded p-2 mb-2 position-relative">
                                                    <?= date('H.i', strtotime($slot['jam_mulai'])) ?> - <?= date('H.i', strtotime($slot['jam_selesai'])) ?><br>
                                                    <small><?= esc($slot['mapel']) ?> - <strong><?= esc($slot['kelas']) ?></strong></small>

                                                    <?php if ($role === 'operator'): ?>
                                                        <form action="<?= site_url('jadwal-mengajar/hapus') ?>" method="post" class="form-hapus d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="id_jadwal" value="<?= $slot['id_jadwal'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-light text-danger position-absolute top-0 end-0 me-1 ml-2 mt-1" title="Hapus">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($role === 'operator'): ?>
<!-- ====== FORM TAMBAH JADWAL (hanya operator) ====== -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        Tambah Jadwal Mengajar
    </div>
    <div class="card-body">
        <form action="<?= site_url('jadwal-mengajar/tambah-jadwal-mengajar') ?>" method="post">
            <?= csrf_field() ?>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Kelas</label>
                    <select name="id_kelas" class="form-control" required>
                        <option selected disabled>Pilih Kelas</option>
                        <?php foreach ($kelas as $k): ?>
                            <option value="<?= $k['id_kelas'] ?>"><?= esc($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Nama Guru</label>
                    <select name="id_guru" class="form-control" required>
                        <option selected disabled>Pilih Guru</option>
                        <?php foreach ($guru as $g): ?>
                            <option value="<?= $g['id_user'] ?>"><?= esc($g['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Mata Pelajaran</label>
                    <select name="kode_mapel" class="form-control" required>
                        <option selected disabled>Pilih Mata Pelajaran</option>
                        <?php foreach ($mapel as $m): ?>
                            <option value="<?= $m['kode_mapel'] ?>"><?= esc($m['nama_mapel']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Hari</label>
                    <select class="form-control" name="hari" required>
                        <option value="" selected disabled>Pilih Hari</option>
                        <option>Senin</option><option>Selasa</option><option>Rabu</option>
                        <option>Kamis</option><option>Jumat</option><option>Sabtu</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Jam Mulai</label>
                    <input type="time" class="form-control" name="jam_mulai" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Jam Selesai</label>
                    <input type="time" class="form-control" name="jam_selesai" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Simpan Jadwal</button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.form-hapus').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin menghapus jadwal ini?',
                text: 'Data yang dihapus tidak bisa dikembalikan.',
                icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
            }).then(r => { if (r.isConfirmed) form.submit(); });
        });
    });
</script>
<?php endif; ?>

<?= $this->endSection() ?>