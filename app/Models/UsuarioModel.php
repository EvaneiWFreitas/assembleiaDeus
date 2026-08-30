<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table         = 'usuarios';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'role_id', 'nome', 'email', 'senha_hash', 'telefone', 'foto',
        'ativo', 'bloqueado', 'tentativas_login', 'bloqueado_ate',
        'ultimo_login', 'token_reset', 'token_reset_expira', 'deve_alterar_senha',
    ];

    protected $validationRules = [
        'nome'  => 'required|min_length[3]|max_length[150]',
        'email' => 'required|valid_email|max_length[191]|is_unique[usuarios.email,id,{id}]',
        'role_id' => 'required|integer|is_not_unique[roles.id]',
    ];

    protected $beforeInsert = ['hashSenha'];
    protected $beforeUpdate = ['hashSenha'];

    protected function hashSenha(array $data): array
    {
        if (isset($data['data']['senha']) && $data['data']['senha'] !== '') {
            $data['data']['senha_hash'] = password_hash($data['data']['senha'], PASSWORD_DEFAULT);
            $data['data']['deve_alterar_senha'] = 1;
        }
        unset($data['data']['senha']);

        return $data;
    }

    /**
     * Busca usuário por e-mail (inclui inativos para mensagens precisas).
     */
    public function buscarPorEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Lista usuários com o nome do perfil (para DataTables/listagem).
     */
    public function listarComRoles(?string $busca = null, int $limite = 20, int $offset = 0): array
    {
        $builder = $this->builder('usuarios u')
            ->select('u.id, u.nome, u.email, u.telefone, u.ativo, u.bloqueado, u.ultimo_login, r.nome as role')
            ->join('roles r', 'r.id = u.role_id');

        if ($busca !== null && $busca !== '') {
            $builder->groupStart()
                ->like('u.nome', $busca)
                ->orLike('u.email', $busca)
                ->groupEnd();
        }

        return $builder->limit($limite, $offset)->orderBy('u.id', 'DESC')->get()->getResultArray();
    }
}
