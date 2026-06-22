<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\JadwalMengajarModel;
use App\Models\AbsensiModel;
use App\Models\GaleriModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * ManajemenController
 * 
 * Mengelola semua data master: guru, siswa, kelas, mapel, jadwal, galeri.
 * 
 * AKSES:
 *  - GET (view): kepala sekolah & operator
 *  - POST/DELETE (CRUD): operator saja
 * 
 * Filter role sudah diterapkan di Routes.php, controller ini
 * tidak perlu cek role lagi kecuali untuk logika tampilan view.
 */
class ManajemenController extends BaseController
{
    // ================================================================
    // GURU
    // ================================================================

    public function guru()
    {
        $userModel = new UsersModel();
        $data['guru'] = $userModel
            ->whereIn('role', ['guru', 'guru bk', 'operator', 'kepala sekolah'])
            ->findAll();

        return view('manajemen-data/v_guru', $data);
    }

    public function tambahGuru()
    {
        $validationRules = [
            'nama'      => 'required|min_length[3]',
            'username'  => 'required|min_length[4]|is_unique[users.username]',
            'nip'       => 'required|numeric|min_length[8]',
            'ttl'       => 'required',
            'gender'    => 'required',
            'agama'     => 'required',
            'role'      => 'required',
            'pendidikan'=> 'required',
            'alamat'    => 'required',
            'no_hp'     => 'required|numeric|min_length[10]',
            'password'  => 'required|min_length[6]',
            'foto'      => 'permit_empty|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Gagal menyimpan. ' . implode(' ', $this->validator->getErrors()));
        }

        $model = new UsersModel();

        $foto = $this->request->getFile('foto');
        $namaFoto = 'users.png';
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('profil/', $namaFoto);
        }

        $model->insert([
            'nama'               => $this->request->getPost('nama'),
            'username'           => $this->request->getPost('username'),
            'nip'                => $this->request->getPost('nip'),
            'ttl'                => $this->request->getPost('ttl'),
            'jenis_kelamin'      => $this->request->getPost('gender'),
            'agama'              => $this->request->getPost('agama'),
            'role'               => strtolower($this->request->getPost('role')),
            'pendidikan_terakhir'=> $this->request->getPost('pendidikan'),
            'alamat'             => $this->request->getPost('alamat'),
            'no_hp'              => $this->request->getPost('no_hp'),
            'password'           => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'foto'               => $namaFoto,
        ]);

        return redirect()->to('/guru')->with('swal_success', 'Data guru berhasil disimpan.');
    }

    public function editGuru($id)
    {
        $model = new UsersModel();
        $guru  = $model->find($id);

        if (!$guru) {
            return redirect()->to('/guru')->with('swal_error', 'Data guru tidak ditemukan.');
        }

        return view('manajemen-data/v_editguru', ['guru' => $guru]);
    }

    public function updateGuru($id)
    {
        $userModel = new UsersModel();
        $guruLama  = $userModel->find($id);

        if (!$guruLama) {
            return redirect()->to('/guru')->with('swal_error', 'Data guru tidak ditemukan.');
        }

        if (!$this->validate([
            'nama'      => 'required',
            'username'  => "required|is_unique[users.username,id_user,{$id}]",
            'nip'       => 'required|numeric',
            'ttl'       => 'required',
            'role'      => 'required',
            'pendidikan'=> 'required',
            'gender'    => 'required',
            'agama'     => 'required',
            'alamat'    => 'required',
            'no_hp'     => 'required|numeric',
            'foto'      => 'permit_empty|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
        ])) {
            return redirect()->to('/guru')->with('swal_error', implode('<br>', $this->validator->getErrors()));
        }

        $foto     = $this->request->getFile('foto');
        $namaFoto = $guruLama['foto'];

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            if (!empty($guruLama['foto']) && $guruLama['foto'] !== 'users.png' && file_exists('profil/' . $guruLama['foto'])) {
                unlink('profil/' . $guruLama['foto']);
            }
            $namaFoto = $foto->getRandomName();
            $foto->move('profil/', $namaFoto);
        }

        $dataUpdate = [
            'nama'               => $this->request->getPost('nama'),
            'username'           => $this->request->getPost('username'),
            'nip'                => $this->request->getPost('nip'),
            'ttl'                => $this->request->getPost('ttl'),
            'role'               => strtolower($this->request->getPost('role')),
            'pendidikan_terakhir'=> $this->request->getPost('pendidikan'),
            'jenis_kelamin'      => $this->request->getPost('gender'),
            'agama'              => $this->request->getPost('agama'),
            'alamat'             => $this->request->getPost('alamat'),
            'no_hp'              => $this->request->getPost('no_hp'),
            'foto'               => $namaFoto,
        ];

        $passwordBaru = $this->request->getPost('password');
        if (!empty($passwordBaru)) {
            $dataUpdate['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $dataUpdate);

        return redirect()->to('/guru')->with('swal_success', 'Data guru berhasil diperbarui.');
    }

    public function hapusGuru($id)
    {
        $userModel = new UsersModel();
        $guru      = $userModel->find($id);

        if (!$guru) {
            return redirect()->to('/guru')->with('swal_error', 'Data guru tidak ditemukan.');
        }

        if (!empty($guru['foto']) && $guru['foto'] !== 'users.png') {
            $fotoPath = FCPATH . 'profil/' . $guru['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        $userModel->delete($id);

        return redirect()->to('/guru')->with('swal_success', 'Data guru berhasil dihapus.');
    }

    public function exportGuruExcel()
    {
        $userModel = new UsersModel();
        $users = $userModel
            ->select('users.nama, users.username, users.role')
            ->whereIn('users.role', ['kepala sekolah', 'operator', 'guru', 'guru bk'])
            ->orderBy('users.role', 'ASC')
            ->orderBy('users.nama', 'ASC')
            ->findAll();

        $filename = 'data_guru_' . date('Y-m-d_H-i-s') . '.xls';

        if (ob_get_level() > 0) ob_end_clean();

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "Nama\tUsername\tRole\n";
        foreach ($users as $user) {
            echo $user['nama'] . "\t" . $user['username'] . "\t" . $user['role'] . "\n";
        }
        exit;
    }

    // ================================================================
    // SISWA
    // ================================================================

    public function siswa()
    {
        $userModel  = new UsersModel();
        $kelasModel = new KelasModel();

        $data['siswa'] = $userModel->getSiswaWithKelas();
        $data['kelas'] = $kelasModel->findAll();

        return view('manajemen-data/v_siswa', $data);
    }

    public function tambahSiswa()
    {
        $validationRules = [
            'nama'         => 'required|min_length[3]',
            'username'     => 'required|is_unique[users.username]',
            'nisn'         => 'required|numeric|is_unique[users.nisn]',
            'ttl'          => 'required',
            'jenis_kelamin'=> 'required',
            'agama'        => 'required',
            'kelas'        => 'required',
            'alamat'       => 'required',
            'no_hp'        => 'required',
            'password'     => 'required|min_length[6]',
            'foto'         => 'permit_empty|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Gagal menyimpan data: ' . implode(' ', $this->validator->getErrors()));
        }

        $model    = new UsersModel();
        $foto     = $this->request->getFile('foto');
        $namaFoto = 'users.png';

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('profil/', $namaFoto);
        }

        $model->insert([
            'nama'         => $this->request->getPost('nama'),
            'username'     => $this->request->getPost('username'),
            'nisn'         => $this->request->getPost('nisn'),
            'ttl'          => $this->request->getPost('ttl'),
            'jenis_kelamin'=> $this->request->getPost('jenis_kelamin'),
            'agama'        => $this->request->getPost('agama'),
            'role'         => 'siswa',
            'id_kelas'     => $this->request->getPost('kelas'),
            'alamat'       => $this->request->getPost('alamat'),
            'no_hp'        => $this->request->getPost('no_hp'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'foto'         => $namaFoto,
        ]);

        return redirect()->to('/siswa')->with('swal_success', 'Data siswa berhasil disimpan.');
    }

    public function editSiswa($id)
    {
        $userModel  = new UsersModel();
        $kelasModel = new KelasModel();

        $data['siswa'] = $userModel->find($id);
        $data['kelas'] = $kelasModel->findAll();

        if (!$data['siswa']) {
            return redirect()->to('/siswa')->with('swal_error', 'Data siswa tidak ditemukan.');
        }

        return view('manajemen-data/v_editsiswa', $data);
    }

    public function updateSiswa($id)
    {
        $siswaModel = new UsersModel();
        $siswaLama  = $siswaModel->find($id);

        if (!$siswaLama) {
            return redirect()->to('/siswa')->with('swal_error', 'Data siswa tidak ditemukan.');
        }

        $validationRules = [
            'nama'         => 'required',
            'username'     => "required|is_unique[users.username,id_user,{$id}]",
            'nisn'         => "required|numeric|is_unique[users.nisn,id_user,{$id}]",
            'ttl'          => 'required',
            'agama'        => 'required',
            'jenis_kelamin'=> 'required',
            'id_kelas'     => 'required',
            'alamat'       => 'required',
            'no_hp'        => 'required',
        ];

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $validationRules['foto'] = 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]';
        }

        if (!$this->validate($validationRules)) {
            return redirect()->to('/siswa')->with('swal_error', implode('<br>', $this->validator->getErrors()));
        }

        $data = [
            'nama'         => $this->request->getPost('nama'),
            'username'     => $this->request->getPost('username'),
            'nisn'         => $this->request->getPost('nisn'),
            'ttl'          => $this->request->getPost('ttl'),
            'agama'        => $this->request->getPost('agama'),
            'jenis_kelamin'=> $this->request->getPost('jenis_kelamin'),
            'id_kelas'     => $this->request->getPost('id_kelas'),
            'alamat'       => $this->request->getPost('alamat'),
            'no_hp'        => $this->request->getPost('no_hp'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFotoBaru = $foto->getRandomName();
            $foto->move('profil/', $namaFotoBaru);
            $data['foto'] = $namaFotoBaru;

            if (!empty($siswaLama['foto']) && $siswaLama['foto'] !== 'users.png' && file_exists('profil/' . $siswaLama['foto'])) {
                unlink('profil/' . $siswaLama['foto']);
            }
        }

        $siswaModel->update($id, $data);

        return redirect()->to('/siswa')->with('swal_success', 'Data siswa berhasil diperbarui.');
    }

    public function hapusSiswa($id)
    {
        $userModel = new UsersModel();
        $siswa     = $userModel->find($id);

        if (!$siswa) {
            return redirect()->to('/siswa')->with('swal_error', 'Data siswa tidak ditemukan.');
        }

        if (!empty($siswa['foto']) && $siswa['foto'] !== 'users.png' && file_exists('profil/' . $siswa['foto'])) {
            unlink('profil/' . $siswa['foto']);
        }

        $userModel->delete($id);

        return redirect()->to('/siswa')->with('swal_success', 'Data siswa berhasil dihapus.');
    }

    public function exportSiswaExcel()
    {
        $userModel = new UsersModel();
        $users = $userModel
            ->select('users.nama, users.username, kelas.nama_kelas')
            ->join('kelas', 'kelas.id_kelas = users.id_kelas', 'left')
            ->where('users.role', 'siswa')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->findAll();

        $filename = 'data_siswa_' . date('Y-m-d_H-i-s') . '.xls';

        if (ob_get_level() > 0) ob_end_clean();

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "Nama\tUsername\tKelas\n";
        foreach ($users as $user) {
            echo $user['nama'] . "\t" . $user['username'] . "\t" . ($user['nama_kelas'] ?? '-') . "\n";
        }
        exit;
    }

    public function importSiswa()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            return redirect()->to('/siswa')->with('swal_error', 'File tidak valid.');
        }

        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            return redirect()->to('/siswa')->with('swal_error', 'Format file tidak didukung. Gunakan CSV, XLS, atau XLSX.');
        }

        $filePath = WRITEPATH . 'uploads/' . $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', basename($filePath));

        $userModel  = new UsersModel();
        $kelasModel = new KelasModel();

        $errors       = [];
        $successCount = 0;

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheetData   = $spreadsheet->getActiveSheet()->toArray();

            foreach ($sheetData as $i => $row) {
                if ($i === 0) continue; // skip header

                $nama          = trim($row[0] ?? '');
                $nisn          = trim($row[1] ?? '');
                $ttl           = trim($row[2] ?? '');
                $jenis_kelamin = trim($row[3] ?? '');
                $agama         = trim($row[4] ?? '');
                $nama_kelas    = trim($row[5] ?? '');
                $alamat        = trim($row[6] ?? '');
                $no_hp         = trim($row[7] ?? '');

                if (empty($nama) || empty($nisn) || empty($nama_kelas)) {
                    $errors[] = "Baris " . ($i + 1) . " - Data tidak lengkap.";
                    continue;
                }

                // Cek NISN duplikat
                if ($userModel->where('nisn', $nisn)->countAllResults() > 0) {
                    $errors[] = "Baris " . ($i + 1) . " - NISN {$nisn} sudah terdaftar.";
                    continue;
                }

                // Generate username unik
                $username         = strtolower(str_replace(' ', '.', $nama));
                $originalUsername = $username;
                $counter          = 1;
                while ($userModel->where('username', $username)->countAllResults() > 0) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }

                // Cari ID kelas
                $kelas    = $kelasModel->where('nama_kelas', strtoupper($nama_kelas))->first();
                $id_kelas = $kelas['id_kelas'] ?? null;
                if (!$id_kelas) {
                    $errors[] = "Baris " . ($i + 1) . " - Kelas '{$nama_kelas}' tidak ditemukan.";
                    continue;
                }

                if ($userModel->insert([
                    'nama'          => $nama,
                    'username'      => $username,
                    'nisn'          => $nisn,
                    'ttl'           => $ttl,
                    'jenis_kelamin' => $jenis_kelamin,
                    'agama'         => $agama,
                    'role'          => 'siswa',
                    'id_kelas'      => $id_kelas,
                    'alamat'        => $alamat,
                    'no_hp'         => $no_hp,
                    'password'      => password_hash('123456789', PASSWORD_DEFAULT),
                    'foto'          => 'users.png',
                ])) {
                    $successCount++;
                } else {
                    $errors[] = "Baris " . ($i + 1) . " - Gagal menyimpan ke database.";
                }
            }
        } catch (\Exception $e) {
            @unlink($filePath);
            return redirect()->to('/siswa')->with('swal_error', 'Gagal membaca file: ' . $e->getMessage());
        }

        @unlink($filePath);

        if (!empty($errors)) {
            session()->setFlashdata('swal_error', implode('; ', $errors));
        }

        return redirect()->to('/siswa')->with('swal_success', "{$successCount} data siswa berhasil diimport.");
    }

    // ================================================================
    // KELAS
    // ================================================================

    public function kelas()
    {
        $kelasModel = new KelasModel();
        $userModel  = new UsersModel();

        $data['kelas'] = $kelasModel
            ->select('kelas.*, users.nama as nama_wali')
            ->join('users', 'users.id_user = kelas.id_user', 'left')
            ->findAll();

        $data['guru'] = $userModel->whereIn('role', ['guru', 'guru bk'])->findAll();

        return view('manajemen-data/v_kelas', $data);
    }

    public function tambahKelas()
    {
        $validation = \Config\Services::validation();

        if (!$this->validate([
            'nama_kelas' => 'required|is_unique[kelas.nama_kelas]',
            'wali_kelas' => 'required|integer',
        ])) {
            return redirect()->to('/kelas')
                ->with('swal_error', implode(' ', $this->validator->getErrors()));
        }

        $kelasModel = new KelasModel();
        $kelasModel->insert([
            'nama_kelas' => strtoupper($this->request->getPost('nama_kelas')),
            'id_user'    => $this->request->getPost('wali_kelas'),
        ]);

        return redirect()->to('/kelas')->with('swal_success', 'Kelas berhasil ditambahkan.');
    }

    public function editKelas()
    {
        $kelasModel = new KelasModel();
        $kelasModel->update($this->request->getPost('id_kelas'), [
            'nama_kelas' => strtoupper($this->request->getPost('nama_kelas')),
            'id_user'    => $this->request->getPost('wali_kelas'),
        ]);

        return redirect()->to('/kelas')->with('swal_success', 'Data kelas berhasil diperbarui.');
    }

    public function hapusKelas()
    {
        $id         = $this->request->getPost('id_kelas');
        $kelasModel = new KelasModel();

        if ($kelasModel->delete($id)) {
            return redirect()->to('/kelas')->with('swal_success', 'Data kelas berhasil dihapus.');
        }

        return redirect()->to('/kelas')->with('swal_error', 'Gagal menghapus data kelas.');
    }

    // ================================================================
    // MAPEL
    // ================================================================

    public function mapel()
    {
        $mapelModel  = new MapelModel();
        $data['mapel'] = $mapelModel->findAll();

        return view('manajemen-data/v_mapel', $data);
    }

    public function tambahMapel()
    {
        $data = [
            'kode_mapel' => strtoupper($this->request->getPost('kode_mapel')),
            'nama_mapel' => strtoupper($this->request->getPost('nama_mapel')),
        ];

        if (!$this->validate([
            'kode_mapel' => 'required|is_unique[mapel.kode_mapel]',
            'nama_mapel' => 'required',
        ])) {
            return redirect()->to('/mapel')
                ->with('swal_error', implode(' ', $this->validator->getErrors()));
        }

        $mapelModel = new MapelModel();
        $mapelModel->insert($data);

        return redirect()->to('/mapel')->with('swal_success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function hapusMapel($kode_mapel)
    {
        $mapelModel = new MapelModel();
        $data = $mapelModel->where('kode_mapel', $kode_mapel)->first();

        if (!$data) {
            return redirect()->to('/mapel')->with('swal_error', 'Data tidak ditemukan.');
        }

        $mapelModel->where('kode_mapel', $kode_mapel)->delete();

        return redirect()->to('/mapel')->with('swal_success', 'Data berhasil dihapus.');
    }

    public function editMapel()
    {
        $kodeLama = $this->request->getPost('kode_mapel_lama');
        $kodeBaru = strtoupper($this->request->getPost('kode_mapel'));
        $nama     = strtoupper($this->request->getPost('nama_mapel'));

        $mapelModel = new MapelModel();

        if ($kodeLama !== $kodeBaru && $mapelModel->where('kode_mapel', $kodeBaru)->first()) {
            return redirect()->to('/mapel')->with('swal_error', 'Kode mata pelajaran sudah digunakan!');
        }

        $mapelModel->where('kode_mapel', $kodeLama)->set([
            'kode_mapel' => $kodeBaru,
            'nama_mapel' => $nama,
        ])->update();

        return redirect()->to('/mapel')->with('swal_success', 'Data berhasil diperbarui!');
    }

    // ================================================================
    // JADWAL MENGAJAR
    // ================================================================

    public function tambahJadwal()
    {
        $model      = new JadwalMengajarModel();
        $id_kelas   = $this->request->getPost('id_kelas');
        $id_user    = $this->request->getPost('id_guru');
        $kode_mapel = $this->request->getPost('kode_mapel');
        $hari       = $this->request->getPost('hari');
        $jam_mulai  = $this->request->getPost('jam_mulai');
        $jam_selesai= $this->request->getPost('jam_selesai');

        // Validasi wajib diisi
        if (empty($id_kelas) || empty($id_user) || empty($kode_mapel) || empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
            return redirect()->to('/jadwal-mengajar')->with('swal_error', 'Semua field wajib diisi.');
        }

        // Validasi jam
        if ($jam_selesai <= $jam_mulai) {
            return redirect()->to('/jadwal-mengajar')->with('swal_error', 'Jam selesai harus setelah jam mulai.');
        }

        // Helper cek bentrok
        $cekBentrok = function ($field, $value) use ($model, $hari, $jam_mulai, $jam_selesai) {
            return $model
                ->where('hari', $hari)
                ->where($field, $value)
                ->groupStart()
                    ->groupStart()
                        ->where('jam_mulai <=', $jam_mulai)
                        ->where('jam_selesai >', $jam_mulai)
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('jam_mulai <', $jam_selesai)
                        ->where('jam_selesai >=', $jam_selesai)
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('jam_mulai >=', $jam_mulai)
                        ->where('jam_selesai <=', $jam_selesai)
                    ->groupEnd()
                ->groupEnd()
                ->first();
        };

        if ($cekBentrok('id_user', $id_user)) {
            return redirect()->to('/jadwal-mengajar')->with('swal_error', 'Guru sudah memiliki jadwal di waktu tersebut.');
        }

        if ($cekBentrok('id_kelas', $id_kelas)) {
            return redirect()->to('/jadwal-mengajar')->with('swal_error', 'Kelas sudah memiliki jadwal di waktu tersebut.');
        }

        $model->insert([
            'id_kelas'    => $id_kelas,
            'id_user'     => $id_user,
            'kode_mapel'  => $kode_mapel,
            'hari'        => $hari,
            'jam_mulai'   => $jam_mulai,
            'jam_selesai' => $jam_selesai,
        ]);

        return redirect()->to('/jadwal-mengajar')->with('swal_success', 'Jadwal berhasil disimpan.');
    }

    public function hapusJadwal()
    {
        $id    = $this->request->getPost('id_jadwal');
        $model = new JadwalMengajarModel();

        if ($model->delete($id)) {
            return redirect()->to('/jadwal-mengajar')->with('swal_success', 'Jadwal berhasil dihapus.');
        }

        return redirect()->to('/jadwal-mengajar')->with('swal_error', 'Gagal menghapus jadwal.');
    }

    // ================================================================
    // GALERI
    // ================================================================

    public function tambahGaleri()
    {
        $file = $this->request->getFile('foto');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'galeri', $newName);

            $galeriModel = new GaleriModel();
            $galeriModel->insert(['foto' => $newName]);

            return redirect()->to('/dashboard')->with('swal_success', 'Foto berhasil diupload.');
        }

        return redirect()->to('/dashboard')->with('swal_error', 'Gagal mengupload foto.');
    }

    public function hapusGaleri($id_galeri)
    {
        $galeriModel = new GaleriModel();
        $foto        = $galeriModel->find($id_galeri);

        if (!$foto) {
            return redirect()->to('/dashboard')->with('swal_error', 'Foto tidak ditemukan.');
        }

        $filePath = FCPATH . 'galeri/' . $foto['foto'];
        if (is_file($filePath)) {
            unlink($filePath);
        }

        $galeriModel->delete($id_galeri);

        return redirect()->to('/dashboard')->with('swal_success', 'Foto berhasil dihapus.');
    }

    public function exportAbsenExcel()
    {
        $absensiModel = new AbsensiModel();

        $riwayat = $absensiModel
            ->select('users.nama, kelas.nama_kelas, absensi.keterangan')
            ->join('users', 'users.id_user = absensi.id_user')
            ->join('kelas', 'kelas.id_kelas = absensi.kelas_saat_absen')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('users.nama', 'ASC')
            ->findAll();

        $rekap = [];
        foreach ($riwayat as $row) {
            $nama   = trim($row['nama']);
            $kelas  = trim($row['nama_kelas']);
            $status = strtolower(trim($row['keterangan']));

            if (!isset($rekap[$kelas][$nama])) {
                $rekap[$kelas][$nama] = ['nama' => $nama, 'kelas' => $kelas, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'total' => 0];
            }

            if (in_array($status, ['hadir', 'masuk'])) {
                $rekap[$kelas][$nama]['hadir']++;
            } elseif ($status === 'izin') {
                $rekap[$kelas][$nama]['izin']++;
            } elseif ($status === 'sakit') {
                $rekap[$kelas][$nama]['sakit']++;
            } else {
                $rekap[$kelas][$nama]['alpa']++;
            }

            $rekap[$kelas][$nama]['total']++;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($rekap as $kelas => $siswaList) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(substr($kelas, 0, 30));

            $sheet->setCellValue('A1', 'No')
                  ->setCellValue('B1', 'Nama Siswa')
                  ->setCellValue('C1', 'Kelas')
                  ->setCellValue('D1', 'Hadir')
                  ->setCellValue('E1', 'Izin')
                  ->setCellValue('F1', 'Sakit')
                  ->setCellValue('G1', 'Alfa')
                  ->setCellValue('H1', 'Total Pertemuan')
                  ->setCellValue('I1', '% Kehadiran');

            $rowNum = 2;
            $no     = 1;

            uasort($siswaList, fn($a, $b) => strcmp($a['nama'], $b['nama']));

            foreach ($siswaList as $stat) {
                $persen = $stat['total'] > 0 ? round(($stat['hadir'] / $stat['total']) * 100, 2) : 0;
                $sheet->setCellValue('A' . $rowNum, $no++)
                      ->setCellValue('B' . $rowNum, $stat['nama'])
                      ->setCellValue('C' . $rowNum, $stat['kelas'])
                      ->setCellValue('D' . $rowNum, $stat['hadir'])
                      ->setCellValue('E' . $rowNum, $stat['izin'])
                      ->setCellValue('F' . $rowNum, $stat['sakit'])
                      ->setCellValue('G' . $rowNum, $stat['alpa'])
                      ->setCellValue('H' . $rowNum, $stat['total'])
                      ->setCellValue('I' . $rowNum, $persen . '%');
                $rowNum++;
            }
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="rekap_absensi_per_kelas.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}