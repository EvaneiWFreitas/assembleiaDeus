<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CelulaModel extends Model
{
    protected $table         = 'celulas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'congregacao_id', 'nome', 'codigo', 'lider_id', 'vice_lider_id',
        'endereco', 'dia_semana', 'horario', 'ativo',
    ];

    public const DIAS = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];

    protected $validationRules = [
        'nome'   => 'required|min_length[3]|max_length[100]',
        'codigo' => 'required|alpha_numeric_punct|max_length[20]|is_unique[celulas.codigo,id,{id}]',
        'lider_id'       => 'permit_empty|integer|is_not_unique[membros.id]',
        'vice_lider_id'  => 'permit_empty|integer|is_not_unique[membros.id]',
        'congregacao_id' => 'permit_empty|integer|is_not_unique[congregacoes.id]',
        'dia_semana'     => 'permit_empty|in_list[Domingo,Segunda,Terça,Quarta,Quinta,Sexta,Sábado]',
    ];

    public function listarComLideres(?string $busca = null): array
    {
        $builder = $this->builder('celulas c')
            ->select('c.*, l.nome AS lider, v.nome AS vice_lider, g.nome AS congregacao,
                (SELECT COUNT(*) FROM celula_membros cm WHERE cm.celula_id = c.id) AS total_participantes')
            ->join('membros l', 'l.id = c.lider_id', 'left')
            ->join('membros v', 'v.id = c.vice_lider_id', 'left')
            ->join('congregacoes g', 'g.id = c.congregacao_id', 'left')
            ->where('c.deleted_at', null);

        if ($busca) {
            $builder->groupStart()->like('c.nome', $busca)->orLike('c.codigo', $busca)->groupEnd();
        }

        return $builder->orderBy('c.nome')->get()->getResultArray();
    }

    public function getParticipantes(int $celulaId): array
    {
        return $this->db->table('celula_membros cm')
            ->select('cm.membro_id AS id, cm.membro_id, m.nome, m.telefone, m.status AS status_membro')
            ->join('membros m', 'm.id = cm.membro_id')
            ->where('cm.celula_id', $celulaId)
            ->get()->getResultArray();
    }

    public function adicionarParticipante(int $celulaId, int $membroId): void
    {
        $existe = $this->db->table('celula_membros')
            ->where('celula_id', $celulaId)
            ->where('membro_id', $membroId)
            ->countAllResults();

        if ($existe === 0) {
            $this->db->table('celula_membros')->insert([
                'celula_id' => $celulaId,
                'membro_id' => $membroId,
            ]);
        }
    }

    public function removerParticipante(int $celulaId, int $membroId): void
    {
        $this->db->table('celula_membros')
            ->where('celula_id', $celulaId)
            ->where('membro_id', $membroId)
            ->delete();
    }

    public function sincronizarParticipantes(int $celulaId, array $membroIds): void
    {
        $this->db->table('celula_membros')->where('celula_id', $celulaId)->delete();

        $rows = array_map(static fn ($id) => ['celula_id' => $celulaId, 'membro_id' => (int) $id], $membroIds);
        if ($rows !== []) {
            $this->db->table('celula_membros')->insertBatch($rows);
        }
    }
}
