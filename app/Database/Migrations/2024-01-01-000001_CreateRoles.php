<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cria tabela de perfis de usuário (roles).
 */
class CreateRoles extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 80],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 80],
            'descricao'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'super_admin' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('roles', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('roles', true);
    }
}
