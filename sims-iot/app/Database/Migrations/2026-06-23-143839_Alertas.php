<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Alertas extends Migration
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

            'mensaje' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],

            'nivel' => [
                'type'       => 'VARCHAR',
                'constraint' => 50
            ],

            'fecha' => [
                'type' => 'DATETIME'
            ]

        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('alertas');
    }

    public function down()
    {
        $this->forge->dropTable('alertas');
    }
}
