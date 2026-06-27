<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<?php $role = session()->get('role'); ?>

<?php if (session()->getFlashdata('swal_success')): ?>
    <script>
        Swal.fire({
            toast: true, icon: 'success',
            title: '<?= session()->getFlashdata('swal_success') ?>',
            position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true,
        });
    </script>
<?php endif; ?>
<?php if (session()->getFlashdata('swal_error')): ?>
    <script>
        Swal.fire({
            toast: true, icon: 'error',
            title: '<?= session()->getFlashdata('swal_error') ?>',
            position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true,
        });
    </script>
<?php endif; ?>

<div class="container-fluid">

    <?php if ($role === 'operator'): ?>
    <!-- ====== FORM TAMBAH GURU (hanya operator) ====== -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Form Tambah Guru</h5>
        </div>
        <div class="card-body">
            <form action="<?= site_url('guru/tambah-guru') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <!-- Row 1: Nama & Username -->
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

                <!-- Row 2: NIP & TTL -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>NIP</label>
                        <input type="text" class="form-control" name="nip" placeholder="Nomor Induk Pegawai" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Tempat, Tanggal Lahir</label>
                        <input type="text" class="form-control" name="ttl" placeholder="Contoh: Pekalongan, 10 Mei 1985" required>
                    </div>
                </div>

                <!-- Row 3: Jenis Kelamin & Agama -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Jenis Kelamin</label>
                        <select class="form-control" name="gender" required>
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-Laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Agama</label>
                        <select class="form-control" name="agama" required>
                            <option value="" disabled selected>-- Pilih Agama --</option>
                            <option>Islam</option><option>Kristen</option><option>Katolik</option>
                            <option>Hindu</option><option>Buddha</option><option>Konghucu</option>
                        </select>
                    </div>
                </div>

                <!-- Row 4: Role & Pendidikan -->
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Role</label>
                        <select class="form-control" name="role" required>
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <option value="Kepala Sekolah">Kepala Sekolah</option>
                            <option value="Guru">Guru</option>
                            <option value="Guru BK">Guru BK</option>
                            <option value="Operator">Operator</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Pendidikan Terakhir</label>
                        <select class="form-control" name="pendidikan" required>
                            <option value="" disabled selected>-- Pilih Pendidikan --</option>
                            <option>SMA/SMK</option><option>D3</option>
                            <option>S1</option><option>S2</option><option>S3</option>
                        </select>
                    </div>
                </div>

                <!-- Row 5: Foto (full width) -->
                <div class="form-group">
                    <label>Foto</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputFoto" name="foto" accept="image/*">
                        <label class="custom-file-label" for="inputFoto">Choose file</label>
                    </div>
                </div>

                <!-- Row 6: Alamat (full width) -->
                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea class="form-control" name="alamat" rows="2" placeholder="Alamat Lengkap" required></textarea>
                </div>

                <!-- Row 7: No HP & Password -->
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

                <button type="submit" class="btn btn-primary mt-2">Tambah</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ====== TABEL GURU ====== -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <?= $role === 'kepala sekolah' ? 'Data Guru (View Only)' : 'Manajemen Guru' ?>
            </h5>
            <a href="<?= site_url('guru/export-excel') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-file-excel"></i> Download Excel
            </a>
        </div>
        <div class="card-body">
            <?php if ($role === 'kepala sekolah'): ?>
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-info-circle"></i> Anda hanya dapat melihat data guru. Hubungi <strong>Operator</strong> untuk melakukan perubahan data.
                </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Jenis Kelamin</th>
                            <th>Agama</th>
                            <th>NIP</th>
                            <th>TTL</th>
                            <th>Alamat</th>
                            <th>Role</th>
                            <th>Pendidikan</th>
                            <th>No. HP</th>
                            <?php if ($role === 'operator'): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($guru as $g): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <a href="<?= base_url('profil/' . ($g['foto'] ?? 'users.png')) ?>" data-lightbox="foto-guru">
                                        <img src="<?= base_url('profil/' . ($g['foto'] ?? 'users.png')) ?>" alt="Foto" width="60">
                                    </a>
                                </td>
                                <td><?= esc($g['nama']) ?></td>
                                <td><?= esc($g['username']) ?></td>
                                <td><?= esc($g['jenis_kelamin']) ?></td>
                                <td><?= esc($g['agama']) ?></td>
                                <td><?= esc($g['nip']) ?></td>
                                <td><?= esc($g['ttl']) ?></td>
                                <td><?= esc($g['alamat']) ?></td>
                                <td><?= ucfirst($g['role']) ?></td>
                                <td><?= esc($g['pendidikan_terakhir']) ?></td>
                                <td><?= esc($g['no_hp']) ?></td>
                                <?php if ($role === 'operator'): ?>
                                    <td>
                                        <div class="d-flex">
                                            <a href="<?= site_url('guru/edit-guru/' . $g['id_user']) ?>" class="btn btn-sm btn-warning mr-1" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-hapus"
                                                data-id="<?= $g['id_user'] ?>"
                                                data-nama="<?= esc($g['nama']) ?>" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <form id="form-hapus-<?= $g['id_user'] ?>" action="<?= site_url('guru/hapus/' . $g['id_user']) ?>" method="post" style="display:none;">
                                                <?= csrf_field() ?>
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
    // Fungsi toggle password
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    }

    // File input label
    $('#inputFoto').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // SweetAlert untuk hapus
    document.querySelectorAll('.btn-hapus').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `Data guru atas nama "${nama}" akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-hapus-' + id).submit();
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>