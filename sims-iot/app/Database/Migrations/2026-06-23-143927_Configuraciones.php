<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Configuraciones extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],

            'temperatura_minima' => [
                'type' => 'FLOAT',
                'default' => 18
            ],

            'temperatura_maxima' => [
                'type' => 'FLOAT',
                'default' => 24
            ],

            'humedad_minima' => [
                'type' => 'FLOAT',
                'default' => 40
            ],

            'humedad_maxima' => [
                'type' => 'FLOAT',
                'default' => 60
            ],

            'ruido_maximo' => [
                'type' => 'FLOAT',
                'default' => 40
            ],

            'movimiento_maximo' => [
                'type' => 'INT',
                'default' => 10
            ]

        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('configuraciones');
    }

    public function down()
    {
        $this->forge->dropTable('configuraciones');
    }
}
