<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class MembroModel extends Model
{
    protected $table         = 'membros';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'congregacao_id', 'nome', 'nome_social', 'cpf', 'rg', 'data_nascimento',
        'sexo', 'estado_civil', 'nacionalidade', 'naturalidade', 'foto',
        'telefone', 'whatsapp', 'email', 'cep', 'logradouro', 'numero',
        'complemento', 'bairro', 'cidade', 'estado', 'data_conversao',
        'data_batismo', 'local_batismo', 'igreja_anterior', 'data_recebimento',
        'tipo_membro', 'cargo', 'observacoes', 'status',
    ];

    protected $validationRules = [
        'nome'      => 'required|min_length[3]|max_length[150]',
        'cpf'       => 'permit_empty',
        'email'     => 'permit_empty|valid_email',
        'sexo'      => 'permit_empty|in_list[M,F]',
        'status'    => 'required|in_list[Ativo,Inativo,Transferido,Desligado,Falecido]',
        'congregacao_id' => 'permit_empty|integer|is_not_unique[congregacoes.id]',
    ];

    /**
     * Listagem com nome da congregação + filtros.
     */
    public function listar(?string $busca, ?string $status, ?int $congregacaoId, int $limite, int $offset): array
    {
        $builder = $this->builder('membros m')
            ->select('m.id, m.nome, m.cpf, m.rg, m.data_nascimento, m.telefone, m.status, m.tipo_membro, m.foto, c.nome AS congregacao')
            ->join('congregacoes c', 'c.id = m.congregacao_id', 'left')
            ->where('m.deleted_at', null);

        if ($busca) {
            $builder->groupStart()
                ->like('m.nome', $busca)->orLike('m.cpf', $busca)->orLike('m.email', $busca)
                ->groupEnd();
        }
        if ($status) {
            $builder->where('m.status', $status);
        }
        if ($congregacaoId) {
            $builder->where('m.congregacao_id', $congregacaoId);
        }

        return $builder->orderBy('m.nome')->limit($limite, $offset)->get()->getResultArray();
    }

    public function contar(?string $busca, ?string $status, ?int $congregacaoId): int
    {
        $builder = $this->builder('membros m')->where('m.deleted_at', null);

        if ($busca) {
            $builder->groupStart()->like('m.nome', $busca)->orLike('m.cpf', $busca)->groupEnd();
        }
        if ($status) {
            $builder->where('m.status', $status);
        }
        if ($congregacaoId) {
            $builder->where('m.congregacao_id', $congregacaoId);
        }

        return $builder->countAllResults();
    }

    /**
     * Dropdown id => nome (apenas ativos, exceto o próprio registro em edição).
     */
    public function getDropdown(?int $excluirId = null): array
    {
        $builder = $this->where('status', 'Ativo')->orderBy('nome');
        if ($excluirId) {
            $builder->where('id !=', $excluirId);
        }
        $rows = $builder->findAll();

        return array_column($rows, 'nome', 'id');
    }

    /**
     * Verifica se o CPF (somente dígitos) já está cadastrado,
     * ignorando registros soft-deleted e opcionalmente o próprio id.
     */
    public function cpfEmUso(string $cpf, ?int $ignorarId = null): bool
    {
        $builder = $this->builder()->where('cpf', $cpf)->where('deleted_at', null);

        if ($ignorarId !== null) {
            $builder->where('id !=', $ignorarId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Valida os dígitos verificadores de um CPF (somente dígitos).
     */
    public function cpfValido(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }
            $dv = 11 - ($soma % 11);
            if ($dv >= 10) {
                $dv = 0;
            }
            if ((int) $cpf[$t] !== $dv) {
                return false;
            }
        }

        return true;
    }
}
