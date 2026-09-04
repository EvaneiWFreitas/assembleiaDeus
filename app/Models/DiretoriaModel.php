<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class DiretoriaModel extends Model
{
    protected $table            = 'diretorias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nome', 'cargo', 'foto', 'email', 'telefone', 'whatsapp', 'ordem', 'ativo'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules = [
        'nome'  => 'required|max_length[150]',
        'cargo' => 'required|max_length[100]',
    ];

    public function getAtivos(): array
    {
        return $this->where('deleted_at', null)
            ->where('ativo', 1)
            ->orderBy('ordem', 'ASC')
            ->orderBy('nome', 'ASC')
            ->findAll();
    }

    public function listar(?string $busca = null, int $limite = 20, int $offset = 0): array
    {
        $builder = $this->where('deleted_at', null);

        if ($busca !== null && $busca !== '') {
            $builder->groupStart()
                ->like('nome', $busca)
                ->orLike('cargo', $busca)
                ->orLike('email', $busca)
            ->groupEnd();
        }

        return $builder->orderBy('ordem', 'ASC')
            ->orderBy('nome', 'ASC')
            ->findAll($limite, $offset);
    }

    public function contar(?string $busca = null): int
    {
        $builder = $this->where('deleted_at', null);

        if ($busca !== null && $busca !== '') {
            $builder->groupStart()
                ->like('nome', $busca)
                ->orLike('cargo', $busca)
                ->orLike('email', $busca)
            ->groupEnd();
        }

        return $builder->countAllResults();
    }
}
