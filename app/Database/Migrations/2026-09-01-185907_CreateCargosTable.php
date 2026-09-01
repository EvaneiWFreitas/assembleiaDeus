<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCargosTable extends Migration
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
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'descricao' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'ordem' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'default'        => 0,
            ],
            'ativo' => [
                'type'           => 'TINYINT',
                'constraint'     => 1,
                'default'        => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nome');
        $this->forge->createTable('cargos');
    }

    public function down()
    {
        $this->forge->dropTable('cargos');
    }
}