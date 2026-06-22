<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<?php if (session()->getFlashdata('swal_error')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: '<?= session()->getFlashdata('swal_error') ?><br><?= implode('<br>', (array) session()->getFlashdata('errors')) ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('swal_success')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            text: '<?= session()->getFlashdata('swal_success') ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
<?php endif; ?>
<div class="container mt-4">
    <h4 class="mb-4">Profil Pengguna</h4>

    <div class="card shadow-sm border-0 p-4">
        <div class="row">
            <!-- Foto profil -->
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <img src="<?= base_url('profil/' . ($user['foto'] ?? 'user.png')) ?>"
                    alt="Foto Profil"
                    class="img-fluid rounded-circle"
                    style="width: 160px; height: 160px; object-fit: cover;">
                <h5 class="mt-3 mb-1"><?= esc($user['nama']) ?></h5>
                <p class="text-muted"><?= esc($user['role']) ?></p>
            </div>

            <!-- Informasi profil -->
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <th>Username</th>
                                <td>: <?= esc($user['username']) ?></td>
                            </tr>
                            <?php if (in_array($role, ['kepala sekolah', 'operator', 'guru', 'guru bk'])): ?>
                                <tr>
                                    <th>NIP</th>
                                    <td>: <?= esc($user['nip'] ?? '-') ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($role == 'siswa'): ?>
                                <tr>
                                    <th>NISN</th>
                                    <td>: <?= esc($user['nisn'] ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>: <?= esc($user['nama_kelas'] ?? '-') ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>: <?= esc($user['jenis_kelamin']) ?></td>
                            </tr>
                            <tr>
                                <th>Agama</th>
                                <td>: <?= esc($user['agama']) ?></td>
                            </tr>
                            <tr>
                                <th>Tempat, Tanggal Lahir</th>
                                <td>: <?= esc($user['ttl']) ?></td>
                            </tr>
                            <tr>
                                <th>No. HP</th>
                                <td>: <?= esc($user['no_hp']) ?></td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>: <?= esc($user['alamat']) ?></td>
                            </tr>
                            <?php if (in_array($role, ['kepala sekolah', 'operator', 'guru', 'guru bk'])): ?>
                                <tr>
                                    <th>Pendidikan Terakhir</th>
                                    <td>: <?= esc($user['pendidikan_terakhir'] ?? '-') ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <hr>
                <a href="<?= site_url('/profil/edit-profil') ?>" class="btn btn-outline-warning btn-block btn-sm">
                    <i class="fa fa-edit"></i> Edit Profil
                </a>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>