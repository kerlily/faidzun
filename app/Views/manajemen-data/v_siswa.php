<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<?php $role = session()->get('role'); ?>

<?php if (session()->getFlashdata('swal_success')): ?>
    <script>
        Swal.fire({ toast: true, icon: 'success', title: '<?= session()->getFlashdata('swal_success') ?>', position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
    </script>
<?php endif; ?>
<?php if (session()->getFlashdata('swal_error')): ?>
    <script>
        Swal.fire({ toast: true, icon: 'error', html: '<?= session()->getFlashdata('swal_error') ?>', position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
    </script>
<?php endif; ?>

<div class="container-fluid">

    <?php if ($role === 'operator'): ?>
    <!-- ====== IMPORT SISWA (hanya operator) ====== -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-upload"></i> Import Data Siswa</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('siswa/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="input-group mb-2">
                    <div class="custom-file">
                        <input type="file" name="file_excel" class="custom-file-input" id="fileExcel" accept=".xls,.xlsx,.csv" required>
                        <label class="custom-file-label" for="fileExcel">Pilih file Excel</label>
                    </div>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Import</button>
                    </div>
                </div>
                <small class="form-text text-muted">
                    Kolom: <strong>nama, nisn, ttl, jenis_kelamin, agama, kelas, alamat, no_hp</strong><br>
                    <a href="<?= base_url('assets/contoh_data.xlsx') ?>"><i class="bi bi-download"></i> Download contoh file Excel</a>
                </small>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('fileExcel').addEventListener('change', function (e) {
            this.nextElementSibling.innerText = e.target.files[0].name;
        });
    </script>

    <!-- ====== FORM TAMBAH SISWA (hanya operator) ====== -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-plus"></i> Form Tambah Siswa</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('siswa/tambah-siswa') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>NISN</label>
                        <input type="text" class="form-control" name="nisn" placeholder="Nomor Induk Siswa Nasional" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Tempat, Tanggal Lahir</label>
                        <input type="text" class="form-control" name="ttl" placeholder="Contoh: Pekalongan, 10 Mei 2008" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin" required>
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="Laki-Laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Agama</label>
                        <select class="form-control" name="agama" required>
                            <option value="" disabled selected>-- Pilih --</option>
                            <option>Islam</option><option>Kristen</option><option>Katolik</option>
                            <option>Hindu</option><option>Buddha</option><option>Konghucu</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Role</label>
                        <input type="text" class="form-control" value="Siswa" disabled>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Kelas</label>
                        <select name="kelas" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id_kelas'] ?>"><?= $k['nama_kelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Foto</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputFoto" name="foto" accept="image/*">
                        <label class="custom-file-label" for="inputFoto">Choose file</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat Lengkap" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Nomor HP</label>
                        <input type="text" class="form-control" name="no_hp" placeholder="08123456789" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Password Akun</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <div class="input-group-append">
                                <span class="input-group-text" onclick="togglePassword()" style="cursor:pointer;">
                                    <i class="bi bi-eye" id="eye-icon"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ====== TABEL SISWA (kepala sekolah & operator) ====== -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <?= $role === 'kepala sekolah' ? 'Data Siswa' : 'Manajemen Siswa' ?>
            </h5>
            <a href="<?= base_url('siswa/export-excel') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-file-excel"></i> Download Excel
            </a>
        </div>
        <div class="card-body">
            <?php if ($role === 'kepala sekolah'): ?>
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-info-circle"></i> Anda hanya dapat melihat data siswa. Hubungi <strong>Operator</strong> untuk melakukan perubahan data.
                </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>NISN</th>
                            <th>Username</th>
                            <th>Kelas</th>
                            <th>Jenis Kelamin</th>
                            <th>Agama</th>
                            <th>TTL</th>
                            <th>Alamat</th>
                            <th>No. HP</th>
                            <?php if ($role === 'operator'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($siswa as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <a href="<?= base_url('profil/' . ($row['foto'] ?? 'users.png')) ?>" data-lightbox="foto-siswa">
                                        <img src="<?= base_url('profil/' . ($row['foto'] ?? 'users.png')) ?>" alt="Foto" width="60">
                                    </a>
                                </td>
                                <td><?= esc($row['nama']) ?></td>
                                <td><?= esc($row['nisn']) ?></td>
                                <td><?= esc($row['username']) ?></td>
                                <td><?= esc($row['nama_kelas']) ?></td>
                                <td><?= esc($row['jenis_kelamin']) ?></td>
                                <td><?= esc($row['agama']) ?></td>
                                <td><?= esc($row['ttl']) ?></td>
                                <td><?= esc($row['alamat']) ?></td>
                                <td><?= esc($row['no_hp']) ?></td>
                                <?php if ($role === 'operator'): ?>
                                    <td>
                                        <div class="d-flex">
                                            <a href="<?= base_url('siswa/edit-siswa/' . $row['id_user']) ?>" class="btn btn-sm btn-warning mr-1" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <!-- FORM HAPUS dengan method POST -->
                                            <form action="<?= base_url('siswa/hapus/' . $row['id_user']) ?>" method="post" class="d-inline form-hapus">
                                                <?= csrf_field() ?>
                                                <button type="button" class="btn btn-sm btn-danger btn-hapus"
                                                    data-id="<?= $row['id_user'] ?>"
                                                    data-nama="<?= esc($row['nama']) ?>">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-hapus').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var nama = this.getAttribute('data-nama');
            var form = this.closest('form');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data siswa "' + nama + '" akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>
<?= $this->endSection() ?>