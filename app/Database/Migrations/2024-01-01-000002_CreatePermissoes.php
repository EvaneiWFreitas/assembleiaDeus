<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cria tabela de permissões granulares (modulo.acao).
 */
class CreatePermissoes extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'modulo'     => ['type' => 'VARCHAR', 'constraint' => 80],
            'acao'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'descricao'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['modulo', 'acao']);
        $this->forge->addKey('modulo');
        $this->forge->createTable('permissoes', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('permissoes', true);
    }
}
