<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Categorias de lançamento financeiro (entradas e saídas).
 */
class CreateCategorias extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'       => ['type' => 'VARCHAR', 'constraint' => 80],
            'tipo'       => ['type' => 'ENUM', 'constraint' => ['entrada', 'saida'], 'default' => 'entrada'],
            'ativo'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categorias', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('categorias', true);
    }
}