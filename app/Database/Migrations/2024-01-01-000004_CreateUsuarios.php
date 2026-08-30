<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabela de usuários do sistema.
 */
class CreateUsuarios extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'role_id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nome'                 => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'                => ['type' => 'VARCHAR', 'constraint' => 191],
            'senha_hash'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'telefone'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'foto'                 => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ativo'                => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'bloqueado'            => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'tentativas_login'     => ['type' => 'INT', 'constraint' => 5, 'default' => 0],
            'bloqueado_ate'        => ['type' => 'DATETIME', 'null' => true],
            'ultimo_login'         => ['type' => 'DATETIME', 'null' => true],
            'token_reset'          => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true],
            'token_reset_expira'   => ['type' => 'DATETIME', 'null' => true],
            'deve_alterar_senha'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'           => ['type' => 'DATETIME', 'null' => true],
            'updated_at'           => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'           => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('usuarios', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('usuarios', true);
    }
}
