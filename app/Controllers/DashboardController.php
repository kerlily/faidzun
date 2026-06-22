<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\GaleriModel;
use App\Models\JadwalMengajarModel;

/**
 * DashboardController
 * 
 * Hanya menangani:
 *  - Halaman dashboard (statistik + galeri)
 *  - Jadwal mengajar (view only)
 *  - Profil user
 *  - Panduan penggunaan
 */
class DashboardController extends BaseController
{
    public function index()
    {
        $userModel   = new UsersModel();
        $kelasModel  = new KelasModel();
        $mapelModel  = new MapelModel();
        $galeriModel = new GaleriModel();

        $data = [
            'totalSiswa' => $userModel->where('role', 'siswa')->countAllResults(),
            'totalGuru'  => $userModel->whereIn('role', ['guru', 'guru bk', 'operator', 'kepala sekolah'])->countAllResults(),
            'totalKelas' => $kelasModel->countAllResults(),
            'totalMapel' => $mapelModel->countAllResults(),
            'role'       => session()->get('role'),
            'galeri'     => $galeriModel->findAll(),
        ];

        return view('dashboard/v_dashboard', $data);
    }

    public function jadwalmengajar()
    {
        $kelasModel  = new KelasModel();
        $userModel   = new UsersModel();   // PERBAIKAN: bukan UserSModel
        $mapelModel  = new MapelModel();
        $jadwalModel = new JadwalMengajarModel();

        $id_user = session()->get('id_user');
        $role    = session()->get('role');

        if (in_array($role, ['guru', 'guru bk'])) {
            $jadwal = $jadwalModel->getJadwalLengkapByUser($id_user);
        } else {
            $jadwal = $jadwalModel->getJadwalLengkap();
        }

        // Strukturkan per guru → hari
        $jadwal_terstruktur = [];
        foreach ($jadwal as $item) {
            $nama  = $item['nama_guru'];
            $hari  = ucfirst(strtolower($item['hari'])); // normalisasi kapitalisasi
            $kelas = $item['kelas'];

            $jadwal_terstruktur[$nama][$hari][] = [
                'id_jadwal'   => $item['id_jadwal'],
                'jam_mulai'   => $item['jam_mulai'],
                'jam_selesai' => $item['jam_selesai'],
                'mapel'       => $item['mapel'],
                'kelas'       => $kelas,
            ];
        }

        return view('aktivitas/v_jadwalmengajar', [
            'kelas'  => $kelasModel->findAll(),
            'guru'   => $userModel->whereIn('role', ['guru', 'guru bk', 'operator'])->findAll(),
            'mapel'  => $mapelModel->findAll(),
            'jadwal' => $jadwal_terstruktur,
        ]);
    }

    public function profil()
    {
        $userModel  = new UsersModel();
        $kelasModel = new KelasModel();
        $userId     = session()->get('id_user');

        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/dashboard')->with('swal_error', 'Data pengguna tidak ditemukan.');
        }

        if (!empty($user['id_kelas'])) {
            $kelas             = $kelasModel->find($user['id_kelas']);
            $user['nama_kelas']= $kelas['nama_kelas'] ?? '';
        } else {
            $user['nama_kelas'] = '';
        }

        return view('lainnya/v_profil', [
            'user' => $user,
            'role' => session()->get('role'),
        ]);
    }

    public function editprofil()
    {
        $userModel  = new UsersModel();
        $kelasModel = new KelasModel();
        $idUser     = session()->get('id_user');

        $user = $userModel->find($idUser);

        if (!$user) {
            return redirect()->to('/profil')->with('swal_error', 'Data pengguna tidak ditemukan.');
        }

        if (!empty($user['id_kelas'])) {
            $kelas             = $kelasModel->find($user['id_kelas']);
            $user['nama_kelas']= $kelas['nama_kelas'] ?? '';
        } else {
            $user['nama_kelas'] = '';
        }

        return view('lainnya/v_editprofil', ['user' => $user]);
    }

    public function updateprofil()
    {
        $userModel = new UsersModel();
        $idUser    = session()->get('id_user');

        $guruLama = $userModel->find($idUser);

        if (!$guruLama) {
            return redirect()->to('/profil')->with('swal_error', 'Data pengguna tidak ditemukan.');
        }

        $data = [
            'nama'               => $this->request->getPost('nama'),
            'ttl'                => $this->request->getPost('ttl'),
            'jenis_kelamin'      => $this->request->getPost('jenis_kelamin'),
            'agama'              => $this->request->getPost('agama'),
            'no_hp'              => $this->request->getPost('no_hp'),
            'alamat'             => $this->request->getPost('alamat'),
            'pendidikan_terakhir'=> $this->request->getPost('pendidikan'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if (!empty($guruLama['foto']) && $guruLama['foto'] !== 'users.png' && file_exists('profil/' . $guruLama['foto'])) {
                unlink('profil/' . $guruLama['foto']);
            }
            $namaFoto     = $foto->getRandomName();
            $foto->move('profil/', $namaFoto);
            $data['foto'] = $namaFoto;
        }

        try {
            $userModel->update($idUser, $data);
            return redirect()->to('/profil')->with('swal_success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            log_message('error', 'updateprofil error: ' . $e->getMessage());
            return redirect()->to('/profil')->with('swal_error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    public function panduan()
    {
        return view('lainnya/v_panduan');
    }
}