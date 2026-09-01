<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Categorias de lançamento financeiro (entradas/saídas).
 */
class CategoriaModel extends Model
{
    protected $table         = 'categorias';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nome', 'tipo', 'ativo',
    ];

    public const TIPOS = ['entrada', 'saida'];

    protected $validationRules = [
        'nome'  => 'required|min_length[3]|max_length[80]',
        'tipo'  => 'required|in_list[entrada,saida]',
    ];

    public function listar(?string $tipo = null): array
    {
        $builder = $this->where('deleted_at', null);

        if ($tipo !== null && in_array($tipo, self::TIPOS, true)) {
            $builder->where('tipo', $tipo);
        }

        return $builder->orderBy('tipo', 'ASC')->orderBy('nome', 'ASC')->findAll();
    }

    public function getDropdown(?string $tipo = null): array
    {
        $builder = $this->where('deleted_at', null)->where('ativo', 1);
        if ($tipo !== null && in_array($tipo, self::TIPOS, true)) {
            $builder->where('tipo', $tipo);
        }
        $rows = $builder->orderBy('nome')->findAll();

        return array_column($rows, 'nome', 'id');
    }
}