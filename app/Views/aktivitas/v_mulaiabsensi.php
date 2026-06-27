<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h4 class="mb-4">Mulai Absensi</h4>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Silakan lakukan absensi sebelum sesi berakhir. Pastikan mengisi materi yang diajarkan hari ini.
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php
            $mapel = $jadwal['nama_mapel'];
            $kelas = $jadwal['nama_kelas'];
            $guru = $jadwal['nama_guru'];
            $jamMulai = date('H:i', strtotime($jadwal['jam_mulai']));
            $jamSelesai = date('H:i', strtotime($jadwal['jam_selesai']));
            ?>

            <form action="<?= site_url('absensi/simpan') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id_jadwal" value="<?= $jadwal['id_jadwal'] ?>">
                <!-- Informasi Umum dan Materi -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Informasi Umum</h5>

                        <div class="form-row">
                            <!-- Tanggal -->
                            <div class="form-group col-md-4">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" value="<?= $tanggal ?>" readonly>
                            </div>
                        </div>

                        <!-- Materi -->
                        <div class="form-group">
                            <label for="materi">Materi yang Diajarkan <span class="text-danger">*</span></label>
                            <textarea name="materi" id="materi" class="form-control" rows="3" required placeholder="Contoh: Menyusun teks prosedur..."></textarea>
                        </div>
                    </div>
                </div>


                <!-- Daftar Siswa -->
                <div class="mb-3">
                    <label>Daftar Siswa & Kehadiran:</label>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Kelamin</th>
                                    <th style="width: 200px;">Keterangan Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                foreach ($siswa as $s): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($s['nisn']) ?></td>
                                        <td><?= esc($s['nama']) ?></td>
                                        <td><?= esc($s['jenis_kelamin']) ?></td>
                                        <td>
                                            <select name="kehadiran[<?= $s['id_user'] ?>]" class="form-control" required>
                                                <option value="Masuk" selected>Masuk</option>
                                                <option value="Izin">Izin</option>
                                                <option value="Sakit">Sakit</option>
                                                <option value="Alpa">Alpa</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach ?>

                            </tbody>
                        </table>

                    </div>
                </div>
                <!-- PERINGATAN -->
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    Mohon periksa kembali data absensi sebelum menekan tombol <strong>Submit</strong>.
                    Pastikan semua keterangan kehadiran dan materi sudah diisi dengan benar.
                </div>

                <!-- Tombol -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
                <a href="<?= site_url('absensi') ?>" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>