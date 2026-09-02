<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agenda — compromissos, reuniões e eventos da igreja.
 */
class CreateAgenda extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'titulo'           => ['type' => 'VARCHAR', 'constraint' => 150],
            'descricao'        => ['type' => 'TEXT', 'null' => true],
            'data_inicio'      => ['type' => 'DATE'],
            'data_fim'         => ['type' => 'DATE', 'null' => true],
            'hora_inicio'      => ['type' => 'TIME', 'null' => true],
            'hora_fim'         => ['type' => 'TIME', 'null' => true],
            'local'            => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'tipo'             => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Reunião'],  // Reunião|Culto|Evento|Retiro|Encontro|Outro
            'responsavel'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'congregacao_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'recorrencia'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],          // null|semanal|quinzenal|mensal
            'cor'              => ['type' => 'VARCHAR', 'constraint' => 7, 'default' => '#0d6efd'],   // hex color
            'observacoes'      => ['type' => 'TEXT', 'null' => true],
            'status'           => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Pendente'],  // Pendente|Confirmado|Cancelado|Concluído
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('congregacao_id', 'congregacoes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addKey('data_inicio');
        $this->forge->addKey('status');
        $this->forge->addKey('tipo');
        $this->forge->createTable('agenda', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('agenda', true);
    }
}