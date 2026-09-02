<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Presenças confirmadas nos eventos da Agenda.
 */
class CreateAgendaPresencas extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'agenda_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'membro_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nome'             => ['type' => 'VARCHAR', 'constraint' => 150],
            'telefone'         => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'confirmado_em'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('agenda_id', 'agenda', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('membro_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addKey('agenda_id');
        $this->forge->createTable('agenda_presencas', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('agenda_presencas', true);
    }
}