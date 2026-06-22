<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;

    protected $allowedFields    = [
        'nama',
        'username',
        'nip',
        'nisn',
        'ttl',
        'role',
        'jenis_kelamin',
        'agama',
        'foto',
        'alamat',
        'no_hp',
        'id_kelas',
        'pendidikan_terakhir',
        'password',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType    = 'array';

    protected $validationRules = [
        'nama'            => 'required|min_length[3]',
        'username'        => 'required',
        'ttl'             => 'required',
        'role' => 'required|in_list[kepala sekolah,operator,guru, guru bk,siswa]',
        'jenis_kelamin'   => 'required|in_list[Laki-Laki,Perempuan]',
        'agama'           => 'required',
        'foto'            => 'required',
        'alamat'          => 'required',
        'password'        => 'required|min_length[6]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getSiswaWithKelas()
    {
        return $this->select('users.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id_kelas = users.id_kelas')
            ->where('users.role', 'siswa') // Hanya siswa
            ->findAll();
    }
}
