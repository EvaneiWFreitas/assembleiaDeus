<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Receitas — outras entradas financeiras além de dízimos e ofertas
 * (aluguéis, eventos, vendas, contribuições gerais etc).
 */
class CreateReceitas extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'categoria_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'descricao'       => ['type' => 'VARCHAR', 'constraint' => 191],
            'valor'           => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'data'            => ['type' => 'DATE'],
            'forma_pagamento' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'observacoes'     => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('categoria_id', 'categorias', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('receitas', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('receitas', true);
    }
}