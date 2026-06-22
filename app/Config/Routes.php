<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =============================================
// PUBLIC (tidak perlu login)
// =============================================
$routes->get('/', 'ValidationController::index');
$routes->get('/login', 'ValidationController::index');
$routes->post('/login/proses', 'ValidationController::proses');
$routes->get('/logout', 'ValidationController::logout');

// =============================================
// SEMUA ROLE YANG SUDAH LOGIN
// =============================================
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('/dashboard', 'DashboardController::index');

    // Profil (semua role)
    $routes->get('/profil', 'DashboardController::profil');
    $routes->get('/profil/edit-profil', 'DashboardController::editprofil');
    $routes->post('/profil/update', 'DashboardController::updateprofil');

    // Panduan (semua role)
    $routes->get('/panduan', 'DashboardController::panduan');

    // =============================================
    // GURU & GURU BK (+ kepala sekolah & operator sebagai view)
    // =============================================

    // Jadwal mengajar — view untuk guru, kepala, operator
    $routes->get('/jadwal-mengajar', 'DashboardController::jadwalmengajar', ['filter' => 'role:kepala sekolah,operator,guru,guru bk']);

    // Absensi — hanya guru yang bisa mulai absen
    $routes->get('/absensi', 'AbsensiController::index', ['filter' => 'role:kepala sekolah,operator,guru,guru bk']);
    $routes->get('/absensi/mulai-absensi/(:num)', 'AbsensiController::mulai/$1', ['filter' => 'role:guru,guru bk']);
    $routes->post('/absensi/simpan', 'AbsensiController::simpan', ['filter' => 'role:guru,guru bk']);
    $routes->get('/absensi/export', 'AbsensiController::exportExcel', ['filter' => 'role:kepala sekolah,operator']);

    // Riwayat absensi
    $routes->get('/riwayat-absensi', 'AbsensiController::riwayat', ['filter' => 'role:kepala sekolah,operator,guru,guru bk,siswa']);
    $routes->post('/riwayat-absensi/edit-absensi', 'AbsensiController::editAbsensi', ['filter' => 'role:kepala sekolah,operator']);
    $routes->post('/data-siswa/hapus_kelas', 'AbsensiController::hapusPerKelas', ['filter' => 'role:kepala sekolah,operator']);

    // Penugasan
    $routes->get('/penugasan', 'PenugasanController::index', ['filter' => 'role:kepala sekolah,operator,guru,guru bk']);
    $routes->post('/penugasan/tambah-penugasan', 'PenugasanController::tambah', ['filter' => 'role:operator,guru,guru bk']);
    $routes->get('/penugasan/edit-penugasan/(:num)', 'PenugasanController::edit/$1', ['filter' => 'role:operator,guru,guru bk']);
    $routes->post('/penugasan/update/(:num)', 'PenugasanController::update/$1', ['filter' => 'role:operator,guru,guru bk']);
    $routes->post('/penugasan/hapus/(:num)', 'PenugasanController::hapus/$1', ['filter' => 'role:operator,guru,guru bk']);

    // Tugas siswa (view only untuk kepala sekolah, operator, siswa)
    $routes->get('/tugas-saya', 'PenugasanController::tugasSaya', ['filter' => 'role:kepala sekolah,operator,siswa']);

    // =============================================
    // OPERATOR SAJA (bisa CRUD semua data)
    // =============================================

    // Guru — operator bisa tambah/edit/hapus
    $routes->get('/guru', 'ManajemenController::guru', ['filter' => 'role:kepala sekolah,operator']);
    $routes->post('/guru/tambah-guru', 'ManajemenController::tambahGuru', ['filter' => 'role:operator']);
    $routes->get('/guru/edit-guru/(:num)', 'ManajemenController::editGuru/$1', ['filter' => 'role:operator']);
    $routes->post('/guru/update/(:num)', 'ManajemenController::updateGuru/$1', ['filter' => 'role:operator']);
    $routes->post('/guru/hapus/(:num)', 'ManajemenController::hapusGuru/$1', ['filter' => 'role:operator']);
    $routes->get('/guru/export-excel', 'ManajemenController::exportGuruExcel', ['filter' => 'role:kepala sekolah,operator']);

    // Siswa
    $routes->get('/siswa', 'ManajemenController::siswa', ['filter' => 'role:kepala sekolah,operator']);
    $routes->post('/siswa/tambah-siswa', 'ManajemenController::tambahSiswa', ['filter' => 'role:operator']);
    $routes->get('/siswa/edit-siswa/(:num)', 'ManajemenController::editSiswa/$1', ['filter' => 'role:operator']);
    $routes->post('/siswa/update/(:num)', 'ManajemenController::updateSiswa/$1', ['filter' => 'role:operator']);
    $routes->post('/siswa/hapus/(:num)', 'ManajemenController::hapusSiswa/$1', ['filter' => 'role:operator']);
    $routes->get('/siswa/export-excel', 'ManajemenController::exportSiswaExcel', ['filter' => 'role:kepala sekolah,operator']);
    $routes->post('/siswa/import', 'ManajemenController::importSiswa', ['filter' => 'role:operator']);

    // Kelas
    $routes->get('/kelas', 'ManajemenController::kelas', ['filter' => 'role:kepala sekolah,operator']);
    $routes->post('/kelas/tambah-kelas', 'ManajemenController::tambahKelas', ['filter' => 'role:operator']);
    $routes->post('/kelas/edit-kelas', 'ManajemenController::editKelas', ['filter' => 'role:operator']);
    $routes->post('/kelas/hapus-kelas', 'ManajemenController::hapusKelas', ['filter' => 'role:operator']);

    // Mapel
    $routes->get('/mapel', 'ManajemenController::mapel', ['filter' => 'role:kepala sekolah,operator']);
    $routes->post('/mapel/tambah-mapel', 'ManajemenController::tambahMapel', ['filter' => 'role:operator']);
    $routes->get('/mapel/hapus/(:any)', 'ManajemenController::hapusMapel/$1', ['filter' => 'role:operator']);
    $routes->post('/mapel/edit-mapel', 'ManajemenController::editMapel', ['filter' => 'role:operator']);

    // Jadwal mengajar CRUD
    $routes->post('/jadwal-mengajar/tambah-jadwal-mengajar', 'ManajemenController::tambahJadwal', ['filter' => 'role:operator']);
    $routes->post('/jadwal-mengajar/hapus', 'ManajemenController::hapusJadwal', ['filter' => 'role:operator']);

    // Galeri (dashboard)
    $routes->post('/dashboard/tambah-galeri', 'ManajemenController::tambahGaleri', ['filter' => 'role:operator']);
    $routes->get('/dashboard/hapus-galeri/(:num)', 'ManajemenController::hapusGaleri/$1', ['filter' => 'role:operator']);
    $routes->get('/dashboard/export-absen', 'ManajemenController::exportAbsenExcel', ['filter' => 'role:kepala sekolah,operator']);
});