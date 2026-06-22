<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id_absen';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'id_jadwal',
        'id_user',
        'tanggal',
        'kelas_saat_absen',
        'keterangan',
        'materi'
    ];

    protected $useTimestamps = true; // Aktifkan timestamps
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getRiwayatAbsensi($filterKelas = null, $filterMapel = null)
    {
        return $this->db->table($this->table)
            ->select('
            absensi.*,
            users.nama,
            users.jenis_kelamin,
            kelas.nama_kelas,
            mapel.nama_mapel
        ')
            ->join('users', 'users.id_user = absensi.id_user', 'left')
            ->join('kelas', 'kelas.id_kelas = users.id_kelas', 'left')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id_jadwal = absensi.id_jadwal', 'left')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel', 'left')
            ->when($filterKelas && $filterKelas !== 'Semua', function ($query) use ($filterKelas) {
                return $query->where('kelas.id_kelas', $filterKelas);
            })
            ->when($filterMapel && $filterMapel !== 'Semua', function ($query) use ($filterMapel) {
                return $query->where('mapel.kode_mapel', $filterMapel);
            })
            ->orderBy('tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }
}
