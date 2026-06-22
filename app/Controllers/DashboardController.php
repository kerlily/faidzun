<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\JadwalMengajarModel;
use App\Models\TugasModel;
use App\Models\AbsensiModel;


class DashboardController extends BaseController
{
    public function index()
    {
        $userModel = new \App\Models\UsersModel();
        $kelasModel = new \App\Models\KelasModel();
        $mapelModel = new \App\Models\MapelModel();
        $galeriModel = new \App\Models\GaleriModel(); // Tambahan untuk galeri

        // Ambil jumlah berdasarkan role
        $totalSiswa = $userModel->where('role', 'siswa')->countAllResults();
        $totalGuru = $userModel->whereIn('role', ['guru', 'guru bk', 'operator', 'kepala sekolah'])->countAllResults();
        $totalKelas = $kelasModel->countAllResults();
        $totalMapel = $mapelModel->countAllResults();
        $role = session()->get('role');

        // Ambil semua foto dari database
        $galeri = $galeriModel->findAll();

        $data = [
            'totalSiswa' => $totalSiswa,
            'totalGuru'  => $totalGuru,
            'totalKelas' => $totalKelas,
            'totalMapel' => $totalMapel,
            'role'       => $role,
            'galeri'     => $galeri, // kirim ke view
        ];

        return view('dashboard/v_dashboard', $data);
    }


    public function guru()
    {
        $userModel = new UsersModel();
        $data['guru'] = $userModel
            ->whereIn('role', ['guru', 'guru bk', 'operator', 'kepala sekolah'])
            ->findAll();

        return view('manajemen-data/v_guru', $data);
    }
    public function editguru($id)
    {
        $model = new UsersModel();
        $guru = $model->find($id);

        if (!$guru) {
            return redirect()->to('/guru')->with('swal_error', 'Data guru tidak ditemukan.');
        }

        return view('manajemen-data/v_editguru', ['guru' => $guru]);
    }

    public function siswa()
    {
        $userModel = new UsersModel();
        $kelasModel = new KelasModel();
        $data['kelas'] = $kelasModel->findAll();
        $data['siswa'] = $userModel->getSiswaWithKelas();
        return view('manajemen-data/v_siswa', $data);
    }
    public function editsiswa($id)
    {
        $userModel = new UsersModel();
        $kelasModel = new KelasModel();
        $data['siswa'] = $userModel->find($id);
        $data['kelas'] = $kelasModel->findAll();

        return view('manajemen-data/v_editsiswa', $data);
    }

    public function kelas()
    {
        $kelasModel = new KelasModel();
        $userModel = new UsersModel();

        // Ambil semua data kelas + nama wali kelas (JOIN ke tabel users)
        $data['kelas'] = $kelasModel
            ->select('kelas.*, users.nama as nama_wali, users.role as role_wali')
            ->join('users', 'users.id_user = kelas.id_user')
            ->findAll();

        // Ambil semua guru dan guru bk untuk dropdown tambah/edit
        $data['guru'] = $userModel->whereIn('role', ['guru', 'guru bk'])->findAll();

        return view('manajemen-data/v_kelas', $data);
    }


    public function mapel()
    {
        $mapelModel = new MapelModel();
        $data['mapel'] = $mapelModel->findAll();

        return view('manajemen-data/v_mapel', $data);
    }

    public function jadwalmengajar()
    {
        $kelasModel = new KelasModel();
        $userModel = new UserSModel();
        $mapelModel = new MapelModel();
        $jadwalModel = new JadwalMengajarModel();

        $user_id = session()->get('id_user');
        $role = session()->get('role');

        // Ambil semua jadwal atau hanya milik sendiri berdasarkan role
        if (in_array($role, ['guru', 'guru bk'])) {
            $jadwal = $jadwalModel->getJadwalLengkapByUser($user_id);
        } else {
            $jadwal = $jadwalModel->getJadwalLengkap(); // semua jadwal
        }

        // Strukturkan jadwal
        $jadwal_terstruktur = [];

        foreach ($jadwal as $item) {
            $nama = $item['nama_guru'];
            $hari = $item['hari'];
            $kelas = $item['kelas'];

            $jadwal_terstruktur[$nama][$hari][] = [
                'id_jadwal'  => $item['id_jadwal'],
                'jam_mulai' => $item['jam_mulai'],
                'jam_selesai' => $item['jam_selesai'],
                'mapel'      => $item['mapel'],
                'kelas'      => $kelas,
            ];
        }

        $data = [
            'kelas' => $kelasModel->findAll(),
            'guru' => $userModel->whereIn('role', ['guru', 'guru bk', 'operator'])->findAll(),
            'mapel' => $mapelModel->findAll(),
            'jadwal' => $jadwal_terstruktur,
        ];

        return view('aktivitas/v_jadwalmengajar', $data);
    }

    public function penugasan()
    {
        $id_user = session()->get('id_user');
        $role = session()->get('role');

        $tugasModel = new \App\Models\TugasModel();
        $dataJadwal = $tugasModel->getDataJadwalByUser($id_user, $role);
        $semuaDataJadwal = $tugasModel->getAllJadwal();

        if (in_array($role, ['guru', 'guru bk'])) {
            // Untuk guru dan guru BK, hanya tampilkan tugas miliknya dan deadline belum habis
            $semuaDataJadwal = array_filter($semuaDataJadwal, function ($jadwal) use ($id_user) {
                return $jadwal['id_user_jadwal'] == $id_user && strtotime($jadwal['deadline']) >= strtotime(date('Y-m-d'));
            });
        }

        // Buat array mapel unik berdasarkan kode_mapel
        $mapelUnik = [];
        $kodeMapelSudahAda = [];

        foreach ($dataJadwal as $jadwal) {
            if (!in_array($jadwal['kode_mapel'], $kodeMapelSudahAda)) {
                $mapelUnik[] = $jadwal;
                $kodeMapelSudahAda[] = $jadwal['kode_mapel'];
            }
        }

        return view('aktivitas/v_penugasan', [
            'dataJadwal' => $dataJadwal, // untuk menampilkan semua jadwal jika dibutuhkan
            'mapelUnik'  => $mapelUnik,  // khusus untuk dropdown select mapel
            'semuaDataJadwal' => $semuaDataJadwal,
        ]);
    }

    public function tambahpenugasan()
    {
        $model = new TugasModel();

        $data = [
            'judul_tugas' => $this->request->getPost('judul_tugas'),
            'id_jadwal'   => $this->request->getPost('jadwal_id'),
            'deadline'    => $this->request->getPost('deadline'),
            'catatan'     => $this->request->getPost('catatan'),
        ];

        if ($model->insert($data)) {
            return redirect()->to('/penugasan')->with('swal_success', 'Tugas berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('swal_error', 'Tugas gagal ditambahkan!');
        }
    }

    public function editpenugasan($id_tugas)
    {
        $id_user = session()->get('id_user');
        $role = session()->get('role');

        $tugasModel = new \App\Models\TugasModel();

        // Ambil semua jadwal sesuai user untuk dropdown
        $dataJadwal = $tugasModel->getDataJadwalByUser($id_user, $role);

        // Ambil data penugasan yang akan diedit
        $penugasan = $tugasModel->find($id_tugas);

        if (!$penugasan) {
            return redirect()->to('/penugasan')->with('swal_error', 'Data penugasan tidak ditemukan.');
        }

        // Buat array mapel unik
        $mapelUnik = [];
        $kodeMapelSudahAda = [];

        foreach ($dataJadwal as $jadwal) {
            if (!in_array($jadwal['kode_mapel'], $kodeMapelSudahAda)) {
                $mapelUnik[] = $jadwal;
                $kodeMapelSudahAda[] = $jadwal['kode_mapel'];
            }
        }

        return view('aktivitas/v_editpenugasan', [
            'penugasan'  => $penugasan,   // data yang akan diedit
            'dataJadwal' => $dataJadwal,  // data semua jadwal
            'mapelUnik'  => $mapelUnik    // untuk dropdown mapel
        ]);
    }
    public function updatepenugasan($id_tugas)
    {
        $model = new TugasModel();

        // Validasi manual (jika diperlukan)
        $validation = \Config\Services::validation();

        $validation->setRules([
            'judul_tugas' => 'required',
            'jadwal_id'   => 'required',
            'deadline'    => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Data gagal diperbarui. Pastikan semua kolom wajib diisi!');
        }

        $data = [
            'judul_tugas' => $this->request->getPost('judul_tugas'),
            'id_jadwal'   => $this->request->getPost('jadwal_id'),
            'deadline'    => $this->request->getPost('deadline'),
            'catatan'     => $this->request->getPost('catatan'),
        ];

        // Eksekusi update
        if ($model->update($id_tugas, $data)) {
            return redirect()->to('/penugasan')->with('swal_success', 'Tugas berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('swal_error', 'Tugas gagal diperbarui.');
        }
    }



    public function hapuspenugasan($id_tugas)
    {
        $model = new TugasModel();

        if ($model->delete($id_tugas)) {
            return redirect()->to('/penugasan')->with('swal_success', 'Tugas berhasil dihapus!');
        } else {
            return redirect()->to('/penugasan')->with('swal_error', 'Gagal menghapus tugas.');
        }
    }
    public function tugassaya()
    {
        $tugasModel = new TugasModel();
        $user = session()->get();
        $role = $user['role'];

        // Ambil semua tugas jika kepala sekolah atau operator
        if ($role === 'kepala sekolah' || $role === 'operator') {
            $tugas = $tugasModel
                ->join('jadwal_mengajar', 'jadwal_mengajar.id_jadwal = tugas.id_jadwal')
                ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
                ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
                ->select('tugas.*, mapel.nama_mapel, kelas.nama_kelas')
                ->findAll();
        }
        // Hanya tugas sesuai kelas siswa dan deadline masih berlaku
        elseif ($role === 'siswa') {
            $id_kelas = $user['id_kelas'];
            $tugas = $tugasModel
                ->join('jadwal_mengajar', 'jadwal_mengajar.id_jadwal = tugas.id_jadwal')
                ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
                ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
                ->where('kelas.id_kelas', $id_kelas)
                ->where('tugas.deadline >=', date('Y-m-d H:i:s'))
                ->select('tugas.*, mapel.nama_mapel, kelas.nama_kelas')
                ->findAll();
        } else {
            $tugas = [];
        }

        return view('aktivitas/v_tugassaya', ['tugas' => $tugas]);
    }

    public function absensi()
    {
        // --- normalisasi session & timezone ---
        $session = session();
        $role = strtolower(trim($session->get('role')));    // normalisasi role
        $id_user = (int) $session->get('id_user');          // cast ke int supaya konsisten
        date_default_timezone_set('Asia/Jakarta');

        // --- models ---
        $jadwalModel = new \App\Models\JadwalMengajarModel();
        $kelasModel  = new \App\Models\KelasModel();
        $mapelModel  = new \App\Models\MapelModel();
        $absenModel  = new \App\Models\AbsensiModel();

        // --- build base query (masukkan id_kelas & kode_mapel juga supaya output konsisten) ---
        $jadwalQuery = $jadwalModel
            ->select('jadwal_mengajar.id_jadwal, jadwal_mengajar.id_kelas, jadwal_mengajar.kode_mapel, jadwal_mengajar.jam_mulai, jadwal_mengajar.jam_selesai, jadwal_mengajar.hari, mapel.nama_mapel, users.nama as guru, kelas.nama_kelas, jadwal_mengajar.id_user')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
            ->join('users', 'users.id_user = jadwal_mengajar.id_user');

        // --- filter berdasarkan role (gunakan role yang sudah ternormalisasi) ---
        if ($role === 'guru' || $role === 'guru bk') {
            $jadwalQuery->where('jadwal_mengajar.id_user', $id_user);
        } elseif ($role !== 'kepala sekolah' && $role !== 'operator') {
            // role lain tidak diijinkan
            return redirect()->to('/dashboard')->with('swal_error', 'Akses ditolak.');
        }

        // --- filter dari GET (kelas & mapel) ---
        $selected_kelas = $this->request->getGet('kelas');
        $selected_mapel = $this->request->getGet('mapel');
        $data['selected_kelas'] = $selected_kelas;
        $data['selected_mapel'] = $selected_mapel;

        if (!empty($selected_kelas)) {
            $jadwalQuery->where('jadwal_mengajar.id_kelas', $selected_kelas);
        }
        if (!empty($selected_mapel)) {
            $jadwalQuery->where('jadwal_mengajar.kode_mapel', $selected_mapel);
        }

        // --- ambil hasil jadwal ---
        $jadwal = $jadwalQuery->get()->getResultArray();

        // optional: log untuk debugging (cek di writable/logs)
        if (empty($jadwal) && ($role === 'guru' || $role === 'guru bk')) {
            log_message('debug', "Absensi::jadwal kosong untuk guru id_user={$id_user} (role={$role}). selected_kelas={$selected_kelas} selected_mapel={$selected_mapel}");
        }

        // --- cek absensi hari ini ---
        $tanggalHariIni = date('Y-m-d');
        $absensiHariIni = $absenModel->select('id_jadwal')->where('tanggal', $tanggalHariIni)->findAll();
        $absenMap = [];
        foreach ($absensiHariIni as $a) {
            // tanda bahwa jadwal tsb sudah diabsen hari ini
            $absenMap[$a['id_jadwal']] = true;
        }

        // --- tambah flag boleh_absen / sudahAbsenList ---
        $sudahAbsenList = [];
        foreach ($jadwal as &$item) {
            $item['boleh_absen'] = !isset($absenMap[$item['id_jadwal']]);
            $sudahAbsenList[$item['id_jadwal']] = !$item['boleh_absen'];
        }
        unset($item);

        $data['absensi'] = $jadwal;
        $data['sudahAbsenList'] = $sudahAbsenList;

        // --- siapkan dropdown filter ---
        if ($role === 'kepala sekolah' || $role === 'operator') {
            $data['filter_kelas'] = $kelasModel->findAll();
            $data['filter_mapel'] = $mapelModel->findAll();
        } else {
            // ambil kelas & mapel yang guru ini ajar (distinct)
            $jadwalGuru = (new \App\Models\JadwalMengajarModel())
                ->select('kelas.id_kelas, kelas.nama_kelas, mapel.kode_mapel, mapel.nama_mapel')
                ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
                ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
                ->where('jadwal_mengajar.id_user', $id_user)
                ->groupBy(['kelas.id_kelas', 'mapel.kode_mapel'])
                ->findAll();

            $kelasTersedia = [];
            $mapelTersedia = [];
            foreach ($jadwalGuru as $it) {
                $kelasTersedia[$it['id_kelas']] = $it['nama_kelas'];
                $mapelTersedia[$it['kode_mapel']] = $it['nama_mapel'];
            }

            $data['filter_kelas'] = array_map(function ($id, $nama) {
                return ['id_kelas' => $id, 'nama_kelas' => $nama];
            }, array_keys($kelasTersedia), $kelasTersedia);

            $data['filter_mapel'] = array_map(function ($kode, $nama) {
                return ['kode_mapel' => $kode, 'nama_mapel' => $nama];
            }, array_keys($mapelTersedia), $mapelTersedia);
        }

        // optional: debug kecil buat tampil di view sementara
        // $data['debug'] = ['session' => $session->get(), 'jadwal_count' => count($jadwal)];

        return view('aktivitas/v_absensi', $data);
    }

    public function mulaiabsensi($id_jadwal)
    {
        $jadwalModel = new \App\Models\JadwalMengajarModel();
        $siswaModel = new \App\Models\UsersModel(); // buat model ini kalau belum ada

        $jadwal = $jadwalModel
            ->select('jadwal_mengajar.*, mapel.nama_mapel, kelas.nama_kelas, kelas.id_kelas, users.nama as nama_guru')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
            ->join('users', 'users.id_user = jadwal_mengajar.id_user')
            ->where('jadwal_mengajar.id_jadwal', $id_jadwal)
            ->first();

        if (!$jadwal) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Jadwal tidak ditemukan.");
        }

        // Proteksi: hanya bisa diakses saat hari dan waktu yang sesuai
        date_default_timezone_set('Asia/Jakarta');
        $now = new \DateTime();

        // Konversi nama hari ke Indonesia
        $hariInggris = strtolower($now->format('l'));
        $hariIndonesia = [
            'monday'    => 'senin',
            'tuesday'   => 'selasa',
            'wednesday' => 'rabu',
            'thursday'  => 'kamis',
            'friday'    => 'jumat',
            'saturday'  => 'sabtu',
            'sunday'    => 'minggu',
        ];
        $hariIni = $hariIndonesia[$hariInggris] ?? $hariInggris;

        // Ambil hari dan jam dari jadwal
        $hariJadwal = strtolower($jadwal['hari']);
        $jamMulai = new \DateTime($jadwal['jam_mulai']);
        $jamSelesai = new \DateTime($jadwal['jam_selesai']);

        // Cek apakah hari dan waktu sudah sesuai
        if ($hariIni !== $hariJadwal || $now < $jamMulai || $now > $jamSelesai) {
            return redirect()->to('/absensi')->with('swal_error', 'Sesi absensi hanya bisa diakses pada waktu yang telah dijadwalkan.');
        }

        $siswa = $siswaModel
            ->where('id_kelas', $jadwal['id_kelas'])
            ->orderBy('nama', 'asc')
            ->findAll();

        return view('aktivitas/v_mulaiabsensi', [
            'jadwal' => $jadwal,
            'siswa' => $siswa,
            'tanggal' => date('Y-m-d'),
        ]);
    }

    public function simpan()
    {
        $absensiModel = new \App\Models\AbsensiModel();
        $request = service('request');

        $idJadwal = $request->getPost('id_jadwal');
        $materi = $request->getPost('materi');
        $kehadiranData = $request->getPost('kehadiran');

        // Ambil info jadwal (untuk kelas)
        $jadwalModel = new \App\Models\JadwalMengajarModel();
        $jadwal = $jadwalModel->find($idJadwal);

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Data jadwal tidak ditemukan.');
        }

        $tanggal = date('Y-m-d');

        // Loop simpan absensi per siswa
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

        return redirect()->to('/absensi')->with('swal_success', 'Absensi berhasil disimpan.');
    }



    public function riwayatabsensi()
    {
        $absensiModel = new AbsensiModel();
        $userModel = new UsersModel();
        $kelasModel = new KelasModel();
        $mapelModel = new MapelModel();
        $jadwalModel = new JadwalMengajarModel();

        $session = session();
        $role = $session->get('role');
        $idUser = $session->get('id_user');

        // Filter dari form (GET)
        $filterKelas = $this->request->getGet('kelas');
        $filterMapel = $this->request->getGet('mapel');

        // Query dasar
        $builder = $absensiModel
            ->select('
            absensi.*,
            users.nama,
            users.jenis_kelamin,
            kelas.nama_kelas,
            mapel.nama_mapel
        ')
            ->join('users', 'users.id_user = absensi.id_user')
            ->join('kelas', 'kelas.id_kelas = absensi.kelas_saat_absen')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id_jadwal = absensi.id_jadwal')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel');

        // Jika siswa → filter kelas sesuai user login
        if ($role === 'siswa') {
            $user = $userModel->find($idUser);

            // Batasi hanya data sesuai kelas siswa
            $builder->where('absensi.kelas_saat_absen', $user['id_kelas']);
            $builder->where('absensi.id_user', $user['id_user']);

            // Ambil daftar mapel dari jadwal siswa saja
            $mapelList = $jadwalModel
                ->select('mapel.kode_mapel, mapel.nama_mapel')
                ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
                ->where('jadwal_mengajar.id_kelas', $user['id_kelas'])
                ->groupBy('mapel.kode_mapel')
                ->findAll();
        } else {
            // Untuk role lain, ambil semua mapel
            $mapelList = $mapelModel->findAll();
        }

        // Filter kelas & mapel
        if ($role !== 'siswa' && !empty($filterKelas) && $filterKelas != 'Semua') {
            $builder->where('kelas.id_kelas', $filterKelas);
        }
        if (!empty($filterMapel) && $filterMapel != 'Semua') {
            $builder->where('mapel.kode_mapel', $filterMapel);
        }

        $data['riwayat'] = $builder->orderBy('tanggal', 'DESC')->findAll();
        $data['role'] = $role;
        $data['kelasList'] = $kelasModel->findAll();
        $data['mapelList'] = $mapelList;

        $data['filterKelas'] = $filterKelas ?? 'Semua';
        $data['filterMapel'] = $filterMapel ?? 'Semua';

        return view('aktivitas/v_riwayatabsensi', $data);
    }

    public function profil()
    {
        $userModel = new UsersModel();
        $kelasModel = new KelasModel();
        $userId = session()->get('id_user');

        // Ambil data user
        $user = $userModel->find($userId);
        $data['role'] = session()->get('role');

        // Ambil nama_kelas berdasarkan id_kelas
        if (isset($user['id_kelas'])) {
            $kelas = $kelasModel->find($user['id_kelas']);
            $user['nama_kelas'] = $kelas['nama_kelas'] ?? '';
        } else {
            $user['nama_kelas'] = '';
        }

        $data['user'] = $user;

        return view('lainnya/v_profil', $data);
    }

    public function editprofil()
    {
        $userModel = new UsersModel();
        $kelasModel = new \App\Models\KelasModel(); // Pastikan model ini ada

        $session = session();
        $idUser = $session->get('id_user');

        // Ambil data user
        $user = $userModel->find($idUser);

        // Ambil nama_kelas berdasarkan id_kelas user
        $kelas = $kelasModel->find($user['id_kelas']);
        $user['nama_kelas'] = $kelas['nama_kelas'] ?? '';

        return view('lainnya/v_editprofil', ['user' => $user]);
    }
    public function updateprofil()
    {
        $userModel = new UsersModel();
        $session = session();
        $idUser = $session->get('id_user');

        // Ambil data user lama
        $guruLama = $userModel->find($idUser);

        if (!$guruLama) {
            return redirect()->to('/profil')->with('swal_error', 'Data pengguna tidak ditemukan.');
        }

        // Ambil data input
        $data = [
            'nama' => $this->request->getPost('nama'),
            'ttl' => $this->request->getPost('ttl'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'agama' => $this->request->getPost('agama'),
            'no_hp' => $this->request->getPost('no_hp'),
            'alamat' => $this->request->getPost('alamat'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan'),
        ];

        // Jika password baru diisi
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Proses upload foto
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Hapus foto lama jika bukan default
            if (!empty($guruLama['foto']) && $guruLama['foto'] !== 'user.png' && file_exists('profil/' . $guruLama['foto'])) {
                unlink('profil/' . $guruLama['foto']);
            }

            // Simpan foto baru
            $namaFoto = $foto->getRandomName();
            $foto->move('profil/', $namaFoto);
            $data['foto'] = $namaFoto;
        }

        try {
            $userModel->update($idUser, $data);

            return redirect()->to('/profil')->with('swal_success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->to('/profil')->with('swal_error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }






    public function panduan()
    {
        return view('lainnya/v_panduan');
    }
}
