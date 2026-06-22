<?= $this->extend('v_layout') ?>

<?= $this->section('content') ?>

<style>
    .hero-section {
        background: linear-gradient(to right, #3a7bd5, #00d2ff);
        color: white;
        padding: 3rem 1rem;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 2rem;
    }

    .step-icon {
        font-size: 2.2rem;
        margin-right: 10px;
    }

    .accordion .card-header {
        background-color: #f8f9fa;
    }

    @media (max-width: 768px) {
        .step-icon {
            font-size: 1.5rem;
        }
    }
</style>

<div class="container mt-4">

    <!-- Hero Section -->
    <div class="hero-section shadow-sm">
        <h2 class="mb-3">Panduan Penggunaan Website</h2>
        <p class="lead">Petunjuk lengkap untuk menggunakan sistem LMS ini sesuai peran masing-masing.</p>
    </div>

    <!-- Accordion Peran -->
    <div class="accordion mb-4" id="panduanAccordion">

        <!-- ADMIN / OPERATOR -->
        <div class="card">
            <div class="card-header" id="adminHeader">
                <h2 class="mb-0">
                    <button class="btn btn-link text-primary" type="button" data-toggle="collapse" data-target="#adminPanduan" aria-expanded="true" aria-controls="adminPanduan">
                        <i class="step-icon fa fa-user-shield"></i> Panduan untuk Kepala Sekolah & Operator
                    </button>
                </h2>
            </div>
            <div id="adminPanduan" class="collapse show" aria-labelledby="adminHeader" data-parent="#panduanAccordion">
                <div class="card-body">
                    <ol>
                        <li><strong>Login:</strong> Masukkan username & password yang telah dibuat admin sebelumnya.</li>
                        <li><strong>Manajemen Guru:</strong> Tambahkan/ubah data guru melalui menu <em>Guru</em> (isi NIP, nama, username, password, pendidikan).</li>
                        <li><strong>Manajemen Siswa:</strong> Tambahkan/ubah data siswa sesuai kelasnya melalui menu <em>Siswa</em>.</li>
                        <li><strong>Mata Pelajaran:</strong> Tambahkan mata pelajaran di menu <em>Mapel</em> dan hubungkan dengan guru pengampu.</li>
                        <li><strong>Jadwal:</strong> Atur jadwal pelajaran setiap kelas melalui menu <em>Jadwal</em>.</li>
                        <li><strong>Absensi:</strong> Cek rekap absensi siswa pada menu <em>Riwayat Absensi</em>. Bisa filter berdasarkan kelas & mapel.</li>
                        <li><strong>Tugas:</strong> Pantau penugasan siswa di menu <em>Penugasan</em>.</li>
                        <li><strong>Profil:</strong> Ubah data profil admin/operator di menu <em>Profil</em>.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- GURU -->
        <div class="card">
            <div class="card-header" id="guruHeader">
                <h2 class="mb-0">
                    <button class="btn btn-link collapsed text-primary" type="button" data-toggle="collapse" data-target="#guruPanduan" aria-expanded="false" aria-controls="guruPanduan">
                        <i class="step-icon fa fa-chalkboard-teacher"></i> Panduan untuk Guru
                    </button>
                </h2>
            </div>
            <div id="guruPanduan" class="collapse" aria-labelledby="guruHeader" data-parent="#panduanAccordion">
                <div class="card-body">
                    <ol>
                        <li><strong>Login:</strong> Gunakan akun guru yang diberikan oleh admin/operator.</li>
                        <li><strong>Lihat Jadwal Mengajar:</strong> Buka menu <em>Jadwal</em> untuk melihat jadwal mengajar.</li>
                        <li><strong>Absensi:</strong> Di menu <em>Absensi</em>, pilih kelas & mapel sesuai jadwal lalu tandai status kehadiran siswa.</li>
                        <li><strong>Edit Absensi:</strong> Bisa ubah status absensi siswa melalui menu <em>Riwayat Absensi</em>.</li>
                        <li><strong>Berikan Tugas:</strong> Buka menu <em>Penugasan</em> → klik “Tambah Tugas” → isi deskripsi, file (opsional), dan batas waktu.</li>
                        <li><strong>Profil:</strong> Ubah data diri atau password di menu <em>Profil</em>.</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- SISWA -->
        <div class="card">
            <div class="card-header" id="siswaHeader">
                <h2 class="mb-0">
                    <button class="btn btn-link collapsed text-primary" type="button" data-toggle="collapse" data-target="#siswaPanduan" aria-expanded="false" aria-controls="siswaPanduan">
                        <i class="step-icon fa fa-user-graduate"></i> Panduan untuk Siswa
                    </button>
                </h2>
            </div>
            <div id="siswaPanduan" class="collapse" aria-labelledby="siswaHeader" data-parent="#panduanAccordion">
                <div class="card-body">
                    <ol>
                        <li><strong>Login:</strong> Masukkan username & password yang diberikan sekolah.</li>
                        <li><strong>Lihat Jadwal:</strong> Cek jadwal pelajaran di menu <em>Jadwal</em>.</li>
                        <li><strong>Absensi:</strong> Jika diaktifkan, masuk ke menu <em>Absensi</em> dan klik tombol hadir sesuai jadwal.</li>
                        <li><strong>Lihat Tugas:</strong> Buka menu <em>Penugasan</em> untuk melihat daftar tugas dari guru.</li>
                        <li><strong>Kumpulkan Tugas:</strong> Klik tugas → unggah file jawaban sebelum batas waktu.</li>
                        <li><strong>Profil:</strong> Ubah data pribadi atau password di menu <em>Profil</em>.</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>