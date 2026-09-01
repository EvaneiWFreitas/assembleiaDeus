<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Dízimos — contribuições de membros.
 */
class DizimoModel extends Model
{
    protected $table         = 'dizimos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'membro_id', 'valor', 'data', 'forma_pagamento', 'observacoes',
    ];

    public const FORMAS_PAGAMENTO = ['Dinheiro', 'PIX', 'Cartão', 'Transferência', 'Cheque'];

    protected $validationRules = [
        'membro_id'       => 'required|integer|is_not_unique[membros.id]',
        'valor'           => 'required|decimal|greater_than[0]',
        'data'            => 'required|valid_date',
        'forma_pagamento' => 'permit_empty|in_list[Dinheiro,PIX,Cartão,Transferência,Cheque]',
    ];

    /**
     * Listagem com nome do membro e filtro por mês.
     */
    public function listar(?string $mes = null): array
    {
        $builder = $this->builder('dizimos d')
            ->select('d.*, m.nome AS membro')
            ->join('membros m', 'm.id = d.membro_id', 'left')
            ->where('d.deleted_at', null);

        if ($mes && preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $builder->where('DATE_FORMAT(d.data, "%Y-%m")', $mes);
        }

        return $builder->orderBy('d.data', 'DESC')->orderBy('d.id', 'DESC')->get()->getResultArray();
    }

    public function totalNoMes(string $mes): float
    {
        $row = $this->builder('dizimos')
            ->selectSum('valor')
            ->where('DATE_FORMAT(data, "%Y-%m")', $mes)
            ->where('deleted_at', null)
            ->get()->getRowArray();

        return (float) ($row['valor'] ?? 0);
    }
}