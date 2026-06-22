<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Card Import Siswa -->
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
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload"></i> Import
                        </button>
                    </div>
                </div>

                <!-- Keterangan Format -->
                <small class="form-text text-muted">
                    Pastikan format file sesuai contoh berikut: <br>
                    Kolom: <strong>nama, nisn, ttl, jenis_kelamin, agama, kelas, alamat, no_hp</strong> <br>
                    <a href="<?= base_url('assets/contoh_data.xlsx') ?>" class="btn btn-link p-0">
                        <i class="bi bi-download"></i> Download contoh file Excel
                    </a>

                </small>

            </form>
        </div>
    </div>


    <script>
        // Tampilkan nama file di label
        document.getElementById("fileExcel").addEventListener("change", function(e) {
            const fileName = e.target.files[0].name;
            e.target.nextElementSibling.innerText = fileName;
        });
    </script>

    <!-- Card Tambah Siswa Manual -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-plus"></i> Form Tambah Siswa</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('siswa/tambah-siswa') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nisn">NISN</label>
                        <input type="text" class="form-control" name="nisn" id="nisn" placeholder="Nomor Induk Siswa Nasional" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ttl">Tempat, Tanggal Lahir</label>
                        <input type="text" class="form-control" name="ttl" id="ttl" placeholder="Contoh: Pekalongan, 10 Mei 2008" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-Laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="agama">Agama</label>
                        <select class="form-control" name="agama" id="agama" required>
                            <option value="" disabled selected>-- Pilih Agama --</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Role</label>
                        <input type="text" class="form-control" value="Siswa" disabled>
                        <input type="hidden" name="role" value="siswa">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="id_kelas">Kelas</label>
                        <select name="kelas" id="id_kelas" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id_kelas']; ?>"><?= $k['nama_kelas']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="foto">Foto</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputFoto" name="foto" accept="image/*">
                        <label class="custom-file-label" for="inputFoto">Choose file</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat Lengkap</label>
                    <textarea class="form-control" name="alamat" id="alamat" rows="2" placeholder="Alamat Lengkap" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="no_hp">Nomor HP</label>
                        <input type="text" class="form-control" name="no_hp" id="no_hp" placeholder="08123456789" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">Password Akun</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                            <div class="input-group-append">
                                <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
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

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const eyeIcon = document.getElementById("eye-icon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("bi-eye");
                eyeIcon.classList.add("bi-eye-slash");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("bi-eye-slash");
                eyeIcon.classList.add("bi-eye");
            }
        }
    </script>


    <!-- Tabel Manajemen Siswa -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manajemen Siswa</h5>
            <a href="<?= base_url('siswa/export-excel') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-file-excel"></i> Download Excel
            </a>
        </div>

        <div class="card-body">
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
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($siswa as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <a href="<?= base_url('profil/' . ($row['foto'] ?? 'user.png')) ?>" data-lightbox="foto-siswa">
                                        <img src="<?= base_url('profil/' . ($row['foto'] ?? 'user.png')) ?>" alt="Foto" width="100">
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
                                <td>
                                    <div class="d-flex">
                                        <a href="<?= base_url('siswa/edit-siswa/' . $row['id_user']) ?>" class="btn btn-sm btn-warning mr-1" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger btn-hapus"
                                            data-id="<?= $row['id_user'] ?>"
                                            data-nama="<?= $row['nama'] ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $('#inputFoto').on('change', function() {
            // Ambil nama file
            var fileName = $(this).val().split('\\').pop();
            // Tampilkan di label
            $(this).next('.custom-file-label').html(fileName);
        });

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
        document.addEventListener('DOMContentLoaded', function() {
            const hapusButtons = document.querySelectorAll('.btn-hapus');

            hapusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const nama = this.dataset.nama;

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        text: `Data siswa dengan nama "${nama}" akan dihapus!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "<?= base_url('siswa/hapus') ?>/" + id;
                        }
                    });
                });
            });
        });
    </script>
</div>

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
            title: 'Error',
            html: '<?= session()->getFlashdata('swal_error') ?>', // gunakan html
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    </script>
<?php endif; ?>


<?= $this->endSection() ?>