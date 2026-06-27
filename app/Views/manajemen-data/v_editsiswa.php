<?= $this->extend('v_layout') ?>

<?= $this->section('topBar') ?>
<h3 class="ml-4">Form Edit Data Siswa</h3>
<?= $this->endSection('topBar') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <form action="<?= site_url('siswa/update/' . $siswa['id_user']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nama">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama"
                    value="<?= old('nama', $siswa['nama']) ?>" placeholder="Nama Lengkap">
            </div>
            <div class="form-group col-md-6">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="<?= old('username', $siswa['username']) ?>" placeholder="Username">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nisn">NISN</label>
                <input type="text" class="form-control" id="nisn" name="nisn"
                    value="<?= old('nisn', $siswa['nisn']) ?>" placeholder="Nomor Induk Siswa Nasional">
            </div>
            <div class="form-group col-md-6">
                <label for="ttl">Tempat, Tanggal Lahir</label>
                <input type="text" class="form-control" id="ttl" name="ttl"
                    value="<?= old('ttl', $siswa['ttl']) ?>" placeholder="Contoh: Pekalongan, 10 Mei 2008">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="agama">Agama</label>
                <select class="form-control" id="agama" name="agama">
                    <option value="" disabled>-- Pilih Agama --</option>
                    <?php
                    $listAgama = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
                    foreach ($listAgama as $agama) :
                    ?>
                        <option value="<?= $agama ?>" <?= old('agama', $siswa['agama']) == $agama ? 'selected' : '' ?>>
                            <?= $agama ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                    <option value="" disabled>-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-Laki" <?= old('jenis_kelamin', $siswa['jenis_kelamin']) == 'Laki-Laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= old('jenis_kelamin', $siswa['jenis_kelamin']) == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="role">Role</label>
                <input type="text" class="form-control" value="Siswa" disabled>
            </div>
            <div class="form-group col-md-6">
                <label for="kelas">Kelas</label>
                <select class="form-control" id="kelas" name="id_kelas">
                    <option value="" disabled>-- Pilih Kelas --</option>
                    <?php foreach ($kelas as $k) : ?>
                        <option value="<?= $k['id_kelas'] ?>" <?= old('id_kelas', $siswa['id_kelas']) == $k['id_kelas'] ? 'selected' : '' ?>>
                            <?= $k['nama_kelas'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="foto">Foto</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="inputFoto" name="foto" accept="image/*">
                <label class="custom-file-label" for="inputFoto"><?= $siswa['foto'] ?: 'Pilih file' ?></label>
            </div>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat Lengkap</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="2"
                placeholder="Alamat Lengkap"><?= old('alamat', $siswa['alamat']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="no_hp">Nomor HP</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp"
                    value="<?= old('no_hp', $siswa['no_hp']) ?>" placeholder="Contoh: 08123456789">
            </div>
            <div class="form-group col-md-6">
                <label for="password">Password Akun</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Kosongkan jika tidak ingin mengganti">
                    <div class="input-group-append">
                        <span class="input-group-text" onclick="togglePassword()">
                            <i class="bi bi-eye" id="eye-icon"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            <a href="<?= site_url('/siswa') ?>" class="btn btn-secondary ml-2">Batal</a>
        </div>
    </form>


</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#inputFoto').on('change', function() {
        // Ambil nama file
        var fileName = $(this).val().split('\\').pop();
        // Tampilkan di label
        $(this).next('.custom-file-label').html(fileName);
    });

    // Toggle password visibility
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

<?= $this->endSection() ?>