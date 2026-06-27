<?= $this->extend('v_layout') ?>

<?= $this->section('topBar') ?>
<h3 class="ml-4">Form Edit Data Guru</h3>
<?= $this->endSection('topBar') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <form action="<?= site_url('guru/update/' . $guru['id_user']) ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id_user" value="<?= esc($guru['id_user']) ?>">

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" id="nama" value="<?= esc($guru['nama']) ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" id="username" value="<?= esc($guru['username']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nip">NIP</label>
                <input type="text" name="nip" class="form-control" id="nip" value="<?= esc($guru['nip']) ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="ttl">Tempat, Tanggal Lahir</label>
                <input type="text" name="ttl" class="form-control" id="ttl" value="<?= esc($guru['ttl']) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="role">Role</label>
                <select name="role" class="form-control" id="role" required>
                    <option disabled>-- Pilih Role --</option>
                    <?php $roles = ['Kepala Sekolah', 'Guru', 'Guru BK', 'Operator']; ?>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role ?>" <?= $guru['role'] === strtolower($role) ? 'selected' : '' ?>><?= $role ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="pendidikan">Pendidikan Terakhir</label>
                <select name="pendidikan" class="form-control" id="pendidikan" required>
                    <?php $pendidikan = ['SMA/SMK', 'D3', 'S1', 'S2', 'S3']; ?>
                    <?php foreach ($pendidikan as $p): ?>
                        <option value="<?= $p ?>" <?= $guru['pendidikan_terakhir'] === $p ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select name="gender" class="form-control" id="jenis_kelamin" required>
                    <option disabled>-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-Laki" <?= $guru['jenis_kelamin'] === 'Laki-Laki' ? 'selected' : '' ?>>Laki-Laki</option>
                    <option value="Perempuan" <?= $guru['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="agama">Agama</label>
                <select name="agama" class="form-control" id="agama" required>
                    <?php $agamanya = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']; ?>
                    <?php foreach ($agamanya as $a): ?>
                        <option value="<?= $a ?>" <?= $guru['agama'] === $a ? 'selected' : '' ?>><?= $a ?></option>
                    <?php endforeach ?>
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
            <textarea required name="alamat" class="form-control" id="alamat" rows="2"><?= esc($guru['alamat']) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="no_hp">Nomor HP</label>
                <input type="text" name="no_hp" class="form-control" id="no_hp" value="<?= esc($guru['no_hp']) ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="password">Password Akun</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Isi jika ingin mengubah password">
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
            <a href="<?= site_url('/guru') ?>" class="btn btn-secondary ml-2">Batal</a>
        </div>
    </form>


</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputFoto = document.getElementById('inputFoto');
        const labelFoto = document.querySelector('label[for="inputFoto"]');

        // Set nama file lama ke label saat form dimuat
        <?php if (!empty($guru['foto'])): ?>
            labelFoto.textContent = "<?= esc($guru['foto']) ?>";
        <?php endif ?>

        // Update nama file jika user memilih file baru
        inputFoto.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
            labelFoto.textContent = fileName;
        });
    });
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