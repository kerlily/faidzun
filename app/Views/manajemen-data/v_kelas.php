<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Form Tambah Kelas -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Form Tambah Kelas</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('kelas/tambah-kelas') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group mb-3">
                        <label for="nama_kelas">Nama Kelas</label>
                        <input type="text" class="form-control text-uppercase" id="nama_kelas" name="nama_kelas" placeholder="Contoh: 7A" required style="text-transform: uppercase;">

                    </div>

                    <div class="form-group mb-3">
                        <label for="wali_kelas">Wali Kelas</label>
                        <select name="wali_kelas" class="form-control" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                            <?php foreach ($guru as $g) : ?>
                                <option value="<?= $g['id_user']; ?>"><?= $g['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Tambah</button>
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
                html: '<?= implode('<br>', session()->getFlashdata('swal_error')) ?>',
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        </script>
    <?php endif; ?>

    <!-- Tabel Kelas Dummy -->
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Daftar Kelas</h5>
            </div>
            <div class="card-body">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        foreach ($kelas as $k) : ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $k['nama_kelas']; ?></td>
                                <td>
                                    <?php
                                    // Ambil nama dari $guru yang sesuai dengan id_user wali_kelas
                                    foreach ($guru as $g) {
                                        if ($g['id_user'] == $k['id_user']) {
                                            echo $g['nama'];
                                            break;
                                        }
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal"
                                        onclick="isiEdit('<?= $k['nama_kelas']; ?>', '<?= $k['id_kelas']; ?>', '<?= $k['id_user']; ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger btn-hapus" data-id="<?= $k['id_kelas']; ?>">
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

<!-- Modal Edit Kelas -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">Edit Kelas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" action="<?= base_url('kelas/edit-kelas') ?>" method="post">
                    <input type="hidden" id="edit_id_kelas" name="id_kelas">
                    <div class="form-group">
                        <label for="edit_nama_kelas">Nama Kelas</label>
                        <input type="text" class="form-control" id="edit_nama_kelas" name="nama_kelas" required style="text-transform: uppercase;">
                    </div>

                    <div class="form-group">
                        <label for="edit_wali_kelas">Wali Kelas</label>
                        <select id="edit_wali_kelas" name="wali_kelas" class="form-control" required>
                            <option value="">-- Pilih Wali Kelas --</option>
                            <?php foreach ($guru as $g): ?>
                                <option value="<?= $g['id_user']; ?>"><?= $g['nama']; ?></option>
                            <?php endforeach; ?>
                        </select>


                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success" form="editForm">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<form id="formHapus" action="<?= base_url('kelas/hapus-kelas') ?>" method="post" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="id_kelas" id="inputIdKelas">
</form>


<!-- Script Modal (bisa diletakkan di bawah halaman) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.btn-hapus');
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: "Data kelas yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Set nilai id_kelas ke form tersembunyi dan submit
                        document.getElementById('inputIdKelas').value = id;
                        document.getElementById('formHapus').submit();
                    }
                });
            });
        });
    });

    function isiEdit(namaKelas, idKelas, idWali) {
        document.getElementById('edit_nama_kelas').value = namaKelas;
        document.getElementById('edit_id_kelas').value = idKelas;

        const select = document.getElementById('edit_wali_kelas');
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value == idWali) {
                select.selectedIndex = i;
                break;
            }
        }
    }
</script>

<?= $this->endSection() ?>