<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTugasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_tugas'     => ['type' => 'INT', 'auto_increment' => true],
            'id_jadwal'    => ['type' => 'INT'], // relasi ke jadwal_mengajar
            'judul_tugas'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'deadline'     => ['type' => 'DATETIME'],
            'catatan'      => ['type' => 'TEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id_tugas', true);

        // Relasi hanya ke jadwal_mengajar
        $this->forge->addForeignKey('id_jadwal', 'jadwal_mengajar', 'id_jadwal', 'CASCADE', 'CASCADE');

        $this->forge->createTable('tugas');
    }

    public function down()
    {
        $this->forge->dropTable('tugas');
    }
}
