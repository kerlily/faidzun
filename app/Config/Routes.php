<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ValidationController::index');
$routes->post('login/proses', 'ValidationController::proses');
$routes->get('logout', 'ValidationController::logout');


// $routes->group('', ['filter' => 'auth'], function ($routes) {
//     $routes->get('/dashboard', 'DashboardController::index');
//     $routes->get('/guru', 'DashboardController::guru');
//     $routes->post('/guru/tambah-guru', 'KepalaController::tambahguru');
//     $routes->post('/guru/hapus/(:num)', 'KepalaController::hapusguru/$1');
//     $routes->get('/guru/edit-guru/(:num)', 'DashboardController::editguru/$1');
//     $routes->post('guru/update/(:num)', 'KepalaController::updateguru/$1');
//     $routes->get('/kelas', 'DashboardController::kelas');
//     $routes->post('/kelas/tambah-kelas', 'KepalaController::tambahkelas');
//     $routes->post('/kelas/hapus-kelas', 'KepalaController::hapuskelas');
//     $routes->post('kelas/edit-kelas', 'KepalaController::editkelas');
//     $routes->get('/siswa', 'DashboardController::siswa');
//     $routes->post('/siswa/tambah-siswa', 'KepalaController::tambahsiswa');
//     $routes->get('siswa/edit-siswa/(:num)', 'DashboardController::editsiswa/$1');
//     $routes->post('siswa/update/(:num)', 'KepalaController::updatesiswa/$1');
//     $routes->get('siswa/hapus/(:num)', 'KepalaController::hapussiswa/$1');
//     $routes->get('/mapel', 'DashboardController::mapel');
//     $routes->post('/mapel/tambah-mapel', 'KepalaController::tambahmapel');
//     $routes->post('/mapel/edit-mapel', 'KepalaController::editmapel');
//     $routes->get('mapel/hapus/(:segment)', 'KepalaController::hapusmapel/$1');
//     $routes->get('/jadwal-mengajar', 'DashboardController::jadwalmengajar');
//     $routes->get('/penugasan', 'DashboardController::penugasan');
//     $routes->get('/edit-penugasan', 'DashboardController::editpenugasan');
//     $routes->get('/tugas-saya', 'DashboardController::tugassaya');
//     $routes->get('/absensi', 'DashboardController::absensi');
//     $routes->get('/absensi/mulai-absensi', 'DashboardController::mulaiabsensi');
//     $routes->get('/riwayat-absensi', 'DashboardController::riwayatabsensi');
//     $routes->get('/profil', 'DashboardController::profil');
//     $routes->get('/edit-profil', 'DashboardController::editprofil');
//     $routes->get('/panduan', 'DashboardController::panduan');
// });


$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Route yang bisa diakses semua role yang login
    $routes->get('/dashboard', 'DashboardController::index');
    $routes->get('/profil', 'DashboardController::profil');
    $routes->get('/profil/edit-profil', 'DashboardController::editprofil');
    $routes->post('/profil/update', 'DashboardController::updateprofil');
    $routes->get('/panduan', 'DashboardController::panduan');

    // HANYA Kepala Sekolah & Operator
    $routes->group('', ['filter' => 'role:kepala sekolah,operator'], function ($routes) {
        $routes->get('/guru', 'DashboardController::guru');
        $routes->get('/guru/export-excel', 'KepalaController::exportguruexcel');
        $routes->post('/guru/tambah-guru', 'KepalaController::tambahguru');
        $routes->post('/guru/hapus/(:num)', 'KepalaController::hapusguru/$1');
        $routes->get('/guru/edit-guru/(:num)', 'DashboardController::editguru/$1');
        $routes->post('guru/update/(:num)', 'KepalaController::updateguru/$1');

        $routes->get('/kelas', 'DashboardController::kelas');
        $routes->post('/kelas/tambah-kelas', 'KepalaController::tambahkelas');
        $routes->post('/kelas/hapus-kelas', 'KepalaController::hapuskelas');
        $routes->post('kelas/edit-kelas', 'KepalaController::editkelas');

        $routes->get('/siswa', 'DashboardController::siswa');
        $routes->get('/siswa/export-excel', 'KepalaController::exportsiswaexcel');
        $routes->post('siswa/import', 'KepalaController::import');
        $routes->post('/siswa/tambah-siswa', 'KepalaController::tambahsiswa');
        $routes->get('siswa/edit-siswa/(:num)', 'DashboardController::editsiswa/$1');
        $routes->post('siswa/update/(:num)', 'KepalaController::updatesiswa/$1');
        $routes->get('siswa/hapus/(:num)', 'KepalaController::hapussiswa/$1');

        $routes->get('/mapel', 'DashboardController::mapel');
        $routes->post('/mapel/tambah-mapel', 'KepalaController::tambahmapel');
        $routes->post('/mapel/edit-mapel', 'KepalaController::editmapel');
        $routes->get('mapel/hapus/(:segment)', 'KepalaController::hapusmapel/$1');

        $routes->post('/jadwal-mengajar/tambah-jadwal-mengajar', 'KepalaController::tambahjadwalmengajar');
        $routes->post('/jadwal-mengajar/hapus', 'KepalaController::hapusjadwalmengajar');
        $routes->post('/riwayat-absensi/edit-absensi', 'KepalaController::editabsensi');
        $routes->post('/data-siswa/hapus_kelas', 'KepalaController::hapusSiswaBerdasarkanKelas');
        $routes->post('/dashboard/tambah-galeri', 'KepalaController::tambahgaleri');
        $routes->get('/dashboard/hapus-galeri/(:num)', 'KepalaController::hapusgaleri/$1');
    });

    // Route untuk Guru dan Guru BK
    $routes->group('', ['filter' => 'role:guru,guru bk,kepala sekolah,operator'], function ($routes) {
        $routes->get('/jadwal-mengajar', 'DashboardController::jadwalmengajar');
        $routes->get('/penugasan', 'DashboardController::penugasan');
        $routes->post('/penugasan/tambah-penugasan', 'DashboardController::tambahpenugasan');
        $routes->get('/penugasan/edit-penugasan/(:num)', 'DashboardController::editpenugasan/$1');
        $routes->post('penugasan/update/(:num)', 'DashboardController::updatepenugasan/$1');
        $routes->post('/penugasan/hapus/(:num)', 'DashboardController::hapuspenugasan/$1');
        $routes->get('/absensi', 'DashboardController::absensi');
        $routes->get('/absensi/export', 'KepalaController::exportAbsenExcel');
        $routes->get('/absensi/mulai-absensi/(:num)', 'DashboardController::mulaiabsensi/$1');
        $routes->post('absensi/simpan', 'DashboardController::simpan');
    });

    // Route untuk Siswa
    $routes->group('', ['filter' => 'role:siswa,kepala sekolah,operator'], function ($routes) {
        $routes->get('/tugas-saya', 'DashboardController::tugassaya');
        $routes->get('/riwayat-absensi', 'DashboardController::riwayatabsensi');
    });
});
