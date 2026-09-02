<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Ofertas — contribuições livres e ofertas eventuais.
 */
class OfertaModel extends Model
{
    protected $table         = 'ofertas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'categoria_id', 'valor', 'data', 'forma_pagamento', 'observacoes',
    ];

    public const FORMAS_PAGAMENTO = ['Dinheiro', 'PIX', 'Cartão', 'Transferência', 'Cheque'];

    protected $validationRules = [
        'categoria_id'    => 'permit_empty|integer|is_not_unique[categorias.id]',
        'valor'           => 'required|decimal|greater_than[0]',
        'data'            => 'required|valid_date',
        'forma_pagamento' => 'permit_empty|in_list[Dinheiro,PIX,Cartão,Transferência,Cheque]',
    ];

    /**
     * Listagem com nome da categoria e filtro por mês.
     */
    public function listar(?string $mes = null): array
    {
        $builder = $this->builder('ofertas o')
            ->select('o.*, c.nome AS categoria')
            ->join('categorias c', 'c.id = o.categoria_id', 'left')
            ->where('o.deleted_at', null);

        if ($mes && preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $builder->where('DATE_FORMAT(o.data, "%Y-%m")', $mes);
        }

        return $builder->orderBy('o.data', 'DESC')->orderBy('o.id', 'DESC')->get()->getResultArray();
    }

    public function totalNoMes(string $mes): float
    {
        $row = $this->builder('ofertas')
            ->selectSum('valor')
            ->where('DATE_FORMAT(data, "%Y-%m")', $mes)
            ->where('deleted_at', null)
            ->get()->getRowArray();

        return (float) ($row['valor'] ?? 0);
    }

    /**
     * Lista as ofertas entre dois meses (formato YYYY-MM).
     */
    public function listarPeriodo(string $de, string $ate): array
    {
        return $this->builder('ofertas o')
            ->select('o.*, c.nome AS categoria')
            ->join('categorias c', 'c.id = o.categoria_id', 'left')
            ->where('o.deleted_at', null)
            ->where('DATE_FORMAT(o.data, "%Y-%m") >=', $de)
            ->where('DATE_FORMAT(o.data, "%Y-%m") <=', $ate)
            ->orderBy('o.data', 'ASC')
            ->get()->getResultArray();
    }
}