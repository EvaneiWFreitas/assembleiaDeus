<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Pastores e obreiros — cargo eclesiástico + vínculo com membro.
 * Um obreiro é sempre vinculado a um registro de membro (evita duplicação de dados pessoais).
 */
class CreateObreiros extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'membro_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'cargo'              => ['type' => 'VARCHAR', 'constraint' => 40], // Pastor|Presbítero|Evangelista|Missionário|Diácono|Cooperador|Líder|Obreiro
            'data_conexao'       => ['type' => 'DATE', 'null' => true],        // data de consagração
            'congregacao_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'numero_registro'    => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'observacoes'        => ['type' => 'TEXT', 'null' => true],
            'ativo'              => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'         => ['type' => 'DATETIME', 'null' => true],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('membro_id');
        $this->forge->addForeignKey('membro_id', 'membros', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('congregacao_id', 'congregacoes', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('obreiros', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('obreiros', true);
    }
}
