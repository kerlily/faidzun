<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTanggaltoAbsensi extends Migration
{
    public function up() {}

    public function down()
    {
        $this->forge->dropColumn('absensi', 'tanggal');
    }
}
