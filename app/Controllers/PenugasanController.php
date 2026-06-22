<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TugasModel;

/**
 * PenugasanController
 * 
 * Mengelola fitur penugasan:
 *  - Tampil daftar tugas (guru/operator/kepala sekolah)
 *  - CRUD tugas (guru & operator)
 *  - Tugas saya (siswa/kepala sekolah/operator)
 */
class PenugasanController extends BaseController
{
    public function index()
    {
        $id_user = session()->get('id_user');
        $role    = session()->get('role');

        $tugasModel     = new TugasModel();
        $dataJadwal     = $tugasModel->getDataJadwalByUser($id_user, $role);
        $semuaDataJadwal= $tugasModel->getAllJadwal();

        // Guru hanya lihat tugasnya sendiri yang deadline belum habis
        if (in_array($role, ['guru', 'guru bk'])) {
            $semuaDataJadwal = array_values(array_filter($semuaDataJadwal, function ($jadwal) use ($id_user) {
                return $jadwal['id_user_jadwal'] == $id_user && strtotime($jadwal['deadline']) >= strtotime(date('Y-m-d'));
            }));
        }

        // Mapel unik untuk dropdown
        $mapelUnik          = [];
        $kodeMapelSudahAda  = [];
        foreach ($dataJadwal as $jadwal) {
            if (!in_array($jadwal['kode_mapel'], $kodeMapelSudahAda)) {
                $mapelUnik[]         = $jadwal;
                $kodeMapelSudahAda[] = $jadwal['kode_mapel'];
            }
        }

        return view('aktivitas/v_penugasan', [
            'dataJadwal'      => $dataJadwal,
            'mapelUnik'       => $mapelUnik,
            'semuaDataJadwal' => $semuaDataJadwal,
        ]);
    }

    public function tambah()
    {
        $model = new TugasModel();

        if (!$this->validate([
            'judul_tugas' => 'required',
            'jadwal_id'   => 'required|integer',
            'deadline'    => 'required',
        ])) {
            return redirect()->to('/penugasan')
                ->with('swal_error', 'Pastikan semua kolom wajib diisi.');
        }

        $model->insert([
            'judul_tugas' => $this->request->getPost('judul_tugas'),
            'id_jadwal'   => $this->request->getPost('jadwal_id'),
            'deadline'    => $this->request->getPost('deadline'),
            'catatan'     => $this->request->getPost('catatan'),
        ]);

        return redirect()->to('/penugasan')->with('swal_success', 'Tugas berhasil ditambahkan.');
    }

    public function edit($id_tugas)
    {
        $id_user    = session()->get('id_user');
        $role       = session()->get('role');
        $tugasModel = new TugasModel();

        $penugasan = $tugasModel->find($id_tugas);
        if (!$penugasan) {
            return redirect()->to('/penugasan')->with('swal_error', 'Data penugasan tidak ditemukan.');
        }

        $dataJadwal = $tugasModel->getDataJadwalByUser($id_user, $role);

        $mapelUnik          = [];
        $kodeMapelSudahAda  = [];
        foreach ($dataJadwal as $jadwal) {
            if (!in_array($jadwal['kode_mapel'], $kodeMapelSudahAda)) {
                $mapelUnik[]         = $jadwal;
                $kodeMapelSudahAda[] = $jadwal['kode_mapel'];
            }
        }

        return view('aktivitas/v_editpenugasan', [
            'penugasan'  => $penugasan,
            'dataJadwal' => $dataJadwal,
            'mapelUnik'  => $mapelUnik,
        ]);
    }

    public function update($id_tugas)
    {
        $model = new TugasModel();

        if (!$this->validate([
            'judul_tugas' => 'required',
            'jadwal_id'   => 'required|integer',
            'deadline'    => 'required',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('swal_error', 'Data gagal diperbarui. Pastikan semua kolom wajib diisi!');
        }

        $model->update($id_tugas, [
            'judul_tugas' => $this->request->getPost('judul_tugas'),
            'id_jadwal'   => $this->request->getPost('jadwal_id'),
            'deadline'    => $this->request->getPost('deadline'),
            'catatan'     => $this->request->getPost('catatan'),
        ]);

        return redirect()->to('/penugasan')->with('swal_success', 'Tugas berhasil diperbarui.');
    }

    public function hapus($id_tugas)
    {
        $model = new TugasModel();

        if ($model->delete($id_tugas)) {
            return redirect()->to('/penugasan')->with('swal_success', 'Tugas berhasil dihapus!');
        }

        return redirect()->to('/penugasan')->with('swal_error', 'Gagal menghapus tugas.');
    }

    public function tugasSaya()
    {
        $tugasModel = new TugasModel();
        $user       = session()->get();
        $role       = $user['role'];

        if ($role === 'kepala sekolah' || $role === 'operator') {
            $tugas = $tugasModel
                ->join('jadwal_mengajar', 'jadwal_mengajar.id_jadwal = tugas.id_jadwal')
                ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
                ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
                ->select('tugas.*, mapel.nama_mapel, kelas.nama_kelas')
                ->findAll();
        } elseif ($role === 'siswa') {
            $id_kelas = $user['id_kelas'];
            $tugas    = $tugasModel
                ->join('jadwal_mengajar', 'jadwal_mengajar.id_jadwal = tugas.id_jadwal')
                ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
                ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
                ->select('tugas.*, mapel.nama_mapel, kelas.nama_kelas')
                ->where('kelas.id_kelas', $id_kelas)
                ->where('tugas.deadline >=', date('Y-m-d H:i:s'))
                ->findAll();
        } else {
            $tugas = [];
        }

        return view('aktivitas/v_tugassaya', ['tugas' => $tugas]);
    }
}