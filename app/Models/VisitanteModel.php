<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class VisitanteModel extends Model
{
    protected $table         = 'visitantes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'congregacao_id', 'nome', 'telefone', 'whatsapp', 'email', 'endereco',
        'data_primeira_visita', 'como_conheceu', 'culto_visitado', 'membro_id',
        'status', 'observacoes',
    ];

    public const STATUS_FUNIL = [
        'Primeira visita', 'Contato realizado', 'Nova visita',
        'Acompanhamento', 'Discipulado', 'Membro', 'Desistiu',
    ];

    protected $validationRules = [
        'nome'      => 'required|min_length[3]|max_length[150]',
        'email'     => 'permit_empty|valid_email',
        'status'    => 'required|in_list[Primeira visita,Contato realizado,Nova visita,Acompanhamento,Discipulado,Membro,Desistiu]',
        'congregacao_id' => 'permit_empty|integer|is_not_unique[congregacoes.id]',
        'membro_id' => 'permit_empty|integer|is_not_unique[membros.id]',
    ];

    public function listar(?string $busca, ?string $status, int $limite, int $offset): array
    {
        $builder = $this->builder('visitantes v')
            ->select('v.id, v.nome, v.telefone, v.data_primeira_visita, v.status, c.nome AS congregacao, m.nome AS acompanhamento_responsavel')
            ->join('congregacoes c', 'c.id = v.congregacao_id', 'left')
            ->join('membros m', 'm.id = v.membro_id', 'left')
            ->where('v.deleted_at', null);

        if ($busca) {
            $builder->groupStart()->like('v.nome', $busca)->orLike('v.telefone', $busca)->groupEnd();
        }
        if ($status) {
            $builder->where('v.status', $status);
        }

        return $builder->orderBy('v.data_primeira_visita', 'DESC')->limit($limite, $offset)->get()->getResultArray();
    }

    public function contar(?string $busca, ?string $status): int
    {
        $builder = $this->builder('visitantes v')->where('v.deleted_at', null);
        if ($busca) {
            $builder->groupStart()->like('v.nome', $busca)->groupEnd();
        }
        if ($status) {
            $builder->where('v.status', $status);
        }

        return $builder->countAllResults();
    }
}
