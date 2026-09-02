<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class AgendaModel extends Model
{
    protected $table         = 'agenda';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'titulo', 'descricao', 'data_inicio', 'data_fim', 'hora_inicio',
        'hora_fim', 'local', 'tipo', 'responsavel', 'foto_responsavel',
        'congregacao_id', 'recorrencia', 'cor', 'observacoes', 'status',
    ];

    protected $validationRules = [
        'titulo'      => 'required|min_length[3]|max_length[150]',
        'data_inicio' => 'required|valid_date',
        'status'      => 'required|in_list[Pendente,Confirmado,Cancelado,Concluído]',
        'tipo'        => 'required|max_length[50]',
    ];

    /**
     * Listagem com filtros e nome da congregação.
     */
    public function listar(?string $busca, ?string $status, ?string $tipo, ?int $congregacaoId, int $limite, int $offset): array
    {
        $builder = $this->builder('agenda a')
            ->select('a.*, c.nome AS congregacao')
            ->join('congregacoes c', 'c.id = a.congregacao_id', 'left')
            ->where('a.deleted_at', null);

        if ($busca) {
            $builder->groupStart()
                ->like('a.titulo', $busca)
                ->orLike('a.responsavel', $busca)
                ->orLike('a.local', $busca)
                ->groupEnd();
        }
        if ($status) {
            $builder->where('a.status', $status);
        }
        if ($tipo) {
            $builder->where('a.tipo', $tipo);
        }
        if ($congregacaoId) {
            $builder->where('a.congregacao_id', $congregacaoId);
        }

        return $builder->orderBy('a.data_inicio', 'DESC')
            ->orderBy('a.hora_inicio', 'ASC')
            ->limit($limite, $offset)
            ->get()->getResultArray();
    }

    public function contar(?string $busca, ?string $status, ?string $tipo, ?int $congregacaoId): int
    {
        $builder = $this->builder('agenda a')->where('a.deleted_at', null);

        if ($busca) {
            $builder->groupStart()
                ->like('a.titulo', $busca)
                ->orLike('a.responsavel', $busca)
                ->groupEnd();
        }
        if ($status) {
            $builder->where('a.status', $status);
        }
        if ($tipo) {
            $builder->where('a.tipo', $tipo);
        }
        if ($congregacaoId) {
            $builder->where('a.congregacao_id', $congregacaoId);
        }

        return $builder->countAllResults();
    }

    /**
     * Retorna eventos para exibição em calendário (FullCalendar ou similar).
     */
    public function eventosCalendario(?string $dataInicio, ?string $dataFim): array
    {
        $builder = $this->builder('agenda a')
            ->select('a.id, a.titulo, a.data_inicio, a.data_fim, a.hora_inicio, a.hora_fim, a.local, a.tipo, a.status, a.cor, a.responsavel')
            ->where('a.deleted_at', null)
            ->where('a.status !=', 'Cancelado');

        if ($dataInicio) {
            $builder->where('a.data_inicio >=', $dataInicio);
        }
        if ($dataFim) {
            $builder->where('a.data_inicio <=', $dataFim);
        }

        return $builder->orderBy('a.data_inicio', 'ASC')
            ->orderBy('a.hora_inicio', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Dropdown id => titulo para selects.
     */
    public function getDropdown(): array
    {
        $rows = $this->where('status !=', 'Cancelado')
            ->orderBy('data_inicio', 'DESC')
            ->findAll();

        return array_column($rows, 'titulo', 'id');
    }

    /**
     * Próximos eventos (para dashboard).
     */
    public function proximosEventos(int $limite = 5): array
    {
        return $this->where('deleted_at', null)
            ->where('status !=', 'Cancelado')
            ->where('data_inicio >=', date('Y-m-d'))
            ->orderBy('data_inicio', 'ASC')
            ->orderBy('hora_inicio', 'ASC')
            ->limit($limite)
            ->findAll();
    }
}