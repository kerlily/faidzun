<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\JadwalMengajarModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\UsersModel;

/**
 * AbsensiController
 * 
 * Mengelola fitur absensi:
 *  - Daftar jadwal yang bisa diabsen (guru/guru bk)
 *  - Mulai absensi
 *  - Simpan absensi
 *  - Riwayat absensi
 *  - Edit status kehadiran (kepala sekolah/operator)
 *  - Export Excel
 */
class AbsensiController extends BaseController
{
    public function index()
    {
        $session  = session();
        $role     = strtolower(trim($session->get('role')));
        $id_user  = (int) $session->get('id_user');

        date_default_timezone_set('Asia/Jakarta');

        $jadwalModel = new JadwalMengajarModel();
        $kelasModel  = new KelasModel();
        $mapelModel  = new MapelModel();
        $absenModel  = new AbsensiModel();

        $jadwalQuery = $jadwalModel
            ->select('jadwal_mengajar.id_jadwal, jadwal_mengajar.id_kelas, jadwal_mengajar.kode_mapel, jadwal_mengajar.jam_mulai, jadwal_mengajar.jam_selesai, jadwal_mengajar.hari, mapel.nama_mapel, users.nama as guru, kelas.nama_kelas, jadwal_mengajar.id_user')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
            ->join('users', 'users.id_user = jadwal_mengajar.id_user');

        if ($role === 'guru' || $role === 'guru bk') {
            $jadwalQuery->where('jadwal_mengajar.id_user', $id_user);
        }

        $selected_kelas = $this->request->getGet('kelas');
        $selected_mapel = $this->request->getGet('mapel');

        if (!empty($selected_kelas)) {
            $jadwalQuery->where('jadwal_mengajar.id_kelas', $selected_kelas);
        }
        if (!empty($selected_mapel)) {
            $jadwalQuery->where('jadwal_mengajar.kode_mapel', $selected_mapel);
        }

        $jadwal = $jadwalQuery->get()->getResultArray();

        // Cek jadwal yang sudah diabsen hari ini
        $tanggalHariIni = date('Y-m-d');
        $absensiHariIni = $absenModel->select('id_jadwal')->where('tanggal', $tanggalHariIni)->findAll();
        $absenMap       = [];
        foreach ($absensiHariIni as $a) {
            $absenMap[$a['id_jadwal']] = true;
        }

        $sudahAbsenList = [];
        foreach ($jadwal as &$item) {
            $item['boleh_absen']              = !isset($absenMap[$item['id_jadwal']]);
            $sudahAbsenList[$item['id_jadwal']] = !$item['boleh_absen'];
        }
        unset($item);

        $data = [
            'absensi'         => $jadwal,
            'sudahAbsenList'  => $sudahAbsenList,
            'selected_kelas'  => $selected_kelas,
            'selected_mapel'  => $selected_mapel,
        ];

        // Dropdown filter
        if ($role === 'kepala sekolah' || $role === 'operator') {
            $data['filter_kelas'] = $kelasModel->findAll();
            $data['filter_mapel'] = $mapelModel->findAll();
        } else {
            // Hanya tampilkan kelas & mapel yang guru ini ajar
            $jadwalGuru = (new JadwalMengajarModel())
                ->select('kelas.id_kelas, kelas.nama_kelas, mapel.kode_mapel, mapel.nama_mapel')
                ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
                ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
                ->where('jadwal_mengajar.id_user', $id_user)
                ->groupBy(['kelas.id_kelas', 'mapel.kode_mapel'])
                ->findAll();

            $kelasTersedia = [];
            $mapelTersedia = [];
            foreach ($jadwalGuru as $it) {
                $kelasTersedia[$it['id_kelas']]    = $it['nama_kelas'];
                $mapelTersedia[$it['kode_mapel']]  = $it['nama_mapel'];
            }

            $data['filter_kelas'] = array_map(
                fn($id, $nama) => ['id_kelas' => $id, 'nama_kelas' => $nama],
                array_keys($kelasTersedia), $kelasTersedia
            );
            $data['filter_mapel'] = array_map(
                fn($kode, $nama) => ['kode_mapel' => $kode, 'nama_mapel' => $nama],
                array_keys($mapelTersedia), $mapelTersedia
            );
        }

        return view('aktivitas/v_absensi', $data);
    }

    public function mulai($id_jadwal)
    {
        date_default_timezone_set('Asia/Jakarta');

        $jadwalModel = new JadwalMengajarModel();
        $siswaModel  = new UsersModel();

        $jadwal = $jadwalModel
            ->select('jadwal_mengajar.*, mapel.nama_mapel, kelas.nama_kelas, kelas.id_kelas, users.nama as nama_guru')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
            ->join('users', 'users.id_user = jadwal_mengajar.id_user')
            ->where('jadwal_mengajar.id_jadwal', $id_jadwal)
            ->first();

        if (!$jadwal) {
            return redirect()->to('/absensi')->with('swal_error', 'Jadwal tidak ditemukan.');
        }

        $now = new \DateTime();

        $hariIndonesia = [
            'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
            'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu',
        ];
        $hariIni     = $hariIndonesia[strtolower($now->format('l'))] ?? '';
        $hariJadwal  = strtolower($jadwal['hari']);
        $jamMulai    = new \DateTime($jadwal['jam_mulai']);
        $jamSelesai  = new \DateTime($jadwal['jam_selesai']);

        if ($hariIni !== $hariJadwal || $now < $jamMulai || $now > $jamSelesai) {
            return redirect()->to('/absensi')->with('swal_error', 'Sesi absensi hanya bisa diakses pada waktu yang telah dijadwalkan.');
        }

        // Cek sudah diabsen hari ini
        $absenModel = new AbsensiModel();
        $sudahAbsen = $absenModel->where('id_jadwal', $id_jadwal)->where('tanggal', date('Y-m-d'))->first();

        if ($sudahAbsen) {
            return redirect()->to('/absensi')->with('swal_error', 'Absensi untuk jadwal ini sudah dilakukan hari ini.');
        }

        $siswa = $siswaModel
            ->where('id_kelas', $jadwal['id_kelas'])
            ->where('role', 'siswa')
            ->orderBy('nama', 'ASC')
            ->findAll();

        return view('aktivitas/v_mulaiabsensi', [
            'jadwal'  => $jadwal,
            'siswa'   => $siswa,
            'tanggal' => date('Y-m-d'),
        ]);
    }

    public function simpan()
    {
        $absensiModel = new AbsensiModel();
        $idJadwal     = $this->request->getPost('id_jadwal');
        $materi       = $this->request->getPost('materi');
        $kehadiranData= $this->request->getPost('kehadiran');

        if (empty($idJadwal) || empty($kehadiranData)) {
            return redirect()->to('/absensi')->with('swal_error', 'Data tidak lengkap.');
        }

        $jadwalModel = new JadwalMengajarModel();
        $jadwal      = $jadwalModel->find($idJadwal);

        if (!$jadwal) {
            return redirect()->to('/absensi')->with('swal_error', 'Data jadwal tidak ditemukan.');
        }

        // Cek duplikat absensi hari ini
        $tanggal    = date('Y-m-d');
        $sudahAbsen = $absensiModel->where('id_jadwal', $idJadwal)->where('tanggal', $tanggal)->first();
        if ($sudahAbsen) {
            return redirect()->to('/absensi')->with('swal_error', 'Absensi hari ini sudah pernah disimpan untuk jadwal ini.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($kehadiranData as $idUser => $keterangan) {
            $absensiModel->insert([
                'id_jadwal'        => $idJadwal,
                'id_user'          => $idUser,
                'tanggal'          => $tanggal,
                'kelas_saat_absen' => $jadwal['id_kelas'],
                'keterangan'       => $keterangan,
                'materi'           => $materi,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('/absensi')->with('swal_error', 'Gagal menyimpan absensi. Silakan coba lagi.');
        }

        return redirect()->to('/absensi')->with('swal_success', 'Absensi berhasil disimpan.');
    }

   public function riwayat()
{
    $absensiModel = new AbsensiModel();
    $userModel    = new UsersModel();
    $kelasModel   = new KelasModel();
    $mapelModel   = new MapelModel();
    $jadwalModel  = new JadwalMengajarModel();

    $session     = session();
    $role        = $session->get('role');
    $idUser      = $session->get('id_user');

    $filterKelas = $this->request->getGet('kelas');
    $filterMapel = $this->request->getGet('mapel');

    $builder = $absensiModel
        ->select('absensi.*, users.nama, users.jenis_kelamin, kelas.id_kelas, kelas.nama_kelas, mapel.nama_mapel, jadwal_mengajar.id_user as id_guru, guru_user.nama as guru')
        ->join('users', 'users.id_user = absensi.id_user')
        ->join('kelas', 'kelas.id_kelas = absensi.kelas_saat_absen')
        ->join('jadwal_mengajar', 'jadwal_mengajar.id_jadwal = absensi.id_jadwal')
        ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
        ->join('users as guru_user', 'guru_user.id_user = jadwal_mengajar.id_user');

    // CEK ROLE UNTUK FILTER
    if ($role === 'siswa') {
        $user = $userModel->find($idUser);
        $builder->where('absensi.kelas_saat_absen', $user['id_kelas']);
        $builder->where('absensi.id_user', $user['id_user']);

        $mapelList = $jadwalModel
            ->select('mapel.kode_mapel, mapel.nama_mapel')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->where('jadwal_mengajar.id_kelas', $user['id_kelas'])
            ->groupBy('mapel.kode_mapel')
            ->findAll();
    } elseif ($role === 'guru' || $role === 'guru bk') {
        $builder->where('jadwal_mengajar.id_user', $idUser);
        $mapelList = $jadwalModel
            ->select('mapel.kode_mapel, mapel.nama_mapel')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->where('jadwal_mengajar.id_user', $idUser)
            ->groupBy('mapel.kode_mapel')
            ->findAll();
    } else {
        $mapelList = $mapelModel->findAll();
    }

    if ($role !== 'siswa' && !empty($filterKelas) && $filterKelas !== 'Semua') {
        $builder->where('kelas.id_kelas', $filterKelas);
    }
    if (!empty($filterMapel) && $filterMapel !== 'Semua') {
        $builder->where('mapel.kode_mapel', $filterMapel);
    }

    return view('aktivitas/v_riwayatabsensi', [
        'riwayat'     => $builder->orderBy('tanggal', 'DESC')->findAll(),
        'role'        => $role,
        'kelasList'   => $kelasModel->findAll(),
        'mapelList'   => $mapelList,
        'filterKelas' => $filterKelas ?? 'Semua',
        'filterMapel' => $filterMapel ?? 'Semua',
    ]);
}

    public function editAbsensi()
    {
        $absensiModel = new AbsensiModel();
        $id_absensi   = $this->request->getPost('id_absensi');
        $status       = $this->request->getPost('status');

        if ($absensiModel->update($id_absensi, ['keterangan' => $status])) {
            return redirect()->to('/riwayat-absensi')->with('swal_success', 'Status berhasil diubah.');
        }

        return redirect()->to('/riwayat-absensi')->with('swal_error', 'Gagal mengubah status, silakan coba lagi.');
    }

    public function hapusPerKelas()
    {
        $idKelas = $this->request->getPost('id_kelas');

        if (empty($idKelas)) {
            return redirect()->to('/riwayat-absensi')->with('swal_error', 'Pilih kelas yang ingin dihapus absensinya.');
        }

        $absensiModel = new AbsensiModel();
        $absensiModel->where('kelas_saat_absen', $idKelas)->delete();
        $affectedRows = $absensiModel->db->affectedRows();

        if ($affectedRows > 0) {
            return redirect()->to('/riwayat-absensi')->with('swal_success', $affectedRows . ' data absensi berhasil dihapus.');
        }

        return redirect()->to('/riwayat-absensi')->with('swal_error', 'Tidak ada data absensi yang dihapus untuk kelas ini.');
    }

    public function exportExcel()
    {
        // Delegasi ke ManajemenController
        return (new ManajemenController())->exportAbsenExcel();
    }
}