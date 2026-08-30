<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Congregações ligadas à igreja matriz.
 */
class CreateCongregacoes extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'igreja_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nome'            => ['type' => 'VARCHAR', 'constraint' => 150],
            'codigo'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'telefone'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'           => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'cep'             => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => true],
            'logradouro'      => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'numero'          => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'bairro'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cidade'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'estado'          => ['type' => 'CHAR', 'constraint' => 2, 'null' => true],
            'pastor_responsavel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'lider_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'data_abertura'   => ['type' => 'DATE', 'null' => true],
            'ativo'           => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('codigo');
        $this->forge->addForeignKey('igreja_id', 'igrejas', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('congregacoes', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('congregacoes', true);
    }
}
