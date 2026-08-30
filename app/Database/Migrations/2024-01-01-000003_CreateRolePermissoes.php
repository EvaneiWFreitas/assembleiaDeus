<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabela pivô: quais permissões cada perfil possui.
 */
class CreateRolePermissoes extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'role_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'permissao_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey(['role_id', 'permissao_id']);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permissao_id', 'permissoes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('role_permissoes', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('role_permissoes', true);
    }
}
