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
        'administrador' => ['dashboard:visualizar', 'usuarios:*', 'roles:*', 'igreja:visualizar', 'igreja:editar', 'banners:*', 'congregacoes:*', 'membros:*', 'visitantes:*', 'obreiros:*', 'departamentos:*', 'ministerios:*', 'celulas:*', 'discipulados:*', 'cursos:*', 'financeiro:*', 'relatorios:*'],
        'pastor'        => ['dashboard:visualizar', 'igreja:visualizar', 'banners:visualizar', 'congregacoes:visualizar', 'congregacoes:cadastrar', 'congregacoes:editar', 'membros:visualizar', 'membros:cadastrar', 'membros:editar', 'visitantes:visualizar', 'visitantes:cadastrar', 'obreiros:visualizar', 'obreiros:cadastrar', 'departamentos:visualizar', 'departamentos:cadastrar', 'departamentos:editar', 'ministerios:visualizar', 'ministerios:cadastrar', 'ministerios:editar', 'celulas:visualizar', 'celulas:cadastrar', 'celulas:editar', 'discipulados:visualizar', 'discipulados:cadastrar', 'discipulados:editar', 'cursos:visualizar', 'cursos:cadastrar', 'cursos:editar', 'relatorios:visualizar', 'relatorios:imprimir'],
        'tesoureiro'    => ['dashboard:visualizar', 'financeiro:*', 'membros:visualizar', 'relatorios:visualizar', 'relatorios:imprimir', 'relatorios:exportar'],
        'secretario'    => ['dashboard:visualizar', 'igreja:visualizar', 'igreja:editar', 'banners:*', 'congregacoes:*', 'membros:*', 'visitantes:*', 'obreiros:visualizar', 'obreiros:cadastrar', 'departamentos:visualizar', 'ministerios:visualizar', 'celulas:visualizar', 'discipulados:visualizar', 'cursos:visualizar', 'cursos:cadastrar', 'relatorios:visualizar', 'relatorios:imprimir'],
        'lider'         => ['dashboard:visualizar', 'membros:visualizar', 'visitantes:visualizar', 'visitantes:cadastrar', 'celulas:visualizar', 'ministerios:visualizar', 'departamentos:visualizar'],
        'operador'      => ['dashboard:visualizar', 'membros:visualizar', 'visitantes:visualizar', 'departamentos:visualizar', 'ministerios:visualizar', 'celulas:visualizar'],
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
