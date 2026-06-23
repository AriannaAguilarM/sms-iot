<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Usuarios extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100
            ],

            'correo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100
            ],

            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ]

        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('correo');

        $this->forge->createTable('usuarios');
    }

    public function down()
    {
        $this->forge->dropTable('usuarios');
    }
}
