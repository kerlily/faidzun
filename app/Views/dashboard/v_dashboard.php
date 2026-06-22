<?= $this->extend('v_layout') ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <?php $role = session()->get('role'); ?>
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

    <!-- Logo Sekolah -->
    <div class="text-center mb-4">
        <img src="<?= base_url('logo/logo.png') ?>" alt="Logo SMP 10 Pekalongan" class="img-fluid" style="max-height: 120px;">
        <h4 class="mt-2 fw-bold">SMP Negeri 10 Pekalongan</h4>
    </div>

    <!-- Visi Misi -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Visi dan Misi Sekolah</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Visi:</h6>
                    <p>“Menjadi sekolah yang unggul dalam prestasi, berkarakter, dan berwawasan lingkungan.”</p>

                    <h6 class="fw-bold">Misi:</h6>
                    <ul>
                        <li>Meningkatkan kualitas pembelajaran dan prestasi siswa.</li>
                        <li>Menanamkan nilai-nilai kejujuran, tanggung jawab, dan toleransi.</li>
                        <li>Mewujudkan lingkungan sekolah yang bersih, hijau, dan sehat.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 pb-5">
        <?php
        $stats = [
            ['title' => 'Total Siswa', 'value' => $totalSiswa, 'color' => 'primary'],
            ['title' => 'Total Guru', 'value' => $totalGuru, 'color' => 'success'],
            ['title' => 'Total Kelas', 'value' => $totalKelas, 'color' => 'info'],
            ['title' => 'Mata Pelajaran', 'value' => $totalMapel, 'color' => 'warning'],
        ];
        foreach ($stats as $stat): ?>
            <div class="col mb-3">
                <div class="card shadow-sm border-start border-4 border-<?= $stat['color'] ?> h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="card-title"><?= $stat['title'] ?></h6>
                            <h4 class="fw-bold text-<?= $stat['color'] ?>"><?= $stat['value'] ?></h4>
                        </div>
                        <small class="text-muted mt-auto">Data <?= strtolower($stat['title']) ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <!-- Galeri Sekolah -->
    <div class="container my-2">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Galeri Sekolah</h5>
                <?php if (in_array($role, ['kepala sekolah', 'operator'])): ?>
                    <button class="btn btn-light btn-sm" data-toggle="modal" data-target="#modalUploadFoto">
                        <i class="fas fa-plus me-1"></i> Tambah Foto
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($galeri)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-image fa-3x mb-3"></i>
                        <p>Belum ada foto di galeri</p>
                    </div>
                <?php else: ?>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                        <?php foreach ($galeri as $i => $foto): ?>
                            <div class="col mb-2" id="foto<?= $i ?>">
                                <div class="position-relative shadow-sm border rounded overflow-hidden" style="z-index: 1;">
                                    <img src="<?= base_url('galeri/' . $foto['foto']) ?>"
                                        alt="Foto Galeri"
                                        class="img-fluid w-100"
                                        style="height: 200px; object-fit: cover; cursor: zoom-in;"
                                        onclick="openZoom('<?= base_url('galeri/' . $foto['foto']) ?>')">
                                    <?php if (in_array($role, ['kepala sekolah', 'operator'])): ?>
                                        <button type="button"
                                            class="btn btn-danger btn-sm position-absolute"
                                            style="top: 10px; right: 10px; z-index: 10;"
                                            onclick="konfirmasiHapus('<?= base_url('/dashboard/hapus-galeri/' . $foto['id_galeri']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>


        </div>
    </div>

    <!-- Modal Zoom -->
    <div class="modal fade" id="zoomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0 p-0">
                <img id="zoomImage" class="img-fluid rounded" alt="Zoom Image">
            </div>
        </div>
    </div>

    <!-- Modal Upload Foto -->
    <div class="modal fade" id="modalUploadFoto" tabindex="-1" role="dialog" aria-labelledby="modalUploadFotoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalUploadFotoLabel">Upload Galeri Sekolah</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formUploadFoto" action="<?= base_url('dashboard/tambah-galeri') ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="inputFoto">Pilih Foto</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputFoto" name="foto" accept="image/*" required>
                                <label class="custom-file-label" for="inputFoto">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function openZoom(src) {
            document.getElementById('zoomImage').src = src;
            const modal = new bootstrap.Modal(document.getElementById('zoomModal'));
            modal.show();
        }

        $('#inputFoto').on('change', function() {
            // Ambil nama file
            var fileName = $(this).val().split('\\').pop();
            // Tampilkan di label
            $(this).next('.custom-file-label').html(fileName);
        });

        function konfirmasiHapus(url) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Foto ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
</div>
<?= $this->endSection() ?>