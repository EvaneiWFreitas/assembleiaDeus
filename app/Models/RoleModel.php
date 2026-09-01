<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $allowedFields    = ['nome', 'slug', 'descricao', 'super_admin'];

    protected $validationRules = [
        'nome' => 'required|min_length[3]|max_length[80]',
        'slug' => 'required|alpha_dash|max_length[80]',
    ];

    /**
     * Retorna as permissões vinculadas a um perfil.
     */
    public function getPermissoes(int $roleId): array
    {
        return $this->db->table('role_permissoes rp')
            ->join('permissoes p', 'p.id = rp.permissao_id')
            ->where('rp.role_id', $roleId)
            ->get()->getResultArray();
    }

    /**
     * Sincroniza as permissões de um perfil.
     */
    public function sincronizarPermissoes(int $roleId, array $permissaoIds): void
    {
        $builder = $this->db->table('role_permissoes');
        $builder->where('role_id', $roleId)->delete();

        $rows = array_map(static fn ($id) => ['role_id' => $roleId, 'permissao_id' => (int) $id], $permissaoIds);
        if ($rows !== []) {
            $builder->insertBatch($rows);
        }
    }
}
