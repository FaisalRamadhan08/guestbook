<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTamuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'pesan' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        // Menentukan primary key
        $this->forge->addPrimaryKey('id');

        // Membuat tabel
        $this->forge->createTable('tamu');
    }

    public function down()
    {
        $this->forge->dropTable('tamu');
    }
}
