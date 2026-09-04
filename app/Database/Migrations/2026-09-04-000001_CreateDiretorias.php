<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDiretorias extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'cargo'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'foto'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'telefone'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'whatsapp'   => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'ordem'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'ativo'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('diretorias');
    }

    public function down()
    {
        $this->forge->dropTable('diretorias');
    }
}
