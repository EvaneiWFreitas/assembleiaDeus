<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FinanceiroModel;

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
            'membros'      => $this->contarSeguro($db, 'membros', ['deleted_at' => null]),
            'visitantes'   => $this->contarSeguro($db, 'visitantes', ['deleted_at' => null]),
            'congregacoes' => $this->contarSeguro($db, 'congregacoes', ['deleted_at' => null]),
            'usuarios'     => $this->contarSeguro($db, 'usuarios', ['deleted_at' => null]),
            'dizimos_mes'  => $this->somarSeguro($db, 'dizimos', 'valor'),
            'ofertas_mes'  => $this->somarSeguro($db, 'ofertas', 'valor'),
        ];

        // Desempenho do sistema: contagens de todos os módulos
        $this->dados['desempenho'] = [
            'membros'      => $this->dados['cards']['membros'],
            'visitantes'   => $this->dados['cards']['visitantes'],
            'congregacoes' => $this->dados['cards']['congregacoes'],
            'usuarios'     => $this->dados['cards']['usuarios'],
            'obreiros'     => $this->contarSeguro($db, 'obreiros', ['deleted_at' => null]),
            'departamentos' => $this->contarSeguro($db, 'departamentos', ['deleted_at' => null]),
            'ministerios'  => $this->contarSeguro($db, 'ministerios', ['deleted_at' => null]),
            'celulas'      => $this->contarSeguro($db, 'celulas', ['deleted_at' => null]),
            'discipulados' => $this->contarSeguro($db, 'discipulados', ['deleted_at' => null]),
            'cursos'       => $this->contarSeguro($db, 'cursos', ['deleted_at' => null]),
        ];

        // Gráfico financeiro: receitas x despesas dos últimos 6 meses
        $financeiro = new FinanceiroModel();
        $this->dados['grafico'] = $financeiro->desempenhoMensal(6);

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
            ->where('deleted_at', null)
            ->get()->getRowArray();

        return (float) ($row[$campo] ?? 0);
    }
}
