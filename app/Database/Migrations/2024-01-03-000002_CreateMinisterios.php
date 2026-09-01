<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Ministérios (Louvor, Mídia, Intercessão, Diaconia...) + participantes.
 */
class CreateMinisterios extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'descricao'  => ['type' => 'TEXT', 'null' => true],
            'lider_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'departamento_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'ativo'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('lider_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('departamento_id', 'departamentos', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('ministerios', true);

        $this->forge->addField([
            'ministerio_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'membro_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'funcao'        => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'ativo'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
        ]);
        $this->forge->addPrimaryKey(['ministerio_id', 'membro_id']);
        $this->forge->addForeignKey('ministerio_id', 'ministerios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('membro_id', 'membros', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ministerio_membros', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('ministerio_membros', true);
        $this->forge->dropTable('ministerios', true);
    }
}
