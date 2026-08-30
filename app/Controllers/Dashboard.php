<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Dashboard principal do sistema.
 */
class Dashboard extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        // Cards — módulos futuros retornam 0 quando a tabela ainda não existir
        $this->dados['cards'] = [
            'membros'     => $this->contarSeguro($db, 'membros'),
            'visitantes'  => $this->contarSeguro($db, 'visitantes'),
            'congregacoes' => $this->contarSeguro($db, 'congregacoes'),
            'usuarios'    => $this->contarSeguro($db, 'usuarios', ['deleted_at' => null]),
            'dizimos_mes' => $this->somarSeguro($db, 'dizimos', 'valor'),
            'ofertas_mes' => $this->somarSeguro($db, 'ofertas', 'valor'),
        ];

        // Aniversariantes do mês (quando módulo de membros existir)
        $this->dados['aniversariantes'] = [];

        return view('dashboard/index', $this->dados);
    }

    private function contarSeguro($db, string $tabela, array $where = []): int
    {
        if (! $db->tableExists($tabela)) {
            return 0;
        }
        $builder = $db->table($tabela);
        if ($where !== []) {
            $builder->where($where);
        }

        return $builder->countAllResults();
    }

    private function somarSeguro($db, string $tabela, string $campo): float
    {
        if (! $db->tableExists($tabela)) {
            return 0.0;
        }
        $row = $db->table($tabela)
            ->selectSum($campo)
            ->where('MONTH(data)', date('n'))
            ->where('YEAR(data)', date('Y'))
            ->get()->getRowArray();

        return (float) ($row[$campo] ?? 0);
    }
}
