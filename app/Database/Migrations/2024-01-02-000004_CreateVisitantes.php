<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Visitantes + histórico de acompanhamento.
 */
class CreateVisitantes extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'congregacao_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'nome'                => ['type' => 'VARCHAR', 'constraint' => 150],
            'telefone'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'whatsapp'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email'               => ['type' => 'VARCHAR', 'constraint' => 191, 'null' => true],
            'endereco'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'data_primeira_visita'=> ['type' => 'DATE', 'null' => true],
            'como_conheceu'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'culto_visitado'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'membro_id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true], // responsável pelo acompanhamento
            'status'              => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Primeira visita'],
            // Status do funil: Primeira visita → Contato realizado → Nova visita →
            // Acompanhamento → Discipulado → Membro → Desistiu
            'observacoes'         => ['type' => 'TEXT', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('congregacao_id', 'congregacoes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('membro_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addKey('status');
        $this->forge->createTable('visitantes', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('visitantes', true);
    }
}
