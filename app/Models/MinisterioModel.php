<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class MinisterioModel extends Model
{
    protected $table         = 'ministerios';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = ['nome', 'descricao', 'lider_id', 'departamento_id', 'ativo'];

    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[100]',
        'lider_id'        => 'permit_empty|integer|is_not_unique[membros.id]',
        'departamento_id' => 'permit_empty|integer|is_not_unique[departamentos.id]',
    ];

    public function listarComLideres(): array
    {
        return $this->builder('ministerios m')
            ->select('m.*, l.nome AS lider, d.nome AS departamento,
                (SELECT COUNT(*) FROM ministerio_membros mm WHERE mm.ministerio_id = m.id AND mm.ativo = 1) AS total_participantes')
            ->join('membros l', 'l.id = m.lider_id', 'left')
            ->join('departamentos d', 'd.id = m.departamento_id', 'left')
            ->where('m.deleted_at', null)
            ->orderBy('m.nome')
            ->get()->getResultArray();
    }

    public function getParticipantes(int $ministerioId): array
    {
        return $this->db->table('ministerio_membros mm')
            ->select('mm.membro_id, mm.funcao, mm.ativo, m.nome')
            ->join('membros m', 'm.id = mm.membro_id')
            ->where('mm.ministerio_id', $ministerioId)
            ->get()->getResultArray();
    }

    public function sincronizarParticipantes(int $ministerioId, array $membroIds, array $funcoes = []): void
    {
        $this->db->table('ministerio_membros')->where('ministerio_id', $ministerioId)->delete();

        $rows = [];
        foreach ($membroIds as $membroId) {
            $rows[] = [
                'ministerio_id' => $ministerioId,
                'membro_id'     => (int) $membroId,
                'funcao'        => $funcoes[$membroId] ?? null,
                'ativo'         => 1,
            ];
        }
        if ($rows !== []) {
            $this->db->table('ministerio_membros')->insertBatch($rows);
        }
    }
}
