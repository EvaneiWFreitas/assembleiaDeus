<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CargoModel extends Model
{
    protected $table            = 'cargos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nome', 'descricao', 'ordem', 'ativo'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules = [
        'nome'     => 'required|max_length[100]|is_unique[cargos.nome,id,{id}]',
        'ordem'    => 'permit_empty|integer',
        'ativo'    => 'in_list[0,1]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required'   => 'O nome do cargo é obrigatório.',
            'max_length' => 'O nome deve ter no máximo 100 caracteres.',
            'is_unique'  => 'Este cargo já está cadastrado.',
        ],
    ];

    protected $skipValidation = false;

    public function getAtivos(?string $orderBy = 'ordem'): array
    {
        return $this->where('ativo', 1)
            ->where('deleted_at', null)
            ->orderBy($orderBy)
            ->findAll();
    }

    public function getDropdown(): array
    {
        return array_column($this->getAtivos(), 'nome', 'id');
    }

    public function listar(?string $busca = null, int $limite = 50, int $offset = 0): array
    {
        $builder = $this->where('deleted_at', null);

        if ($busca) {
            $builder->groupStart()
                ->like('nome', $busca)
                ->orLike('descricao', $busca)
                ->groupEnd();
        }

        return $builder->orderBy('ordem')->orderBy('nome')
            ->limit($limite, $offset)
            ->get()->getResultArray();
    }

    public function contar(?string $busca = null): int
    {
        $builder = $this->where('deleted_at', null);

        if ($busca) {
            $builder->groupStart()
                ->like('nome', $busca)
                ->orLike('descricao', $busca)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }
}