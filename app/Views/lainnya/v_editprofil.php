<?= $this->extend('v_layout') ?>

<?= $this->section('topBar') ?>
<h3 class="ml-4">Form Edit Data Profil</h3>
<?= $this->endSection('topBar') ?>

<?= $this->section('content') ?>
<?php $role = session()->get('role'); ?>
<div class="container mt-4">
    <div class="card shadow-sm border-0 p-4">
        <form action="<?= site_url('/profil/update') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="id_user" value="<?= session()->get('id_user') ?>">
            <!-- Nama -->
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="form-control" value="<?= esc($user['nama']) ?>" required>
            </div>

            <!-- Username (read-only) -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" class="form-control" value="<?= esc($user['username']) ?>" readonly>
            </div>
            <?php if (in_array($role, ['kepala sekolah', 'operator', 'guru', 'guru bk'])): ?>
                <!-- NIP (read-only) -->
                <div class="form-group">
                    <label for="nip">NIP</label>
                    <input type="text" name="nip" id="nip" class="form-control" value="<?= esc($user['nip']) ?>" readonly>
                </div>
            <?php endif; ?>
            <?php if ($role == 'siswa'): ?>
                <!-- NISN (read-only) -->
                <div class="form-group">
                    <label for="nip">NISN</label>
                    <input type="text" name="nisn" id="nisn" class="form-control" value="<?= esc($user['nisn']) ?>" readonly>
                </div>
            <?php endif; ?>
            <!-- TTL -->
            <div class="form-group">
                <label for="ttl">Tempat, Tanggal Lahir</label>
                <input type="text" name="ttl" id="ttl" class="form-control" value="<?= esc($user['ttl']) ?>" required>
            </div>
            <?php if ($role == 'siswa'): ?>
                <!-- Kelas (read-only) -->
                <div class="form-group">
                    <label for="kelas">Kelas</label>
                    <input type="text" id="kelas" class="form-control" value="<?= esc($user['nama_kelas']) ?>" readonly>
                </div>
            <?php endif; ?>

            <!-- Jenis Kelamin -->
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-control" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-Laki" <?= $user['jenis_kelamin'] == 'Laki-Laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $user['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>

            <!-- Agama -->
            <div class="form-group">
                <label for="agama">Agama</label>
                <select name="agama" id="agama" class="form-control" required>
                    <?php
                    $agamaku = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
                    foreach ($agamaku as $agama) {
                        $selected = $user['agama'] == $agama ? 'selected' : '';
                        echo "<option value='$agama' $selected>$agama</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Role (read-only) -->
            <div class="form-group">
                <label for="role">Role</label>
                <input type="text" id="role" class="form-control" value="<?= esc($user['role']) ?>" readonly>
            </div>

            <!-- Nomor HP -->
            <div class="form-group">
                <label for="no_hp">Nomor HP</label>
                <input type="text" name="no_hp" id="no_hp" class="form-control" value="<?= esc($user['no_hp']) ?>" required>
            </div>

            <!-- Alamat -->
            <div class="form-group">
                <label for="alamat">Alamat Lengkap</label>
                <textarea name="alamat" id="alamat" class="form-control" rows="2" required><?= esc($user['alamat']) ?></textarea required>
            </div>

            <?php if (in_array($role, ['kepala sekolah', 'operator', 'guru', 'guru bk'])): ?>
               <!-- Pendidikan Terakhir -->
            <div class="form-group">
                <label for="pendidikan">Pendidikan Terakhir</label>
                <select name="pendidikan" id="pendidikan" class="form-control" required>
                    <?php
                    $pendidikanList = ['SMA/SMK', 'D3', 'S1', 'S2', 'S3'];
                    foreach ($pendidikanList as $pendidikan) {
                        $selected = $user['pendidikan_terakhir'] == $pendidikan ? 'selected' : '';
                        echo "<option value='$pendidikan' $selected>$pendidikan</option>";
                    }
                    ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="foto">Foto</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="inputFoto" name="foto" accept="image/*">
                    <label class="custom-file-label" for="inputFoto"><?= $user['foto'] ?: 'Pilih file' ?></label>
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password Baru (Opsional)</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Kosongkan jika tidak ingin mengubah password">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tombol -->
            <div class="mt-4 d-flex justify-content-between">
                <a href="<?= site_url('/profil') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Toggle password JS -->
<script>
    document.getElementById("togglePassword").addEventListener("click", function() {
        var password = document.getElementById("password");
        var icon = this.querySelector("i");
        if (password.type === "password") {
            password.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            password.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });
</script>

<?= $this->endSection() ?>