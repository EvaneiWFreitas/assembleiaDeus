<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adiciona o link da videoaula às aulas dos cursos online.
 */
class AddVideoAulaCursos extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('curso_aulas', [
            'video_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'conteudo'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('curso_aulas', 'video_url');
    }
}
