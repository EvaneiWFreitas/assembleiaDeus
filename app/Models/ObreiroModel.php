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
        'membro_id', 'cargo', 'data_conexao', 'congregacao_id', 'numero_registro', 'foto', 'observacoes', 'ativo',
    ];

    protected $validationRules = [
        'membro_id'      => 'required|integer|is_not_unique[membros.id]',
        'cargo'          => 'required',
        'congregacao_id' => 'permit_empty|integer|is_not_unique[congregacoes.id]',
    ];

    public function listar(?string $busca, ?string $cargo, int $limite, int $offset): array
    {
        $builder = $this->builder('obreiros o')
            ->select('o.id, o.cargo, o.data_conexao, o.ativo, o.numero_registro, o.foto, m.nome, m.cpf, m.rg, m.data_nascimento, m.tipo_membro, m.telefone, m.foto AS foto_membro, c.nome AS congregacao')
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

    /**
     * Retorna array de cargos ativos para validação e dropdowns.
     */
    public function getCargosAtivos(): array
    {
        $cargoModel = new CargoModel();
        return array_column($cargoModel->getAtivos(), 'nome');
    }

    /**
     * Regras de validação dinâmicas baseadas nos cargos cadastrados.
     */
    public function getValidationRules(array $options = []): array
    {
        $cargos = $this->getCargosAtivos();
        $inList = ! empty($cargos) ? implode(',', $cargos) : 'Pastor';

        return [
            'membro_id'      => 'required|integer|is_not_unique[membros.id]',
            'cargo'          => "required|in_list[$inList]",
            'congregacao_id' => 'permit_empty|integer|is_not_unique[congregacoes.id]',
        ];
    }

    /**
     * Dropdown id => nome dos cargos ativos.
     */
    public function getDropdownCargos(): array
    {
        $cargoModel = new CargoModel();
        return $cargoModel->getDropdown();
    }

    /**
     * Registro completo do obreiro com dados do membro vinculado e da congregação.
     */
    public function buscarCompleto(int $id): ?array
    {
        return $this->builder('obreiros o')
            ->select('o.*, m.nome, m.cpf, m.rg, m.data_nascimento, m.sexo, m.tipo_membro, m.telefone, m.foto AS foto_membro, c.nome AS congregacao')
            ->join('membros m', 'm.id = o.membro_id')
            ->join('congregacoes c', 'c.id = o.congregacao_id', 'left')
            ->where('o.id', $id)
            ->where('o.deleted_at', null)
            ->get()->getRowArray();
    }
}