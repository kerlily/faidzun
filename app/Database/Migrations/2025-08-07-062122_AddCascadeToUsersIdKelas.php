<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCascadeToUsersIdKelas extends Migration
{
    public function up()
    {
        // Tambahkan foreign key menggunakan raw SQL
        $this->db->query("
            ALTER TABLE users
            ADD CONSTRAINT users_id_kelas_foreign
            FOREIGN KEY (id_kelas)
            REFERENCES kelas(id_kelas)
            ON DELETE CASCADE
            ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        // Hapus foreign key
        $this->db->query("
            ALTER TABLE users
            DROP FOREIGN KEY users_id_kelas_foreign
        ");
    }
}
