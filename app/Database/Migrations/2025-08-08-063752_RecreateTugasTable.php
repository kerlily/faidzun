<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecreateTugasTable extends Migration
{
    public function up()
    {
        // Pastikan tabel lama di-drop dulu
        $this->forge->dropTable('tugas', true);

        // Buat ulang tabel tugas
        $this->forge->addField([
            'id_tugas'     => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'id_jadwal'    => ['type' => 'INT', 'constraint' => 11],
            'judul_tugas'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'deadline'     => ['type' => 'DATETIME'],
            'catatan'      => ['type' => 'TEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id_tugas', true);

        // Relasi ke tabel jadwal_mengajar
        $this->forge->addForeignKey('id_jadwal', 'jadwal_mengajar', 'id_jadwal', 'CASCADE', 'CASCADE');

        $this->forge->createTable('tugas');
    }

    public function down()
    {
        // Drop tabel jika rollback
        $this->forge->dropTable('tugas', true);
    }
}
