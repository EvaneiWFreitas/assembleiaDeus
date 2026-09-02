<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CursoModel extends Model
{
    protected $table         = 'cursos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nome', 'descricao', 'professor_id', 'data_inicio', 'data_fim', 'vagas', 'status', 'ativo',
    ];

    public const STATUS = ['Planejado', 'Em andamento', 'Concluído', 'Cancelado'];

    protected $validationRules = [
        'nome'         => 'required|min_length[3]|max_length[150]',
        'professor_id' => 'permit_empty|integer|is_not_unique[membros.id]',
        'status'       => 'required|in_list[Planejado,Em andamento,Concluído,Cancelado]',
    ];

    public function listar(): array
    {
        return $this->builder('cursos c')
            ->select('c.*, m.nome AS professor,
                (SELECT COUNT(*) FROM curso_alunos a WHERE a.curso_id = c.id) AS total_alunos,
                (SELECT COUNT(*) FROM curso_aulas  x WHERE x.curso_id = c.id) AS total_aulas')
            ->join('membros m', 'm.id = c.professor_id', 'left')
            ->where('c.deleted_at', null)
            ->orderBy('c.id', 'DESC')
            ->get()->getResultArray();
    }

    public function getAulas(int $cursoId): array
    {
        return $this->db->table('curso_aulas')->where('curso_id', $cursoId)->orderBy('data')->get()->getResultArray();
    }

    public function getAlunos(int $cursoId): array
    {
        return $this->db->table('curso_alunos a')
            ->select('a.*, m.nome')
            ->join('membros m', 'm.id = a.membro_id')
            ->where('a.curso_id', $cursoId)
            ->orderBy('m.nome')
            ->get()->getResultArray();
    }

    public function adicionarAula(array $dados): void
    {
        $this->db->table('curso_aulas')->insert($dados);
    }

    /**
     * Cursos ativos exibidos no site público.
     */
    public function listarPublico(): array
    {
        return $this->builder('cursos c')
            ->select('c.*, m.nome AS professor,
                (SELECT COUNT(*) FROM curso_alunos a WHERE a.curso_id = c.id) +
                (SELECT COUNT(*) FROM aluno_inscricoes i WHERE i.curso_id = c.id) AS total_alunos,
                (SELECT COUNT(*) FROM curso_aulas x WHERE x.curso_id = c.id) AS total_aulas')
            ->join('membros m', 'm.id = c.professor_id', 'left')
            ->where('c.deleted_at', null)
            ->where('c.ativo', 1)
            ->whereIn('c.status', ['Planejado', 'Em andamento'])
            ->orderBy('c.id', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Aulas de um curso para a área do aluno (inclui o link da videoaula).
     */
    public function getAulasPublicas(int $cursoId): array
    {
        return $this->db->table('curso_aulas')
            ->where('curso_id', $cursoId)
            ->orderBy('data')
            ->orderBy('id')
            ->get()->getResultArray();
    }

    /**
     * Verifica se um aluno já está inscrito no curso.
     */
    public function alunoInscrito(int $cursoId, int $alunoId): bool
    {
        return $this->db->table('aluno_inscricoes')
            ->where('curso_id', $cursoId)
            ->where('aluno_id', $alunoId)
            ->countAllResults() > 0;
    }

    /**
     * Inscreve um aluno público no curso. Retorna false se já inscrito.
     */
    public function inscreverAluno(int $cursoId, int $alunoId): bool
    {
        if ($this->alunoInscrito($cursoId, $alunoId)) {
            return false;
        }

        $this->db->table('aluno_inscricoes')->insert([
            'curso_id'       => $cursoId,
            'aluno_id'       => $alunoId,
            'data_inscricao' => date('Y-m-d'),
            'status'         => 'Matriculado',
        ]);

        return true;
    }

    public function matricular(int $cursoId, int $membroId): bool
    {
        $existe = $this->db->table('curso_alunos')
            ->where('curso_id', $cursoId)->where('membro_id', $membroId)
            ->countAllResults();

        if ($existe > 0) {
            return false;
        }

        $this->db->table('curso_alunos')->insert([
            'curso_id'       => $cursoId,
            'membro_id'      => $membroId,
            'data_matricula' => date('Y-m-d'),
            'status'         => 'Matriculado',
        ]);

        return true;
    }

    public function atualizarAluno(int $alunoId, array $dados): void
    {
        $this->db->table('curso_alunos')->where('id', $alunoId)->update($dados);
    }

    public function removerAluno(int $alunoId): void
    {
        $this->db->table('curso_alunos')->delete($alunoId);
    }
}
