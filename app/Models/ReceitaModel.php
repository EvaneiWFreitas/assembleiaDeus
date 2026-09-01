<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Receitas — outras entradas financeiras (aluguéis, eventos, vendas etc).
 */
class ReceitaModel extends Model
{
    protected $table         = 'receitas';
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
        $builder = $this->builder('receitas r')
            ->select('r.*, c.nome AS categoria')
            ->join('categorias c', 'c.id = r.categoria_id', 'left')
            ->where('r.deleted_at', null);

        if ($mes && preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $builder->where('DATE_FORMAT(r.data, "%Y-%m")', $mes);
        }

        return $builder->orderBy('r.data', 'DESC')->orderBy('r.id', 'DESC')->get()->getResultArray();
    }

    public function totalNoMes(string $mes): float
    {
        $row = $this->builder('receitas')
            ->selectSum('valor')
            ->where('DATE_FORMAT(data, "%Y-%m")', $mes)
            ->where('deleted_at', null)
            ->get()->getRowArray();

        return (float) ($row['valor'] ?? 0);
    }
}