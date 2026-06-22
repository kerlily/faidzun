<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<?php $role = session()->get('role'); ?>

<?php if (session()->getFlashdata('swal_success')): ?>
    <script>Swal.fire({ toast: true, icon: 'success', title: '<?= session()->getFlashdata('swal_success') ?>', position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });</script>
<?php endif; ?>
<?php if (session()->getFlashdata('swal_error')): ?>
    <script>
        Swal.fire({
            toast: true, icon: 'error',
            html: <?= json_encode(is_array(session()->getFlashdata('swal_error')) ? implode('<br>', session()->getFlashdata('swal_error')) : session()->getFlashdata('swal_error')) ?>,
            position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true,
        });
    </script>
<?php endif; ?>

<div class="row">

    <?php if ($role === 'operator'): ?>
    <!-- ====== FORM TAMBAH MAPEL (hanya operator) ====== -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Form Tambah Mata Pelajaran</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('mapel/tambah-mapel') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group mb-3">
                        <label>Kode Mata Pelajaran</label>
                        <input type="text" name="kode_mapel" class="form-control" placeholder="Contoh: MTH001" required style="text-transform:uppercase;">
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" class="form-control" placeholder="Contoh: Matematika" style="text-transform:uppercase;">
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
    <?php else: ?>
    <div class="col-md-12 mb-4">
        <div class="alert alert-info py-2 mb-3">
            <i class="fas fa-info-circle"></i> Anda hanya dapat melihat data mata pelajaran. Hubungi <strong>Operator</strong> untuk melakukan perubahan data.
        </div>
    <?php endif; ?>

        <!-- ====== TABEL MAPEL ====== -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><?= $role === 'kepala sekolah' ? 'Data Mata Pelajaran' : 'Daftar Mata Pelajaran' ?></h5>
            </div>
            <div class="card-body">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <?php if ($role === 'operator'): ?>
                                <th class="text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($mapel as $m): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($m['kode_mapel']) ?></td>
                                <td><?= esc($m['nama_mapel']) ?></td>
                                <?php if ($role === 'operator'): ?>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal"
                                            onclick="isiEditMapel('<?= esc($m['kode_mapel']) ?>', '<?= esc($m['nama_mapel']) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="konfirmasiHapus('<?= esc($m['kode_mapel']) ?>')">
                                            <i class="fas fa-trash"></i>
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
</div>

<?php if ($role === 'operator'): ?>
<!-- ====== MODAL EDIT MAPEL (hanya operator) ====== -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Mata Pelajaran</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editMapelForm" action="<?= base_url('mapel/edit-mapel') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kode_mapel_lama" id="kode_mapel_lama">
                    <div class="form-group mb-3">
                        <label>Kode</label>
                        <input type="text" class="form-control" name="kode_mapel" id="edit_kode_mapel" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama Mata Pelajaran</label>
                        <input type="text" class="form-control" name="nama_mapel" id="edit_nama_mapel" required style="text-transform:uppercase;">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    function isiEditMapel(kode, nama) {
        document.getElementById('edit_kode_mapel').value = kode;
        document.getElementById('edit_nama_mapel').value = nama;
        document.getElementById('kode_mapel_lama').value = kode;
    }
    function konfirmasiHapus(kodeMapel) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data mata pelajaran dengan kode ' + kodeMapel + ' akan dihapus!',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
        }).then(r => { if (r.isConfirmed) window.location.href = '/mapel/hapus/' + kodeMapel; });
    }
</script>
<?php endif; ?>

<?= $this->endSection() ?>