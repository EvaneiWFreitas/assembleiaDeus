<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adiciona foto do responsável ao módulo Agenda.
 */
class AddFotoResponsavelAgenda extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('agenda', [
            'foto_responsavel' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'responsavel',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('agenda', 'foto_responsavel');
    }
}