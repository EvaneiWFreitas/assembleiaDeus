<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adiciona o campo de explicação da aula.
 */
class AddExplicacaoAula extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('curso_aulas', [
            'explicacao' => ['type' => 'TEXT', 'null' => true, 'after' => 'conteudo'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('curso_aulas', 'explicacao');
    }
}