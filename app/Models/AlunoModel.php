<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Alunos públicos cadastrados online para os cursos com videoaulas.
 */
class AlunoModel extends Model
{
    protected $table         = 'alunos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nome', 'email', 'senha_hash', 'telefone', 'cpf', 'ativo',
    ];

    protected $validationRules = [
        'nome'   => 'required|min_length[3]|max_length[150]',
        'email'  => 'required|valid_email|is_unique[alunos.email]|max_length[191]',
        'senha'  => 'required|min_length[6]',
    ];

    /**
     * Busca um aluno ativo pelo e-mail (para login).
     */
    public function porEmail(string $email): ?array
    {
        return $this->where('deleted_at', null)
            ->where('LOWER(email)', strtolower($email))
            ->first();
    }

    /**
     * Cadastra um novo aluno com a senha já criptografada.
     */
    public function cadastrar(array $dados, string $senha): ?int
    {
        $this->skipValidation(true);
        if ($this->insert([
            'nome'       => trim((string) ($dados['nome'] ?? '')),
            'email'      => trim((string) ($dados['email'] ?? '')),
            'senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
            'telefone'   => preg_replace('/\D/', '', (string) ($dados['telefone'] ?? '')),
            'cpf'        => preg_replace('/\D/', '', (string) ($dados['cpf'] ?? '')),
            'ativo'      => 1,
        ]) === false) {
            return null;
        }

        return (int) $this->getInsertID();
    }

    /**
     * Inscrições do aluno nos cursos.
     */
    public function inscricoes(int $alunoId): array
    {
        return $this->db->table('aluno_inscricoes ai')
            ->select('ai.*, c.nome AS curso_nome, c.descricao, c.foto AS curso_foto,
                (SELECT COUNT(*) FROM curso_aulas ca WHERE ca.curso_id = ai.curso_id) AS total_aulas')
            ->join('cursos c', 'c.id = ai.curso_id')
            ->where('ai.aluno_id', $alunoId)
            ->orderBy('ai.data_inscricao', 'DESC')
            ->get()->getResultArray();
    }
}
