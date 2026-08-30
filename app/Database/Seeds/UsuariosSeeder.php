<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Usuário padrão de desenvolvimento.
 * E-mail: admin@igreja.com  |  Senha: alterar-na-primeira-utilizacao
 * O sistema exige a troca no primeiro acesso (deve_alterar_senha = 1).
 */
class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $existe = $this->db->table('usuarios')->where('email', 'admin@igreja.com')->countAllResults();
        if ($existe > 0) {
            return;
        }

        $superAdmin = $this->db->table('roles')->where('slug', 'super-admin')->get()->getRowArray();

        $this->db->table('usuarios')->insert([
            'role_id'            => $superAdmin['id'],
            'nome'               => 'Administrador',
            'email'              => 'admin@igreja.com',
            'senha_hash'         => password_hash('alterar-na-primeira-utilizacao', PASSWORD_DEFAULT),
            'ativo'              => 1,
            'deve_alterar_senha' => 1,
        ]);
    }
}
