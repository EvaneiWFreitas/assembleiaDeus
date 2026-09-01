<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cursos (Escola Bíblica, Formação de Obreiros...) com aulas e alunos.
 */
class CreateCursos extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nome'          => ['type' => 'VARCHAR', 'constraint' => 150],
            'descricao'     => ['type' => 'TEXT', 'null' => true],
            'professor_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'data_inicio'   => ['type' => 'DATE', 'null' => true],
            'data_fim'      => ['type' => 'DATE', 'null' => true],
            'vagas'         => ['type' => 'INT', 'constraint' => 5, 'null' => true],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Planejado'], // Planejado|Em andamento|Concluído|Cancelado
            'ativo'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('professor_id', 'membros', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('cursos', true);

        // Aulas
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'curso_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'data'       => ['type' => 'DATE', 'null' => true],
            'tema'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'conteudo'   => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('curso_id', 'cursos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('curso_aulas', true);

        // Matrículas (aluno + frequência + nota + certificado)
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'curso_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'membro_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'data_matricula' => ['type' => 'DATE', 'null' => true],
            'frequencia'   => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true], // %
            'nota_final'   => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'certificado'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'status'       => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'Matriculado'], // Matriculado|Cursando|Aprovado|Reprovado|Evadido
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['curso_id', 'membro_id']);
        $this->forge->addForeignKey('curso_id', 'cursos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('membro_id', 'membros', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('curso_alunos', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('curso_alunos', true);
        $this->forge->dropTable('curso_aulas', true);
        $this->forge->dropTable('cursos', true);
    }
}
