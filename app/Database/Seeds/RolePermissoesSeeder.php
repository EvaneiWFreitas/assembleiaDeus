<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Víncula permissões a cada perfil conforme sua função.
 * Super admin não precisa (tem acesso total no código).
 */
class RolePermissoesSeeder extends Seeder
{
    private const MAPA = [
        'administrador' => ['dashboard:visualizar', 'usuarios:*', 'roles:*', 'igreja:visualizar', 'igreja:editar', 'congregacoes:*', 'membros:*', 'visitantes:*', 'obreiros:*', 'financeiro:*', 'relatorios:*'],
        'pastor'        => ['dashboard:visualizar', 'igreja:visualizar', 'congregacoes:visualizar', 'congregacoes:cadastrar', 'congregacoes:editar', 'membros:visualizar', 'membros:cadastrar', 'membros:editar', 'visitantes:visualizar', 'visitantes:cadastrar', 'obreiros:visualizar', 'obreiros:cadastrar', 'relatorios:visualizar', 'relatorios:imprimir'],
        'tesoureiro'    => ['dashboard:visualizar', 'financeiro:*', 'membros:visualizar', 'relatorios:visualizar', 'relatorios:imprimir', 'relatorios:exportar'],
        'secretario'    => ['dashboard:visualizar', 'igreja:visualizar', 'igreja:editar', 'congregacoes:*', 'membros:*', 'visitantes:*', 'obreiros:visualizar', 'obreiros:cadastrar', 'relatorios:visualizar', 'relatorios:imprimir'],
        'lider'         => ['dashboard:visualizar', 'membros:visualizar', 'visitantes:visualizar', 'visitantes:cadastrar'],
        'operador'      => ['dashboard:visualizar', 'membros:visualizar', 'visitantes:visualizar'],
    ];

    public function run(): void
    {
        foreach (self::MAPA as $slug => $permissoes) {
            $role = $this->db->table('roles')->where('slug', $slug)->get()->getRowArray();
            if ($role === null) {
                continue;
            }

            foreach ($permissoes as $item) {
                [$modulo, $acao] = explode(':', $item);

                if ($acao === '*') {
                    $rows = $this->db->table('permissoes')->where('modulo', $modulo)->get()->getResultArray();
                } else {
                    $rows = $this->db->table('permissoes')
                        ->where('modulo', $modulo)->where('acao', $acao)
                        ->get()->getResultArray();
                }

                foreach ($rows as $p) {
                    $existe = $this->db->table('role_permissoes')
                        ->where('role_id', $role['id'])->where('permissao_id', $p['id'])
                        ->countAllResults();

                    if ($existe === 0) {
                        $this->db->table('role_permissoes')->insert([
                            'role_id'      => $role['id'],
                            'permissao_id' => $p['id'],
                        ]);
                    }
                }
            }
        }
    }
}
