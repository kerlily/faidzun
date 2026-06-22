<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RebuildAbsensiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_absen' => [
                'type'           => 'INT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'id_jadwal' => [
                'type'     => 'INT',
            ],
            'id_user' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'kelas_saat_absen' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'materi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);

        $this->forge->addKey('id_absen', true);

        $this->forge->addForeignKey('id_jadwal', 'jadwal_mengajar', 'id_jadwal', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_saat_absen', 'kelas', 'id_kelas', 'CASCADE', 'CASCADE');

        $this->forge->createTable('absensi');
    }

    public function down()
    {
        $this->forge->dropTable('absensi');
    }
}
