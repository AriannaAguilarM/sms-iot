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
                'auto_increment' => true,
            ],

            'temp_min' => [
                'type'    => 'FLOAT',
                'default' => 18,
            ],

            'temp_max' => [
                'type'    => 'FLOAT',
                'default' => 24,
            ],

            'hum_min' => [
                'type'    => 'FLOAT',
                'default' => 40,
            ],

            'hum_max' => [
                'type'    => 'FLOAT',
                'default' => 60,
            ],

            'ruido_max' => [
                'type'    => 'FLOAT',
                'default' => 40,
            ],

            'mov_max' => [
                'type'    => 'INT',
                'default' => 10,
            ],

        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('configuraciones');
    }

    public function down()
    {
        $this->forge->dropTable('configuraciones');
    }
}