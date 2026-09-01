<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class DiscipuladoModel extends Model
{
    protected $table         = 'discipulados';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'discipulo_id', 'discipulador_id', 'data_inicio', 'data_conclusao', 'status', 'observacoes',
    ];

    public const STATUS = ['Em andamento', 'Concluído', 'Cancelado'];

    protected $validationRules = [
        'discipulo_id'    => 'required|integer|is_not_unique[membros.id]',
        'discipulador_id' => 'required|integer|is_not_unique[membros.id]',
        'status'          => 'required|in_list[Em andamento,Concluído,Cancelado]',
    ];

    public function listar(): array
    {
        return $this->builder('discipulados d')
            ->select('d.*, a.nome AS discipulo, b.nome AS discipulador,
                (SELECT COUNT(*) FROM discipulado_encontros e WHERE e.discipulado_id = d.id) AS total_encontros')
            ->join('membros a', 'a.id = d.discipulo_id')
            ->join('membros b', 'b.id = d.discipulador_id')
            ->where('d.deleted_at', null)
            ->orderBy('d.id', 'DESC')
            ->get()->getResultArray();
    }

    public function getEncontros(int $discipuladoId): array
    {
        return $this->db->table('discipulado_encontros')
            ->where('discipulado_id', $discipuladoId)
            ->orderBy('data', 'DESC')
            ->get()->getResultArray();
    }

    public function registrarEncontro(array $dados): void
    {
        $this->db->table('discipulado_encontros')->insert($dados);
    }
}
