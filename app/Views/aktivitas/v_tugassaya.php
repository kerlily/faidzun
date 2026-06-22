<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<?php $role = session()->get('role'); ?>
<!-- Halaman Tugas Saya -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tugas Saya <?php if (in_array($role, ['kepala sekolah', 'operator'])): ?>
                (Semua Tugas Siswa)
            <?php endif; ?>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Judul Tugas</th>
                        <th>Batas Pengumpulan</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($tugas as $item) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($item['nama_kelas']) ?></td>
                            <td><?= esc($item['nama_mapel']) ?></td>
                            <td><?= esc($item['judul_tugas']) ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($item['deadline'])) ?></td>
                            <td><?= esc($item['catatan']) ?></td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>