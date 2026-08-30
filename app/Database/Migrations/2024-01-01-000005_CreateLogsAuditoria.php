<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Logs de auditoria: quem fez o quê, quando, de onde.
 */
class CreateLogsAuditoria extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'usuario_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'usuario_nome'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'ip'             => ['type' => 'VARCHAR', 'constraint' => 45],
            'acao'           => ['type' => 'VARCHAR', 'constraint' => 30],   // criar, editar, excluir, login...
            'modulo'         => ['type' => 'VARCHAR', 'constraint' => 80],
            'registro_id'    => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'dados_anteriores' => ['type' => 'TEXT', 'null' => true],
            'dados_novos'    => ['type' => 'TEXT', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('usuario_id');
        $this->forge->addKey(['modulo', 'acao']);
        $this->forge->createTable('logs_auditoria', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('logs_auditoria', true);
    }
}
