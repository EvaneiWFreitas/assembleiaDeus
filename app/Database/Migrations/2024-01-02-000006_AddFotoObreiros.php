<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adiciona o campo de foto aos pastores e obreiros.
 */
class AddFotoObreiros extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('obreiros', [
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('obreiros', 'foto');
    }
}