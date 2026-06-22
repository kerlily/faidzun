<?php

namespace App\Models;

use CodeIgniter\Model;

class MapelModel extends Model
{
    protected $table      = 'mapel';
    protected $primaryKey = 'kode_mapel';

    protected $useTimestamps = true;

    protected $allowedFields = ['kode_mapel', 'nama_mapel'];
}
