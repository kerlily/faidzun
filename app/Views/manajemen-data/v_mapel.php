<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Form Tambah Mata Pelajaran -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Form Tambah Mata Pelajaran</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('mapel/tambah-mapel') ?>" method="post">
                    <div class="form-group mb-3">
                        <label for="kode_mapel">Kode Mata Pelajaran</label>
                        <input type="text" name="kode_mapel" class="form-control" id="kode_mapel" placeholder="Contoh: MTH001" required style="text-transform: uppercase;">
                    </div>

                    <div class="form-group mb-3">
                        <label for="nama_mapel">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" placeholder="Contoh: Matematika" id="nama_mapel" class="form-control" style="text-transform: uppercase;">

                    </div>

                    <button type=" submit" class="btn btn-primary">Tambah</button>
                </form>
            </div>
        </div>
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
                html: <?= json_encode(
                            is_array(session()->getFlashdata('swal_error'))
                                ? implode('<br>', session()->getFlashdata('swal_error'))
                                : session()->getFlashdata('swal_error')
                        ) ?>,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        </script>
    <?php endif; ?>


    <!-- Tabel Mata Pelajaran Dummy -->
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Daftar Mata Pelajaran</h5>
            </div>
            <div class="card-body">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Mata Pelajaran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($mapel as $m): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($m['kode_mapel']) ?></td>
                                <td><?= esc($m['nama_mapel']) ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal"
                                        onclick="isiEditMapel('<?= $m['kode_mapel'] ?>', '<?= $m['nama_mapel'] ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="konfirmasiHapus('<?= $m['kode_mapel'] ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Mata Pelajaran -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editMapelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editMapelLabel">Edit Mata Pelajaran</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editMapelForm" action="<?= base_url('mapel/edit-mapel') ?>" method="post">
                    <input type="hidden" name="kode_mapel_lama" id="kode_mapel_lama"> <!-- disimpan jika kode mapel diubah -->
                    <div class="form-group mb-3">
                        <label for="edit_kode_mapel">Kode</label>
                        <input type="text" class="form-control" name="kode_mapel" id="edit_kode_mapel" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_nama_mapel">Nama Mata Pelajaran</label>
                        <input type="text" class="form-control" name="nama_mapel" id="edit_nama_mapel" required style="text-transform: uppercase;">
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
            </div>
            </form>
        </div>
    </div>
</div>


<!-- Script JS untuk Modal -->
<script>
    function isiEditMapel(kode, nama) {
        document.getElementById('edit_kode_mapel').value = kode;
        document.getElementById('edit_nama_mapel').value = nama;
        document.getElementById('kode_mapel_lama').value = kode;
    }


    function konfirmasiHapus(kodeMapel) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data mata pelajaran dengan kode " + kodeMapel + " akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL hapus
                window.location.href = "/mapel/hapus/" + kodeMapel;
            }
        });
    }
</script>
<?= $this->endSection() ?>