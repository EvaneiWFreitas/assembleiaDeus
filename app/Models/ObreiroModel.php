<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Pastores e obreiros — dados pessoais vêm do membro vinculado.
 */
class ObreiroModel extends Model
{
    protected $table         = 'obreiros';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'membro_id', 'cargo', 'data_conexao', 'congregacao_id', 'numero_registro', 'observacoes', 'ativo',
    ];

    public const CARGOS = [
        'Pastor', 'Presbítero', 'Evangelista', 'Missionário',
        'Diácono', 'Cooperador', 'Líder', 'Obreiro',
    ];

    protected $validationRules = [
        'membro_id' => 'required|integer|is_not_unique[membros.id]',
        'cargo'     => 'required|in_list[Pastor,Presbítero,Evangelista,Missionário,Diácono,Cooperador,Líder,Obreiro]',
        'congregacao_id' => 'permit_empty|integer|is_not_unique[congregacoes.id]',
    ];

    public function listar(?string $busca, ?string $cargo, int $limite, int $offset): array
    {
        $builder = $this->builder('obreiros o')
            ->select('o.id, o.cargo, o.data_conexao, o.ativo, o.numero_registro, m.nome, m.telefone, c.nome AS congregacao')
            ->join('membros m', 'm.id = o.membro_id')
            ->join('congregacoes c', 'c.id = o.congregacao_id', 'left')
            ->where('o.deleted_at', null);

        if ($busca) {
            $builder->like('m.nome', $busca);
        }
        if ($cargo) {
            $builder->where('o.cargo', $cargo);
        }

        return $builder->orderBy('m.nome')->limit($limite, $offset)->get()->getResultArray();
    }

    public function contar(?string $busca, ?string $cargo): int
    {
        $builder = $this->builder('obreiros o')
            ->join('membros m', 'm.id = o.membro_id')
            ->where('o.deleted_at', null);

        if ($busca) {
            $builder->like('m.nome', $busca);
        }
        if ($cargo) {
            $builder->where('o.cargo', $cargo);
        }

        return $builder->countAllResults();
    }
}
