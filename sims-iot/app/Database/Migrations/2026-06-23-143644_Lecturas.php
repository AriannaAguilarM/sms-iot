<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Lecturas extends Migration
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

            'temperatura' => [
                'type' => 'FLOAT'
            ],

            'humedad' => [
                'type' => 'FLOAT'
            ],

            'ruido' => [
                'type' => 'FLOAT'
            ],

            'movimiento' => [
                'type' => 'INT',
                'constraint' => 11
            ],

            'indice_sueno' => [
                'type' => 'FLOAT'
            ],

            'fecha' => [
                'type' => 'DATETIME'
            ]

        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('lecturas');
    }

    public function down()
    {
        $this->forge->dropTable('lecturas');
    }
}
