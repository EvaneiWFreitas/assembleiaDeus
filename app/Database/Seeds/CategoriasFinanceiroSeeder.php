<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Categorias de lançamento financeiro padrão.
 */
class CategoriasFinanceiroSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Oferta de Culto',      'tipo' => 'entrada', 'ativo' => 1],
            ['nome' => 'Oferta Missionária',   'tipo' => 'entrada', 'ativo' => 1],
            ['nome' => 'Contribuição Voluntária', 'tipo' => 'entrada', 'ativo' => 1],
            ['nome' => 'Venda de Produtos',    'tipo' => 'entrada', 'ativo' => 1],
            ['nome' => 'Energia Elétrica',     'tipo' => 'saida',   'ativo' => 1],
            ['nome' => 'Água',                 'tipo' => 'saida',   'ativo' => 1],
            ['nome' => 'Internet / Telefone',  'tipo' => 'saida',   'ativo' => 1],
            ['nome' => 'Material de Limpeza',  'tipo' => 'saida',   'ativo' => 1],
            ['nome' => 'Manutenção',           'tipo' => 'saida',   'ativo' => 1],
            ['nome' => 'Ação Social',          'tipo' => 'saida',   'ativo' => 1],
            ['nome' => 'Salários / Diárias',   'tipo' => 'saida',   'ativo' => 1],
        ];

        foreach ($categorias as $categoria) {
            $existe = $this->db->table('categorias')
                ->where('nome', $categoria['nome'])
                ->countAllResults();
            if ($existe === 0) {
                $this->db->table('categorias')->insert($categoria);
            }
        }
    }
}