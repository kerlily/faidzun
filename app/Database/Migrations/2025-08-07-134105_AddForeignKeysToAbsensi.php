<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeysToAbsensi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('absensi', [
            'CONSTRAINT fk_absensi_id_jadwal FOREIGN KEY (id_jadwal) REFERENCES jadwal_mengajar(id_jadwal) ON DELETE CASCADE ON UPDATE CASCADE',
            'CONSTRAINT fk_absensi_id_user FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE ON UPDATE CASCADE',
            'CONSTRAINT fk_absensi_kelas_saat_absen FOREIGN KEY (kelas_saat_absen) REFERENCES kelas(id_kelas) ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    public function down()
    {
        //
    }
}
