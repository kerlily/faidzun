<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalMengajarTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jadwal' => [
                'type'           => 'INT',
                'auto_increment' => true
            ],
            'id_kelas' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'id_user' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'kode_mapel' => [
                'type'           => 'VARCHAR',
                'constraint'     => 20,
            ],
            'hari' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'jam_mulai' => [
                'type' => 'TIME',
            ],
            'jam_selesai' => [
                'type' => 'TIME',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id_jadwal', true); // primary key

        // Tambah foreign key
        $this->forge->addForeignKey('id_kelas', 'kelas', 'id_kelas', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kode_mapel', 'mapel', 'kode_mapel', 'CASCADE', 'CASCADE');

        $this->forge->createTable('jadwal_mengajar');
    }

    public function down()
    {
        $this->forge->dropTable('jadwal_mengajar');
    }
}
