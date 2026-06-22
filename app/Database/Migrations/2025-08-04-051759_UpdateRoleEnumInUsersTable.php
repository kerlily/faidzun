<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateRoleEnumInUsersTable extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE users MODIFY role ENUM('kepala sekolah', 'operator', 'guru', 'siswa', 'guru bk')");
    }

    public function down()
    {
        // Rollback ke ENUM sebelumnya jika perlu
        $this->db->query("ALTER TABLE users MODIFY role ENUM('kepala sekolah', 'operator', 'guru', 'siswa')");
    }
}
