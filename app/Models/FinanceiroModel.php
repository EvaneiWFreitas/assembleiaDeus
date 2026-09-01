<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Consultas consolidadas do módulo Financeiro.
 */
class FinanceiroModel extends Model
{
    protected $table = 'categorias';

    public function resumoMes(string $mes): array
    {
        $dizimos = (new DizimoModel())->totalNoMes($mes);
        $ofertas = (new OfertaModel())->totalNoMes($mes);
        $receitas = (new ReceitaModel())->totalNoMes($mes);
        $despesas = (new DespesaModel())->totalNoMes($mes);

        return [
            'dizimos'  => $dizimos,
            'ofertas'  => $ofertas,
            'receitas' => $receitas,
            'entradas' => $dizimos + $ofertas + $receitas,
            'despesas' => $despesas,
            'saldo'    => $dizimos + $ofertas + $receitas - $despesas,
        ];
    }

    /**
     * Últimos lançamentos unificados (dízimos, ofertas, receitas e despesas).
     */
    public function ultimosLancamentos(int $limite = 10): array
    {
        $sql = '
            (SELECT d.data, d.valor, m.nome AS descricao, "dizimo" AS tipo
                FROM dizimos d LEFT JOIN membros m ON m.id = d.membro_id
                WHERE d.deleted_at IS NULL)
            UNION ALL
            (SELECT o.data, o.valor, CONCAT("Oferta — ", COALESCE(c.nome, "geral")) AS descricao, "oferta" AS tipo
                FROM ofertas o LEFT JOIN categorias c ON c.id = o.categoria_id
                WHERE o.deleted_at IS NULL)
            UNION ALL
            (SELECT r.data, r.valor, CONCAT("Receita — ", r.descricao) AS descricao, "receita" AS tipo
                FROM receitas r WHERE r.deleted_at IS NULL)
            UNION ALL
            (SELECT de.data, de.valor, de.descricao, "despesa" AS tipo
                FROM despesas de WHERE de.deleted_at IS NULL)
            ORDER BY data DESC, valor DESC
            LIMIT ' . (int) $limite;

        return $this->db->query($sql)->getResultArray();
    }

    /**
     * Desempenho mensal (receitas x despesas) dos últimos N meses, incluindo
     * o mês atual. Retorna os rótulos (ex.: "Set/26") e os valores por mês.
     */
    public function desempenhoMensal(int $meses = 6): array
    {
        $rotulos   = [];
        $entradas  = [];
        $despesas  = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $mes = date('Y-m', strtotime("-$i months"));
            $rotulos[]  = date('M/y', strtotime($mes . '-01'));
            $entradas[] = (new DizimoModel())->totalNoMes($mes)
                + (new OfertaModel())->totalNoMes($mes)
                + (new ReceitaModel())->totalNoMes($mes);
            $despesas[] = (new DespesaModel())->totalNoMes($mes);
        }

        return [
            'rotulos'  => $rotulos,
            'entradas' => $entradas,
            'despesas' => $despesas,
        ];
    }
}