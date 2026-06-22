<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\AbsensiModel;
use App\Models\JadwalMengajarModel;
use App\Models\GaleriModel;
use App\Libraries\SimpleXLSX;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KepalaController extends BaseController
{
    public function tambahguru()
    {
        // Validasi input
        $validationRules = [
            'nama' => 'required|min_length[3]',
            'username' => 'required|min_length[4]|is_unique[users.username]',
            'nip' => 'required|numeric|min_length[8]',
            'ttl' => 'required',
            'gender' => 'required',
            'agama' => 'required',
            'role' => 'required',
            'pendidikan' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required|numeric|min_length[10]',
            'password' => 'required|min_length[6]',
            'foto' => 'permit_empty|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Gagal menyimpan. Silakan periksa kembali isian Anda.')
                ->with('errors', $this->validator->getErrors());
        }

        $model = new UsersModel();

        // Upload foto
        $foto = $this->request->getFile('foto');
        $namaFoto = 'users.png';
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('profil/', $namaFoto);
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'nip' => $this->request->getPost('nip'),
            'ttl' => $this->request->getPost('ttl'),
            'jenis_kelamin' => $this->request->getPost('gender'),
            'agama' => $this->request->getPost('agama'),
            'role' => strtolower($this->request->getPost('role')),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan'),
            'alamat' => $this->request->getPost('alamat'),
            'no_hp' => $this->request->getPost('no_hp'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'foto' => $namaFoto,
        ];

        $model->insert($data);

        return redirect()->back()->with('swal_success', 'Data guru berhasil disimpan.');
    }
    public function exportguruexcel()
    {
        $userModel = new UsersModel();
        $users = $userModel
            ->select('users.nama, users.username, users.role')
            ->whereIn('users.role', ['kepala sekolah', 'operator', 'guru', 'guru bk'])
            ->orderBy('users.role', 'ASC')
            ->orderBy('users.nama', 'ASC')
            ->findAll();

        $filename = "data_guru_" . date('Y-m-d_H-i-s') . ".xls";

        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Header kolom
        echo "Nama\tUsername\tRole\n";

        foreach ($users as $user) {
            echo $user['nama'] . "\t" . $user['username'] . "\t" . $user['role'] . "\n";
        }
        exit;
    }

    public function hapusguru($id)
    {
        $userModel = new UsersModel();

        // Ambil data guru berdasarkan ID
        $guru = $userModel->find($id);

        if (!$guru) {
            return redirect()->back()->with('swal_error', 'Data guru tidak ditemukan.');
        }

        // Cek jika foto bukan default, hapus dari direktori
        if (!empty($guru['foto']) && $guru['foto'] != 'users.png') {
            $fotoPath = FCPATH . 'profil/' . $guru['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath); // hapus file dari direktori
            }
        }

        // Hapus data dari database
        $userModel->delete($id);

        return redirect()->back()->with('swal_success', 'Data guru berhasil dihapus.');
    }

    public function updateguru($id)
    {
        $userModel = new UsersModel();
        $guruLama = $userModel->find($id);

        if (!$this->validate([
            'nama' => 'required',
            'username' => "required|is_unique[users.username,id_user,{$id}]",
            'nip' => [
                'rules' => 'required|numeric',
                'swal_error' => [
                    'required' => 'NIP wajib diisi',
                    'numeric' => 'NIP hanya boleh berisi angka'
                ]
            ],
            'ttl' => 'required',
            'role' => 'required',
            'pendidikan' => 'required',
            'gender' => 'required',
            'agama' => 'required',
            'alamat' => 'required',
            'no_hp' => [
                'rules' => 'required|numeric',
                'swal_error' => [
                    'required' => 'Nomor HP wajib diisi',
                    'numeric' => 'Nomor HP hanya boleh berisi angka'
                ]
            ],
            'foto' => [
                'rules' => 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'swal_error' => [
                    'max_size' => 'Ukuran foto maksimal 2MB',
                    'is_image' => 'File harus berupa gambar',
                    'mime_in'  => 'Format gambar tidak valid (jpg/jpeg/png)'
                ]
            ]
        ])) {
            $errors = implode('<br>', $this->validator->getErrors());
            return redirect()->to('/guru')->with('swal_error', $errors);
        }


        $foto = $this->request->getFile('foto');
        $namaFoto = $guruLama['foto'];

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Hapus foto lama jika bukan default
            if (
                !empty($guruLama['foto']) &&
                $guruLama['foto'] !== 'users.png' &&
                file_exists('profil/' . $guruLama['foto'])
            ) {
                unlink('profil/' . $guruLama['foto']);
            }


            // Generate nama unik dan simpan di folder 'profil/'
            $namaFoto = $foto->getRandomName();
            $foto->move('profil/', $namaFoto);
        }

        $passwordBaru = $this->request->getPost('password');
        $dataUpdate = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'nip' => $this->request->getPost('nip'),
            'ttl' => $this->request->getPost('ttl'),
            'role' => strtolower($this->request->getPost('role')),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan'),
            'jenis_kelamin' => $this->request->getPost('gender'),
            'agama' => $this->request->getPost('agama'),
            'alamat' => $this->request->getPost('alamat'),
            'no_hp' => $this->request->getPost('no_hp'),
            'foto' => $namaFoto,
        ];

        if (!empty($passwordBaru)) {
            $dataUpdate['password'] = password_hash($passwordBaru, PASSWORD_DEFAULT);
        }

        $userModel->update($id, $dataUpdate);

        return redirect()->to('/guru')->with('swal_success', 'Data guru berhasil diperbarui.');
    }
    public function tambahkelas()
    {
        $validation = \Config\Services::validation();

        // Aturan validasi
        $rules = [
            'nama_kelas' => 'required|is_unique[kelas.nama_kelas]',
            'wali_kelas' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('swal_error', $validation->getErrors());
        }

        // Ambil data dari form
        $data = [
            'nama_kelas' => strtoupper($this->request->getPost('nama_kelas')),
            'id_user'    => $this->request->getPost('wali_kelas') // asumsi field-nya adalah id_user
        ];

        // Simpan ke database
        $kelasModel = new \App\Models\KelasModel();
        $kelasModel->insert($data);

        return redirect()->to('/kelas')->with('swal_success', 'Kelas berhasil ditambahkan.');
    }
    public function hapuskelas()
    {
        $id = $this->request->getPost('id_kelas');
        $kelasModel = new \App\Models\KelasModel();

        if ($kelasModel->delete($id)) {
            return redirect()->back()->with('swal_success', 'Data kelas berhasil dihapus.');
        } else {
            return redirect()->back()->with('swal_error', 'Gagal menghapus data.');
        }
    }
    public function editkelas()
    {
        $id_kelas = $this->request->getPost('id_kelas');
        $nama_kelas = strtoupper($this->request->getPost('nama_kelas'));
        $wali_kelas = $this->request->getPost('wali_kelas');

        $kelasModel = new \App\Models\KelasModel();

        $data = [
            'nama_kelas' => $nama_kelas,
            'id_user' => $wali_kelas,
        ];

        // Cek apakah update berhasil
        if ($kelasModel->update($id_kelas, $data)) {
            return redirect()->to('/kelas')->with('swal_success', 'Data kelas berhasil diperbarui.');
        } else {
            return redirect()->to('/kelas')->with('swal_error', 'Data kelas gagal diperbarui.');
        }
    }
    public function tambahmapel()
    {
        $validation = \Config\Services::validation();
        $mapelModel = new \App\Models\MapelModel();

        $data = [
            'kode_mapel' => strtoupper($this->request->getPost('kode_mapel')),
            'nama_mapel' => strtoupper($this->request->getPost('nama_mapel')),
        ];

        // Atur aturan validasi
        $validationRules = [
            'kode_mapel' => [
                'rules' => 'required|is_unique[mapel.kode_mapel]',
                'errors' => [
                    'required' => 'Kode mata pelajaran harus diisi.',
                    'is_unique' => 'Kode mata pelajaran sudah digunakan.'
                ]
            ],
            'nama_mapel' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama mata pelajaran harus diisi.'
                ]
            ],
        ];

        if (!$validation->setRules($validationRules)->run($data)) {
            // Ambil semua error validasi sebagai array
            $errors = $validation->getErrors();

            return redirect()->back()
                ->withInput()
                ->with('swal_error', $errors); // kirim array error ke swal_error
        }


        // Simpan ke database
        $mapelModel->insert($data);

        return redirect()->to('/mapel')->with('swal_success', 'Mata pelajaran berhasil ditambahkan.');
    }
    public function hapusmapel($kode_mapel)
    {
        $mapelModel = new MapelModel();

        // Cek dulu apakah data ada
        $data = $mapelModel->where('kode_mapel', $kode_mapel)->first();
        if (!$data) {
            return redirect()->to('/mapel')->with('swal_error', ['Data tidak ditemukan.']);
        }

        $mapelModel->where('kode_mapel', $kode_mapel)->delete();

        return redirect()->to('/mapel')->with('swal_success', 'Data berhasil dihapus.');
    }
    public function editmapel()
    {
        $kodeLama = $this->request->getPost('kode_mapel_lama');
        $kodeBaru = $this->request->getPost('kode_mapel');
        $nama = strtoupper($this->request->getPost('nama_mapel'));
        $mapelModel = new MapelModel();

        // Cek apakah kode mapel baru sudah dipakai (dan bukan milik data yg sedang diedit)
        if ($kodeLama !== $kodeBaru) {
            $cek = $mapelModel->where('kode_mapel', $kodeBaru)->first();
            if ($cek) {
                return redirect()->back()->with('swal_error', 'Kode mata pelajaran sudah digunakan!');
            }
        }

        // Update data
        $mapelModel->where('kode_mapel', $kodeLama)->set([
            'kode_mapel' => $kodeBaru,
            'nama_mapel' => $nama,
        ])->update();

        return redirect()->to('/mapel')->with('swal_success', 'Data berhasil diperbarui!');
    }
    public function import()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file->isValid()) {
            return redirect()->back()->with('swal_error', 'File tidak valid.');
        }

        $ext = $file->getExtension();
        if (!in_array($ext, ['csv', 'xls', 'xlsx'])) {
            return redirect()->back()->with('swal_error', 'Format file tidak didukung. Gunakan CSV, XLS, atau XLSX.');
        }

        // Simpan file sementara
        $filePath = WRITEPATH . 'profil/' . $file->getRandomName();
        $file->move(WRITEPATH . 'profil', basename($filePath));

        $userModel  = new UsersModel();
        $kelasModel = new KelasModel();

        $errors = [];
        $successCount = 0;

        try {
            // Load file Excel/CSV menggunakan PhpSpreadsheet
            $spreadsheet = IOFactory::load($filePath);
            $sheetData   = $spreadsheet->getActiveSheet()->toArray();

            foreach ($sheetData as $i => $data) {
                if ($i == 0) continue; // skip header

                $nama          = trim($data[0] ?? '');
                $nisn          = trim($data[1] ?? '');
                $ttl           = trim($data[2] ?? '');
                $jenis_kelamin = trim($data[3] ?? '');
                $agama         = trim($data[4] ?? '');
                $nama_kelas    = trim($data[5] ?? '');
                $alamat        = trim($data[6] ?? '');
                $no_hp         = trim($data[7] ?? '');

                // Validasi minimal
                if (empty($nama) || empty($nisn) || empty($nama_kelas)) {
                    $errors[] = "Baris " . ($i + 1) . " - Data tidak lengkap.";
                    continue;
                }

                // Generate username unik
                $username = strtolower(str_replace(' ', '.', $nama));
                $originalUsername = $username;
                $counter = 1;
                while ($userModel->where('username', $username)->countAllResults() > 0) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }

                // Cari ID kelas
                $kelas = $kelasModel->where('nama_kelas', $nama_kelas)->first();
                $id_kelas = $kelas['id_kelas'] ?? null;
                if (!$id_kelas) {
                    $errors[] = "Baris " . ($i + 1) . " - Kelas '{$nama_kelas}' tidak ditemukan.";
                    continue;
                }

                // Simpan data
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
            unlink($filePath);
            return redirect()->to('/siswa')->with('swal_error', 'Gagal membaca file: ' . $e->getMessage());
        }

        unlink($filePath);

        if (!empty($errors)) {
            return redirect()->to('/siswa')->with('swal_error', "Import gagal dengan beberapa error.");
        }

        return redirect()->to('/siswa')->with('swal_success', "Data siswa berhasil diimport: {$successCount} berhasil.");
    }

    public function tambahsiswa()
    {
        $validationRules = [
            'nama' => 'required|min_length[3]',
            'username' => 'required|is_unique[users.username]',
            'nisn' => 'required|numeric|is_unique[users.nisn]',
            'ttl' => 'required',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'role' => 'required',
            'kelas' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'password' => 'required|min_length[6]',
            'foto' => 'permit_empty|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Gagal menyimpan data.')
                ->with('errors', $this->validator->getErrors());
        }

        $model = new UsersModel();

        $foto = $this->request->getFile('foto');
        $namaFoto = 'users.png';
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move('profil/', $namaFoto);
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'nisn' => $this->request->getPost('nisn'),
            'ttl' => $this->request->getPost('ttl'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'agama' => $this->request->getPost('agama'),
            'role' => strtolower($this->request->getPost('role')),
            'id_kelas' => $this->request->getPost('kelas'),
            'alamat' => $this->request->getPost('alamat'),
            'no_hp' => $this->request->getPost('no_hp'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'foto' => $namaFoto,
        ];

        $model->insert($data);

        return redirect()->to('/siswa')->with('swal_success', 'Data siswa berhasil disimpan.');
    }
    public function updatesiswa($id)
    {
        $siswaModel = new UsersModel();
        $siswaLama = $siswaModel->find($id); // Ambil data lama dulu

        // Validasi input
        $validationRules = [
            'nama'          => 'required',
            'username'      => "required|is_unique[users.username,id_user,{$id}]",
            'nisn'          => "required|numeric|is_unique[users.nisn,id_user,{$id}]",
            'ttl'           => 'required',
            'agama'         => 'required',
            'jenis_kelamin' => 'required',
            'id_kelas'      => 'required',
            'alamat'        => 'required',
            'no_hp'         => 'required',
        ];

        // Validasi file hanya jika ada file diupload
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $validationRules['foto'] = 'is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]';
        }

        if (!$this->validate($validationRules)) {
            $errors = implode('<br>', $this->validator->getErrors());
            return redirect()->to('/siswa')->with('swal_error', $errors);
        }


        $data = [
            'nama'          => $this->request->getPost('nama'),
            'username'      => $this->request->getPost('username'),
            'nisn'          => $this->request->getPost('nisn'),
            'ttl'           => $this->request->getPost('ttl'),
            'agama'         => $this->request->getPost('agama'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'id_kelas'      => $this->request->getPost('id_kelas'),
            'alamat'        => $this->request->getPost('alamat'),
            'no_hp'         => $this->request->getPost('no_hp'),
        ];

        // Password opsional
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Proses foto jika diupload
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFotoBaru = $foto->getRandomName();
            $foto->move('profil/', $namaFotoBaru);
            $data['foto'] = $namaFotoBaru;

            // Hapus foto lama jika bukan default dan file-nya ada
            if (
                $siswaLama &&
                !empty($siswaLama['foto']) &&
                $siswaLama['foto'] !== 'users.png' &&
                file_exists('profil/' . $siswaLama['foto'])
            ) {
                unlink('profil/' . $siswaLama['foto']);
            }
        }

        $siswaModel->update($id, $data);

        return redirect()->to('/siswa')->with('swal_success', 'Data siswa berhasil diperbarui.');
    }
    public function hapussiswa($id)
    {
        $userModel = new UsersModel();
        $siswa = $userModel->find($id);

        if (!$siswa) {
            return redirect()->to('/siswa')->with('swal_error', 'Data siswa tidak ditemukan.');
        }

        // Hapus foto jika bukan default
        if (
            $siswa['foto'] !== 'users.png' &&
            file_exists('profil/' . $siswa['foto'])
        ) {
            unlink('profil/' . $siswa['foto']);
        }

        $userModel->delete($id);
        return redirect()->to('/siswa')->with('swal_success', 'Data siswa berhasil dihapus.');
    }
    public function exportsiswaexcel()
    {
        $userModel = new UsersModel();
        $users = $userModel
            ->select('users.nama, users.username, kelas.nama_kelas')
            ->join('kelas', 'kelas.id_kelas = users.id_kelas', 'left')
            ->where('users.role', 'siswa')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->findAll();

        $filename = "data_siswa_" . date('Y-m-d_H-i-s') . ".xls";

        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Header kolom
        echo "Nama\tUsername\tKelas\n";

        foreach ($users as $user) {
            echo $user['nama'] . "\t" . $user['username'] . "\t" . $user['nama_kelas'] . "\n";
        }
        exit;
    }

    public function tambahjadwalmengajar()
    {
        $model = new JadwalMengajarModel();

        $id_kelas = $this->request->getPost('id_kelas');
        $id_user = $this->request->getPost('id_guru');
        $kode_mapel = $this->request->getPost('kode_mapel');
        $hari = $this->request->getPost('hari');
        $jam_mulai = $this->request->getPost('jam_mulai');
        $jam_selesai = $this->request->getPost('jam_selesai');

        // Cek bentrok untuk guru
        $bentrokGuru = $model
            ->where('hari', $hari)
            ->where('id_user', $id_user)
            ->groupStart()
            ->groupStart()
            ->where("jam_mulai <= ", $jam_mulai)
            ->where("jam_selesai > ", $jam_mulai)
            ->groupEnd()
            ->orGroupStart()
            ->where("jam_mulai < ", $jam_selesai)
            ->where("jam_selesai >= ", $jam_selesai)
            ->groupEnd()
            ->orGroupStart()
            ->where("jam_mulai >= ", $jam_mulai)
            ->where("jam_selesai <= ", $jam_selesai)
            ->groupEnd()
            ->groupEnd()
            ->first();


        if ($bentrokGuru) {
            return redirect()->back()->withInput()->with('swal_error', 'Guru sudah memiliki jadwal di waktu tersebut.');
        }

        // Cek bentrok untuk kelas
        $bentrokKelas = $model
            ->where('hari', $hari)
            ->where('id_kelas', $id_kelas)
            ->groupStart()
            ->groupStart()
            ->where("jam_mulai <=", $jam_mulai)
            ->where("jam_selesai >", $jam_mulai)
            ->groupEnd()
            ->orGroupStart()
            ->where("jam_mulai <", $jam_selesai)
            ->where("jam_selesai >=", $jam_selesai)
            ->groupEnd()
            ->orGroupStart()
            ->where("jam_mulai >=", $jam_mulai)
            ->where("jam_selesai <=", $jam_selesai)
            ->groupEnd()
            ->groupEnd()
            ->first();

        if ($bentrokKelas) {
            return redirect()->back()->withInput()->with('swal_error', 'Kelas sudah memiliki jadwal di waktu tersebut.');
        }

        // Simpan jika tidak bentrok
        $data = [
            'id_kelas'    => $id_kelas,
            'id_user'     => $id_user,
            'kode_mapel'  => $kode_mapel,
            'hari'        => $hari,
            'jam_mulai'   => $jam_mulai,
            'jam_selesai' => $jam_selesai,
        ];

        $model->insert($data);

        return redirect()->to('/jadwal-mengajar')->with('swal_success', 'Jadwal berhasil disimpan.');
    }

    public function hapusjadwalmengajar()
    {
        $id = $this->request->getPost('id_jadwal');
        $model = new JadwalMengajarModel();

        if ($model->delete($id)) {
            return redirect()->back()->with('swal_success', 'Jadwal berhasil dihapus.');
        } else {
            return redirect()->back()->with('swal_error', 'Gagal menghapus jadwal.');
        }
    }
    public function hapusSiswaBerdasarkanKelas()
    {
        $idKelas = $this->request->getPost('id_kelas');

        if (empty($idKelas)) {
            return redirect()->back()->with('swal_error', 'Pilih kelas yang ingin dihapus absensinya.');
        }

        $absensiModel = new AbsensiModel();

        // Hapus semua absensi berdasarkan kelas
        $absensiModel->where('kelas_saat_absen', $idKelas)->delete();

        $affectedRows = $absensiModel->db->affectedRows();

        if ($affectedRows > 0) {
            return redirect()->back()->with('swal_success', $affectedRows . ' data absensi dari kelas ini berhasil dihapus.');
        } else {
            return redirect()->back()->with('swal_error', 'Tidak ada data absensi yang dihapus.');
        }
    }

    public function editabsensi()
    {
        $absensiModel = new AbsensiModel();

        $id_absensi = $this->request->getPost('id_absensi');
        $status     = $this->request->getPost('status');

        if ($absensiModel->update($id_absensi, ['keterangan' => $status])) {
            return redirect()->to('/riwayat-absensi')
                ->with('swal_success', 'Status berhasil diubah.');
        } else {
            return redirect()->to('/riwayat-absensi')
                ->with('swal_error', 'Gagal mengubah status, silakan coba lagi.');
        }
    }
    public function tambahgaleri()
    {
        $file = $this->request->getFile('foto');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'galeri', $newName); // Simpan ke public/galeri

            $galeriModel = new GaleriModel();
            $galeriModel->insert([
                'foto' => $newName
            ]);

            return redirect()->back()->with('swal_success', 'Foto berhasil diupload.');
        } else {
            return redirect()->back()->with('swal_error', 'Gagal mengupload foto.');
        }
    }
    public function hapusgaleri($id_galeri)
    {
        $galeriModel = new \App\Models\GaleriModel();

        $foto = $galeriModel->find($id_galeri);
        if (!$foto) {
            session()->setFlashdata('swal_error', 'Foto tidak ditemukan.');
            return redirect()->back();
        }

        // Hapus file fisik
        $filePath = FCPATH . 'galeri/' . $foto['foto'];
        if (is_file($filePath)) {
            unlink($filePath);
        }

        // Hapus database
        $galeriModel->delete($id_galeri);

        session()->setFlashdata('swal_success', 'Foto berhasil dihapus.');
        return redirect()->back();
    }
    public function exportAbsenExcel()
    {
        $absensiModel = new \App\Models\AbsensiModel();

        // Ambil data absensi lengkap sesuai filter
        $riwayat = $absensiModel
            ->select('users.nama, kelas.nama_kelas, absensi.keterangan')
            ->join('users', 'users.id_user = absensi.id_user')
            ->join('kelas', 'kelas.id_kelas = absensi.kelas_saat_absen')
            ->where('absensi.kelas_saat_absen = users.id_kelas')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->orderBy('users.nama', 'ASC')
            ->findAll();

        // Inisialisasi array rekap per siswa
        $rekap = [];
        foreach ($riwayat as $row) {
            $nama  = trim($row['nama']);
            $kelas = trim($row['nama_kelas']);
            $status = strtolower(trim($row['keterangan']));

            if (!isset($rekap[$kelas][$nama])) {
                $rekap[$kelas][$nama] = [
                    'nama'  => $nama,
                    'kelas' => $kelas,
                    'hadir' => 0,
                    'izin'  => 0,
                    'sakit' => 0,
                    'alpa'  => 0,
                    'total' => 0,
                    'persentase' => 0
                ];
            }

            if ($status === 'hadir' || $status === 'masuk') {
                $rekap[$kelas][$nama]['hadir']++;
            } elseif ($status === 'izin') {
                $rekap[$kelas][$nama]['izin']++;
            } elseif ($status === 'sakit') {
                $rekap[$kelas][$nama]['sakit']++;
            } elseif ($status === 'alpa' || $status === 'alpha') {
                $rekap[$kelas][$nama]['alpa']++;
            }

            $rekap[$kelas][$nama]['total']++;
        }

        // Hitung persentase per kelas
        foreach ($rekap as $kelas => &$siswaList) {
            foreach ($siswaList as &$data) {
                $data['persentase'] = $data['total'] > 0
                    ? round(($data['hadir'] / $data['total']) * 100, 2)
                    : 0;
            }
            unset($data);

            // Urutkan siswa di dalam kelas berdasarkan nama
            uasort($siswaList, function ($a, $b) {
                return strcmp($a['nama'], $b['nama']);
            });
        }
        unset($siswaList);

        // Buat Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Hapus sheet default

        foreach ($rekap as $kelas => $siswaList) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle(substr($kelas, 0, 30)); // Max 31 karakter

            // Header
            $sheet->setCellValue('A1', 'No')
                ->setCellValue('B1', 'Nama Siswa')
                ->setCellValue('C1', 'Kelas')
                ->setCellValue('D1', 'Hadir')
                ->setCellValue('E1', 'Izin')
                ->setCellValue('F1', 'Sakit')
                ->setCellValue('G1', 'Alfa')
                ->setCellValue('H1', 'Total Pertemuan')
                ->setCellValue('I1', '% Kehadiran');

            // Isi data siswa
            $rowNum = 2;
            $no = 1;
            foreach ($siswaList as $stat) {
                $sheet->setCellValue('A' . $rowNum, $no++)
                    ->setCellValue('B' . $rowNum, $stat['nama'])
                    ->setCellValue('C' . $rowNum, $stat['kelas'])
                    ->setCellValue('D' . $rowNum, $stat['hadir'])
                    ->setCellValue('E' . $rowNum, $stat['izin'])
                    ->setCellValue('F' . $rowNum, $stat['sakit'])
                    ->setCellValue('G' . $rowNum, $stat['alpa'])
                    ->setCellValue('H' . $rowNum, $stat['total'])
                    ->setCellValue('I' . $rowNum, $stat['persentase'] . '%');
                $rowNum++;
            }
        }

        // Output file Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="rekap_absensi_per_kelas.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
