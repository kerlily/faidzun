<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasModel extends Model
{
    protected $table            = 'tugas';
    protected $primaryKey       = 'id_tugas';
    protected $allowedFields    = [
        'judul_tugas',
        'id_jadwal',
        'deadline',
        'catatan'
    ];
    protected $useTimestamps = true;

    public function getDataJadwalByUser($id_user, $role)
    {
        $builder = $this->db->table('jadwal_mengajar');
        $builder->select('jadwal_mengajar.id_jadwal, mapel.kode_mapel, mapel.nama_mapel, kelas.id_kelas, kelas.nama_kelas, users.id_user, users.nama AS nama_guru');
        $builder->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel');
        $builder->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas');
        $builder->join('users', 'users.id_user = jadwal_mengajar.id_user');

        // Filter jika role-nya bukan kepala atau operator
        if (strtolower($role) !== 'kepala sekolah' && strtolower($role) !== 'operator') {
            $builder->where('jadwal_mengajar.id_user', $id_user);
        }


        $builder->groupBy('jadwal_mengajar.id_jadwal'); // Optional, agar tidak duplikat
        return $builder->get()->getResultArray();
    }
    public function getAllJadwal()
    {
        return $this->select('
            tugas.*,
            jadwal_mengajar.kode_mapel,
            jadwal_mengajar.id_kelas,
            users.id_user AS id_user_guru,
            jadwal_mengajar.id_user AS id_user_jadwal,
            users.nama AS nama_guru,
            mapel.nama_mapel,
            kelas.nama_kelas
        ')

            ->join('jadwal_mengajar', 'jadwal_mengajar.id_jadwal = tugas.id_jadwal')
            ->join('users', 'users.id_user = jadwal_mengajar.id_user')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
            ->orderBy('tugas.deadline', 'ASC')
            ->findAll();
    }
}
