<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
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
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Penugasan</h1>

    <!-- Form Tambah Penugasan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Buat Penugasan</h6>
        </div>
        <div class="card-body">
            <?php
            $session = session();
            $role = $session->get('role');
            $id_user = $session->get('id_user');
            $nama_user = $session->get('nama');

            $isGuru = in_array($role, ['guru', 'guru bk']);
            $isAdmin = in_array($role, ['kepala sekolah', 'operator']);
            ?>
            <form action="<?= base_url('penugasan/tambah-penugasan') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-row">
                    <!-- Field jadwal: role-based -->
                    <?php if ($isGuru): ?>
                        <!-- Nama Guru -->
                        <div class="form-group col-md-4">
                            <label for="nama_guru">Nama Guru</label>
                            <input type="text" class="form-control" id="nama_guru" value="<?= esc($nama_user) ?>" readonly>
                            <input type="hidden" name="id_user" value="<?= esc($id_user) ?>">
                        </div>

                        <!-- Dropdown Mapel - Kelas -->
                        <div class="form-group col-md-8">
                            <label for="jadwal">Mapel & Kelas</label>
                            <select class="form-control" id="jadwal" name="jadwal_id" required>
                                <option value="">-- Pilih Mapel & Kelas --</option>
                                <?php
                                $tampil = [];
                                foreach ($dataJadwal as $jadwal) {
                                    if (!in_array($jadwal['id_jadwal'], $tampil) && $jadwal['id_user'] == $id_user) {
                                        $tampil[] = $jadwal['id_jadwal'];
                                        echo '<option value="' . esc($jadwal['id_jadwal']) . '">' . esc($jadwal['nama_mapel'] . ' - ' . $jadwal['nama_kelas']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                    <?php elseif ($isAdmin): ?>
                        <!-- Dropdown Gabungan Nama Guru - Mapel - Kelas -->
                        <div class="form-group col-md-12">
                            <label for="jadwal">Nama Guru - Mapel - Kelas</label>
                            <select class="form-control select2" id="jadwal" name="jadwal_id" required>
                                <option value="">-- Pilih --</option>
                                <?php
                                $tampil = [];
                                foreach ($dataJadwal as $jadwal) {
                                    if (!in_array($jadwal['id_jadwal'], $tampil)) {
                                        $tampil[] = $jadwal['id_jadwal'];
                                        echo '<option value="' . esc($jadwal['id_jadwal']) . '">' . esc($jadwal['nama_guru'] . ' - ' . $jadwal['nama_mapel'] . ' - ' . $jadwal['nama_kelas']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tambahan form input tugas -->
                <div class="form-row">
                    <!-- Judul Tugas -->
                    <div class="form-group col-md-6">
                        <label for="judulTugas">Judul Tugas</label>
                        <input type="text" class="form-control" id="judulTugas" name="judul_tugas" placeholder="Contoh: Tugas Membuat Puisi" required>
                    </div>

                    <!-- Deadline -->
                    <div class="form-group col-md-6">
                        <label for="deadline">Batas Pengumpulan</label>
                        <input type="datetime-local" class="form-control" id="deadline" name="deadline" required>
                    </div>
                </div>

                <!-- Catatan -->
                <div class="form-group">
                    <label for="catatan">Catatan</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Contoh: Kumpulkan di meja kantor saya."></textarea>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary">Simpan Tugas</button>
            </form>

        </div>
    </div>

    <!-- Tabel Penugasan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penugasan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Judul Tugas</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Nama Guru</th>
                            <th>Batas Pengumpulan</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semuaDataJadwal as $i => $tugas): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($tugas['judul_tugas']) ?></td>
                                <td><?= esc($tugas['nama_mapel']) ?></td>
                                <td><?= esc($tugas['nama_kelas']) ?></td>
                                <td><?= esc($tugas['nama_guru']) ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($tugas['deadline'])) ?></td>
                                <td><?= esc($tugas['catatan']) ?></td>
                                <td>
                                    <div class="d-flex">
                                        <a href="<?= site_url('penugasan/edit-penugasan/' . $tugas['id_tugas']) ?>" class="btn btn-sm btn-warning mr-1"><i class="fas fa-edit"></i></a>
                                        <!-- Tombol -->
                                        <button type="button" class="btn btn-sm btn-danger btn-hapus"
                                            data-id="<?= $tugas['id_tugas'] ?>"
                                            data-nama="<?= esc($tugas['judul_tugas']) ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <!-- Form -->
                                        <form id="form-hapus-<?= $tugas['id_tugas'] ?>" action="<?= site_url('penugasan/hapus/' . $tugas['id_tugas']) ?>" method="post" style="display: none;">
                                            <?= csrf_field() ?>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <!-- SweetAlert2 CDN -->
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                    <!-- Script -->
                    <script>
                        document.querySelectorAll('.btn-hapus').forEach(button => {
                            button.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                const nama = this.getAttribute('data-nama');

                                Swal.fire({
                                    title: 'Yakin ingin menghapus?',
                                    text: `Data penugasan "${nama}" akan dihapus permanen.`,
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
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const dataJadwal = <?= json_encode($dataJadwal) ?>;
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Pilih --",
            allowClear: true
        });
    });
</script>

<?= $this->endSection() ?>