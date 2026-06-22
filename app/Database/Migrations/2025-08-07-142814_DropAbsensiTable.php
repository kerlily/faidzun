<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropAbsensiTable extends Migration
{
    public function up()
    {
        // Drop table absensi jika ada
        $this->forge->dropTable('absensi', true);
    }

    public function down()
    {
        // Optional: Buat ulang tabel absensi jika di-rollback
        // Isi sesuai struktur awal tabel absensi jika diperlukan
    }
}
