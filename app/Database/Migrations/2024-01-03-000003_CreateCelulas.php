<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Células / pequenos grupos + participantes.
 */
class CreateCelulas extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'congregacao_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nome'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'codigo'         => ['type' => 'VARCHAR', 'constraint' => 20],
            'lider_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'vice_lider_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'endereco'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'dia_semana'     => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'horario'        => ['type' => 'TIME', 'null' => true],
            'ativo'          => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('codigo');
        $this->forge->addForeignKey('congregacao_id', 'congregacoes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('lider_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('vice_lider_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('celulas', true);

        $this->forge->addField([
            'celula_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'membro_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey(['celula_id', 'membro_id']);
        $this->forge->addForeignKey('celula_id', 'celulas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('membro_id', 'membros', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('celula_membros', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('celula_membros', true);
        $this->forge->dropTable('celulas', true);
    }
}
