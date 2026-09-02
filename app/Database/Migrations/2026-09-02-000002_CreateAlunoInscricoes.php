<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Inscrições de alunos públicos em cursos (área online com videoaulas).
 */
class CreateAlunoInscricoes extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'curso_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'aluno_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'data_inscricao'=> ['type' => 'DATE', 'null' => true],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Matriculado'], // Matriculado|Cursando|Concluído|Evadido
            'certificado'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['curso_id', 'aluno_id']);
        $this->forge->addForeignKey('curso_id', 'cursos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('aluno_id', 'alunos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('aluno_inscricoes', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('aluno_inscricoes', true);
    }
}
