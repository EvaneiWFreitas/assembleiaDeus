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
        'cpf'       => 'permit_empty|exact_length[14]|is_unique[membros.cpf,id,{id}]',
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
            ->select('m.id, m.nome, m.cpf, m.data_nascimento, m.telefone, m.status, m.tipo_membro, c.nome AS congregacao')
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
}
