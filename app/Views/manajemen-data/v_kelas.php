<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<?php $role = session()->get('role'); ?>

<?php if (session()->getFlashdata('swal_success')): ?>
    <script>Swal.fire({ toast: true, icon: 'success', title: '<?= session()->getFlashdata('swal_success') ?>', position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });</script>
<?php endif; ?>
<?php if (session()->getFlashdata('swal_error')): ?>
    <script>Swal.fire({ toast: true, icon: 'error', html: '<?= is_array(session()->getFlashdata('swal_error')) ? implode('<br>', session()->getFlashdata('swal_error')) : session()->getFlashdata('swal_error') ?>', position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });</script>
<?php endif; ?>

<div class="row">

    <?php if ($role === 'operator'): ?>
    <!-- ====== FORM TAMBAH KELAS (hanya operator) ====== -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Form Tambah Kelas</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('kelas/tambah-kelas') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group mb-3">
                        <label>Nama Kelas</label>
                        <input type="text" class="form-control text-uppercase" name="nama_kelas" placeholder="Contoh: 7A" required style="text-transform:uppercase;">
                    </div>
                    <div class="form-group mb-3">
                        <label>Wali Kelas</label>
                        <select name="wali_kelas" class="form-control" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                            <?php foreach ($guru as $g): ?>
                                <option value="<?= $g['id_user'] ?>"><?= $g['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 mb-4">
    <?php else: ?>
    <!-- Kepala sekolah: tabel penuh -->
    <div class="col-md-12 mb-4">
        <div class="alert alert-info py-2 mb-3">
            <i class="fas fa-info-circle"></i> Anda hanya dapat melihat data kelas. Hubungi <strong>Operator</strong> untuk melakukan perubahan data.
        </div>
    <?php endif; ?>

        <!-- ====== TABEL KELAS ====== -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><?= $role === 'kepala sekolah' ? 'Data Kelas' : 'Daftar Kelas' ?></h5>
            </div>
            <div class="card-body">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <?php if ($role === 'operator'): ?>
                                <th class="text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($kelas as $k): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($k['nama_kelas']) ?></td>
                                <td><?= esc($k['nama_wali'] ?? '-') ?></td>
                                <?php if ($role === 'operator'): ?>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal"
                                            onclick="isiEdit('<?= esc($k['nama_kelas']) ?>', '<?= $k['id_kelas'] ?>', '<?= $k['id_user'] ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger btn-hapus" data-id="<?= $k['id_kelas'] ?>">
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
<!-- ====== MODAL EDIT (hanya operator) ====== -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Kelas</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="<?= base_url('kelas/edit-kelas') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" id="edit_id_kelas" name="id_kelas">
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" class="form-control" id="edit_nama_kelas" name="nama_kelas" required style="text-transform:uppercase;">
                    </div>
                    <div class="form-group">
                        <label>Wali Kelas</label>
                        <select id="edit_wali_kelas" name="wali_kelas" class="form-control" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                            <?php foreach ($guru as $g): ?>
                                <option value="<?= $g['id_user'] ?>"><?= $g['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success" form="editForm">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<form id="formHapus" action="<?= base_url('kelas/hapus-kelas') ?>" method="post" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="id_kelas" id="inputIdKelas">
</form>

<script>
    function isiEdit(namaKelas, idKelas, idWali) {
        document.getElementById('edit_nama_kelas').value = namaKelas;
        document.getElementById('edit_id_kelas').value = idKelas;
        const sel = document.getElementById('edit_wali_kelas');
        for (let i = 0; i < sel.options.length; i++) {
            if (sel.options[i].value == idWali) { sel.selectedIndex = i; break; }
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-hapus').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Yakin ingin menghapus kelas ini?',
                    text: 'Data yang dihapus tidak bisa dikembalikan!',
                    icon: 'warning', showCancelButton: true,
                    confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
                }).then(r => {
                    if (r.isConfirmed) {
                        document.getElementById('inputIdKelas').value = id;
                        document.getElementById('formHapus').submit();
                    }
                });
            });
        });
    });
</script>
<?php endif; ?>

<?= $this->endSection() ?>