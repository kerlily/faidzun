<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalMengajarModel extends Model
{
    protected $table = 'jadwal_mengajar';
    protected $primaryKey = 'id_jadwal';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'id_kelas',
        'id_user',
        'kode_mapel',
        'hari',
        'jam_mulai',
        'jam_selesai'
    ];

    public function getJadwalLengkap()
    {
        return $this->select('jadwal_mengajar.*, users.nama as nama_guru, mapel.nama_mapel as mapel, kelas.nama_kelas as kelas')
            ->join('users', 'users.id_user = jadwal_mengajar.id_user')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
            ->orderBy('users.nama', 'ASC')
            ->orderBy('FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu")', '', false)
            ->orderBy('jam_mulai', 'ASC')
            ->findAll();
    }
    public function getJadwalLengkapByUser($user_id)
    {
        return $this->select('jadwal_mengajar.*, users.nama as nama_guru, mapel.nama_mapel as mapel, kelas.nama_kelas as kelas')
            ->join('users', 'users.id_user = jadwal_mengajar.id_user')
            ->join('mapel', 'mapel.kode_mapel = jadwal_mengajar.kode_mapel')
            ->join('kelas', 'kelas.id_kelas = jadwal_mengajar.id_kelas')
            ->where('jadwal_mengajar.id_user', $user_id)
            ->orderBy('FIELD(hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu")', '', false)
            ->orderBy('jam_mulai', 'ASC')
            ->findAll();
    }
}
