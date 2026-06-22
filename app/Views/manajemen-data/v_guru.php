<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white ">
            <h5 class="mb-0">Form Tambah Guru</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('guru/tambah-guru') ?>" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nip">NIP</label>
                        <input type="text" class="form-control" id="nip" name="nip" placeholder="Nomor Induk Pegawai" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="ttl">Tempat, Tanggal Lahir</label>
                        <input type="text" class="form-control" id="ttl" name="ttl" placeholder="Contoh: Pekalongan, 10 Mei 1985" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="gender">Jenis Kelamin</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="" disabled selected>-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-Laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="agama">Agama</label>
                        <select class="form-control" id="agama" name="agama" required>
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
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <option value="Kepala Sekolah">Kepala Sekolah</option>
                            <option value="Guru">Guru</option>
                            <option value="Guru BK">Guru BK</option>
                            <option value="Operator">Operator</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pendidikan">Pendidikan Terakhir</label>
                        <select class="form-control" id="pendidikan" name="pendidikan" required>
                            <option value="" disabled selected>-- Pilih Pendidikan --</option>
                            <option value="SMA/SMK">SMA/SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
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
                    <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Alamat Lengkap" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="no_hp">Nomor HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Contoh: 08123456789" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password">Password Akun</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <div class="input-group-append">
                                <span class="input-group-text" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="eye-icon"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-2">Tambah</button>
            </form>


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
                        title: '<?= session()->getFlashdata('swal_error') ?>',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                    });
                </script>
            <?php endif; ?>


            <script>
                function togglePassword() {
                    const password = document.getElementById("password");
                    const eye = document.getElementById("eye-icon");
                    if (password.type === "password") {
                        password.type = "text";
                        eye.classList.remove("bi-eye");
                        eye.classList.add("bi-eye-slash");
                    } else {
                        password.type = "password";
                        eye.classList.remove("bi-eye-slash");
                        eye.classList.add("bi-eye");
                    }
                }
            </script>

        </div>
    </div>

    <!-- Tabel Manajemen Guru -->
    <div class="card">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manajemen Guru</h5>
            <a href="<?= base_url('guru/export-excel') ?>" class="btn btn-primary btn-sm">
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
                            <th>Username</th>
                            <th>Jenis Kelamin</th>
                            <th>Agama</th>
                            <th>NIP</th>
                            <th>TTL</th>
                            <th>Alamat</th>
                            <th>Role</th>
                            <th>Pendidikan</th>
                            <th>No. HP</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($guru as $g): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <?php
                                    $foto = $g['foto'] ?? 'user.png';
                                    $url = base_url('profil/' . $foto);
                                    ?>
                                    <a href="<?= $url ?>" data-lightbox="foto-guru">
                                        <img src="<?= $url ?>" alt="Foto" width="100">
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
                                <td>
                                    <div class="d-flex">
                                        <a href="<?= base_url('guru/edit-guru/' . $g['id_user']) ?>" class="btn btn-sm btn-warning mr-1" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <!-- Tombol Hapus -->
                                        <button type="button" class="btn btn-sm btn-danger btn-hapus"
                                            data-id="<?= $g['id_user'] ?>"
                                            data-nama="<?= esc($g['nama']) ?>"
                                            title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                        <!-- Form tersembunyi untuk submit -->
                                        <form id="form-hapus-<?= $g['id_user'] ?>" action="<?= base_url('guru/hapus/' . $g['id_user']) ?>" method="post" style="display:none;">
                                            <?= csrf_field() ?>
                                        </form>

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
        document.querySelectorAll('.btn-hapus').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: `Data guru atas nama ${nama} akan dihapus permanen.`,
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

</div>
<?= $this->endSection() ?>