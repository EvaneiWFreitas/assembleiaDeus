<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class DepartamentoModel extends Model
{
    protected $table         = 'departamentos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = ['nome', 'descricao', 'lider_id', 'vice_lider_id', 'cor', 'ativo'];

    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[100]',
        'lider_id'      => 'permit_empty|integer|is_not_unique[membros.id]',
        'vice_lider_id' => 'permit_empty|integer|is_not_unique[membros.id]',
    ];

    public function listarComLideres(): array
    {
        return $this->builder('departamentos d')
            ->select('d.*, l.nome AS lider, v.nome AS vice_lider')
            ->join('membros l', 'l.id = d.lider_id', 'left')
            ->join('membros v', 'v.id = d.vice_lider_id', 'left')
            ->where('d.deleted_at', null)
            ->orderBy('d.nome')
            ->get()->getResultArray();
    }
}
