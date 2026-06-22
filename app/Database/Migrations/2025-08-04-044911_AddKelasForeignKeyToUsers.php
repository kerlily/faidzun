<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelasForeignKeyToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'CONSTRAINT fk_id_kelas FOREIGN KEY (id_kelas) REFERENCES kelas(id_kelas) ON DELETE SET NULL ON UPDATE CASCADE'
        ]);
    }


    public function down()
    {
        //
    }
}
