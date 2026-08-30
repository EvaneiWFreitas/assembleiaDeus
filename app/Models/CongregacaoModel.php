<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CongregacaoModel extends Model
{
    protected $table         = 'congregacoes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'igreja_id', 'nome', 'codigo', 'telefone', 'email', 'cep', 'logradouro',
        'numero', 'bairro', 'cidade', 'estado', 'pastor_responsavel_id',
        'lider_id', 'data_abertura', 'ativo',
    ];

    protected $validationRules = [
        'nome'   => 'required|min_length[3]|max_length[150]',
        'codigo' => 'required|alpha_numeric_punct|max_length[20]|is_unique[congregacoes.codigo,id,{id}]',
        'email'  => 'permit_empty|valid_email',
    ];

    /**
     * Lista com contagem de membros ativos.
     */
    public function listarComContagem(?string $busca = null): array
    {
        $builder = $this->builder('congregacoes c')
            ->select('c.*, (SELECT COUNT(*) FROM membros m WHERE m.congregacao_id = c.id AND m.status = "Ativo" AND m.deleted_at IS NULL) AS total_membros');

        if ($busca) {
            $builder->groupStart()->like('c.nome', $busca)->orLike('c.codigo', $busca)->groupEnd();
        }

        return $builder->where('c.deleted_at', null)->orderBy('c.nome')->get()->getResultArray();
    }

    /**
     * Par id => nome para dropdowns.
     */
    public function getDropdown(): array
    {
        $rows = $this->where('ativo', 1)->orderBy('nome')->findAll();

        return array_column($rows, 'nome', 'id');
    }
}
