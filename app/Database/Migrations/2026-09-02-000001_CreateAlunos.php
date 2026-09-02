<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Alunos públicos (cadastrados online) — identidade e credenciais de acesso
 * à área de cursos online com videoaulas.
 */
class CreateAlunos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'           => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 191],
            'senha_hash'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'telefone'       => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'cpf'            => ['type' => 'VARCHAR', 'constraint' => 14, 'null' => true],
            'ativo'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('alunos', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('alunos', true);
    }
}
