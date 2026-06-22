<?= $this->extend('v_layout') ?>
<?= $this->section('topBar') ?>
<h3 class="ml-4">Form Edit Data Penugasan</h3>
<?= $this->endSection('topBar') ?>
<?= $this->section('content') ?>

<!-- Form Edit  Penugasan -->
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
        <form action="<?= base_url('penugasan/update/' . $penugasan['id_tugas']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id_tugas" value="<?= esc($penugasan['id_tugas']) ?>">

            <div class="form-row">
                <?php if ($isGuru): ?>
                    <div class="form-group col-md-4">
                        <label for="nama_guru">Nama Guru</label>
                        <input type="text" class="form-control" id="nama_guru" value="<?= esc($nama_user) ?>" readonly>
                        <input type="hidden" name="id_user" value="<?= esc($id_user) ?>">
                    </div>

                    <div class="form-group col-md-8">
                        <label for="jadwal">Mapel & Kelas</label>
                        <select class="form-control" id="jadwal" name="jadwal_id" required>
                            <option value="">-- Pilih Mapel & Kelas --</option>
                            <?php
                            $tampil = [];
                            foreach ($dataJadwal as $jadwal) {
                                if (!in_array($jadwal['id_jadwal'], $tampil) && $jadwal['id_user'] == $id_user) {
                                    $tampil[] = $jadwal['id_jadwal'];
                                    $selected = $penugasan['id_jadwal'] == $jadwal['id_jadwal'] ? 'selected' : '';
                                    echo '<option value="' . esc($jadwal['id_jadwal']) . '" ' . $selected . '>' . esc($jadwal['nama_mapel'] . ' - ' . $jadwal['nama_kelas']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                <?php elseif ($isAdmin): ?>
                    <div class="form-group col-md-12">
                        <label for="jadwal">Nama Guru - Mapel - Kelas</label>
                        <select class="form-control select2" id="jadwal" name="jadwal_id" required>
                            <option value="">-- Pilih --</option>
                            <?php
                            $tampil = [];
                            foreach ($dataJadwal as $jadwal) {
                                if (!in_array($jadwal['id_jadwal'], $tampil)) {
                                    $tampil[] = $jadwal['id_jadwal'];
                                    $selected = $penugasan['id_jadwal'] == $jadwal['id_jadwal'] ? 'selected' : '';
                                    echo '<option value="' . esc($jadwal['id_jadwal']) . '" ' . $selected . '>' . esc($jadwal['nama_guru'] . ' - ' . $jadwal['nama_mapel'] . ' - ' . $jadwal['nama_kelas']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="judulTugas">Judul Tugas</label>
                    <input type="text" class="form-control" id="judulTugas" name="judul_tugas" value="<?= esc($penugasan['judul_tugas']) ?>" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="deadline">Batas Pengumpulan</label>
                    <input type="datetime-local" class="form-control" id="deadline" name="deadline"
                        value="<?= date('Y-m-d\TH:i', strtotime($penugasan['deadline'])) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan" rows="2"><?= esc($penugasan['catatan']) ?></textarea>
            </div>

            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-primary mr-2">Update Tugas</button>
                <a href="<?= site_url('/penugasan') ?>" class="btn btn-secondary ms-2">Batal</a>
            </div>

        </form>
    </div>
</div>

<?= $this->endSection() ?>