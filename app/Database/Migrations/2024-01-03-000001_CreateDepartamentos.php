<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Departamentos da igreja (Jovens, Mulheres, Louvor, Infantil...).
 */
class CreateDepartamentos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'descricao'     => ['type' => 'TEXT', 'null' => true],
            'lider_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'vice_lider_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'cor'           => ['type' => 'VARCHAR', 'constraint' => 7, 'null' => true], // cor no calendário
            'ativo'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lider_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('vice_lider_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('departamentos', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('departamentos', true);
    }
}
