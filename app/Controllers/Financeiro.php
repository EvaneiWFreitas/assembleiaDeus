<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\FinanceiroModel;

/**
 * Painel Financeiro — resumo mensal e últimos lançamentos.
 */
class Financeiro extends BaseController
{
    private FinanceiroModel $financeiro;

    public function __construct()
    {
        $this->financeiro = new FinanceiroModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('financeiro', 'visualizar');

        $mes = $this->mesSelecionado();

        $this->dados['titulo']    = 'Painel Financeiro';
        $this->dados['mes']       = $mes;
        $this->dados['resumo']    = $this->financeiro->resumoMes($mes);
        $this->dados['lancamentos'] = $this->financeiro->ultimosLancamentos(10);

        return view('financeiro/index', $this->dados);
    }

    private function mesSelecionado(): string
    {
        $mes = (string) $this->request->getGet('mes');
        if (preg_match('/^\d{4}-\d{2}$/', $mes)) {
            return $mes;
        }

        return date('Y-m');
    }
}