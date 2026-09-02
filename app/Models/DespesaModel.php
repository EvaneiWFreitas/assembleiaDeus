<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Despesas — gastos e saídas financeiras da igreja.
 */
class DespesaModel extends Model
{
    protected $table         = 'despesas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'categoria_id', 'descricao', 'valor', 'data', 'forma_pagamento', 'observacoes',
    ];

    public const FORMAS_PAGAMENTO = ['Dinheiro', 'PIX', 'Cartão', 'Transferência', 'Cheque'];

    protected $validationRules = [
        'categoria_id'    => 'permit_empty|integer|is_not_unique[categorias.id]',
        'descricao'       => 'required|min_length[3]|max_length[191]',
        'valor'           => 'required|decimal|greater_than[0]',
        'data'            => 'required|valid_date',
        'forma_pagamento' => 'permit_empty|in_list[Dinheiro,PIX,Cartão,Transferência,Cheque]',
    ];

    /**
     * Listagem com nome da categoria e filtro por mês.
     */
    public function listar(?string $mes = null): array
    {
        $builder = $this->builder('despesas de')
            ->select('de.*, c.nome AS categoria')
            ->join('categorias c', 'c.id = de.categoria_id', 'left')
            ->where('de.deleted_at', null);

        if ($mes && preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $builder->where('DATE_FORMAT(de.data, "%Y-%m")', $mes);
        }

        return $builder->orderBy('de.data', 'DESC')->orderBy('de.id', 'DESC')->get()->getResultArray();
    }

    public function totalNoMes(string $mes): float
    {
        $row = $this->builder('despesas')
            ->selectSum('valor')
            ->where('DATE_FORMAT(data, "%Y-%m")', $mes)
            ->where('deleted_at', null)
            ->get()->getRowArray();

        return (float) ($row['valor'] ?? 0);
    }

    /**
     * Lista as despesas entre dois meses (formato YYYY-MM).
     */
    public function listarPeriodo(string $de, string $ate): array
    {
        return $this->builder('despesas de')
            ->select('de.*, c.nome AS categoria')
            ->join('categorias c', 'c.id = de.categoria_id', 'left')
            ->where('de.deleted_at', null)
            ->where('DATE_FORMAT(de.data, "%Y-%m") >=', $de)
            ->where('DATE_FORMAT(de.data, "%Y-%m") <=', $ate)
            ->orderBy('de.data', 'ASC')
            ->get()->getResultArray();
    }
}