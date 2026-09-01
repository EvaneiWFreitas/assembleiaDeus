<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Ofertas — contribuições livres e ofertas eventuais.
 */
class CreateOfertas extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'categoria_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
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
        $this->forge->createTable('ofertas', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('ofertas', true);
    }
}