<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropTugasTable extends Migration
{
    public function up()
    {
        $this->forge->dropTable('tugas', true);
    }

    public function down()
    {
        //
    }
}
