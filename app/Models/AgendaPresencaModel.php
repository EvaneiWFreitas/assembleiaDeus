<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class AgendaPresencaModel extends Model
{
    protected $table         = 'agenda_presencas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = ['agenda_id', 'membro_id', 'nome', 'telefone', 'confirmado_em'];

    protected $validationRules = [
        'agenda_id' => 'required|integer|is_not_unique[agenda.id]',
        'nome'      => 'required|min_length[2]|max_length[150]',
        'telefone'  => 'permit_empty|max_length[20]',
    ];

    /**
     * Lista as presenças de um evento, com dados do membro vinculado (se houver).
     */
    public function listarPorEvento(int $agendaId): array
    {
        return $this->builder('agenda_presencas p')
            ->select('p.*, m.foto AS membro_foto, m.status AS membro_status')
            ->join('membros m', 'm.id = p.membro_id', 'left')
            ->where('p.agenda_id', $agendaId)
            ->where('p.deleted_at', null)
            ->orderBy('p.confirmado_em', 'DESC')
            ->orderBy('p.created_at', 'DESC')
            ->get()->getResultArray();
    }

    public function contarPorEvento(int $agendaId): int
    {
        return $this->where('agenda_id', $agendaId)
            ->where('deleted_at', null)
            ->countAllResults();
    }
}