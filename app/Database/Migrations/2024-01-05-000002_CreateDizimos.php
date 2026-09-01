<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Dízimos — contribuições de membros.
 */
class CreateDizimos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'membro_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'valor'           => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'data'            => ['type' => 'DATE'],
            'forma_pagamento' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'observacoes'     => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('membro_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('dizimos', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('dizimos', true);
    }
}