<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CargosSeeder extends Seeder
{
    private const CARGOS = [
        ['nome' => 'Pastor',       'descricao' => 'Pastor titular ou auxiliar da igreja', 'ordem' => 1],
        ['nome' => 'Presbítero',   'descricao' => 'Presbítero da igreja', 'ordem' => 2],
        ['nome' => 'Evangelista',  'descricao' => 'Evangelista da igreja', 'ordem' => 3],
        ['nome' => 'Missionário',  'descricao' => 'Missionário da igreja', 'ordem' => 4],
        ['nome' => 'Diácono',      'descricao' => 'Diácono da igreja', 'ordem' => 5],
        ['nome' => 'Cooperador',   'descricao' => 'Cooperador da igreja', 'ordem' => 6],
        ['nome' => 'Líder',        'descricao' => 'Líder de departamento/ministério', 'ordem' => 7],
        ['nome' => 'Obreiro',      'descricao' => 'Obreiro geral da igreja', 'ordem' => 8],
    ];

    public function run(): void
    {
        foreach (self::CARGOS as $cargo) {
            $existe = $this->db->table('cargos')
                ->where('nome', $cargo['nome'])
                ->countAllResults();

            if ($existe === 0) {
                $this->db->table('cargos')->insert(array_merge($cargo, [
                    'ativo'      => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]));
            }
        }
    }
}