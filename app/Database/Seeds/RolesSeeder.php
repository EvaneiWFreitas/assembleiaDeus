<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Cria perfis padrão do sistema.
 */
class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nome' => 'Super Administrador', 'slug' => 'super-admin', 'descricao' => 'Acesso total ao sistema', 'super_admin' => 1],
            ['nome' => 'Administrador', 'slug' => 'administrador', 'descricao' => 'Acesso administrativo', 'super_admin' => 0],
            ['nome' => 'Pastor', 'slug' => 'pastor', 'descricao' => 'Informações pastorais e membros', 'super_admin' => 0],
            ['nome' => 'Tesoureiro', 'slug' => 'tesoureiro', 'descricao' => 'Acesso ao financeiro', 'super_admin' => 0],
            ['nome' => 'Secretário', 'slug' => 'secretario', 'descricao' => 'Cadastros e documentos', 'super_admin' => 0],
            ['nome' => 'Líder', 'slug' => 'lider', 'descricao' => 'Acesso ao seu departamento/célula', 'super_admin' => 0],
            ['nome' => 'Operador', 'slug' => 'operador', 'descricao' => 'Acesso limitado', 'super_admin' => 0],
        ];

        foreach ($roles as $role) {
            $existe = $this->db->table('roles')->where('slug', $role['slug'])->countAllResults();
            if ($existe === 0) {
                $this->db->table('roles')->insert($role);
            }
        }
    }
}
