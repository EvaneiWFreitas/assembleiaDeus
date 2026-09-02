<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AgendaModel;
use App\Models\AgendaPresencaModel;
use App\Models\DespesaModel;
use App\Models\DizimoModel;
use App\Models\MembroModel;
use App\Models\OfertaModel;
use App\Models\ReceitaModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Relatórios — resumos, financeiro, membros e agenda com exportação CSV.
 */
class Relatorios extends BaseController
{
    public function index(): string
    {
        $this->exigirPermissao('relatorios', 'visualizar');

        $tipo = (string) $this->request->getGet('tipo');
        if (! in_array($tipo, ['financeiro', 'membros', 'agenda'], true)) {
            $tipo = 'resumo';
        }

        $this->dados['titulo']      = 'Relatórios';
        $this->dados['tipo']        = $tipo;
        $this->dados['mesInicio']   = (string) $this->request->getGet('de') ?: date('Y-m');
        $this->dados['mesFim']      = (string) $this->request->getGet('ate') ?: date('Y-m');

        switch ($tipo) {
            case 'financeiro':
                $this->dados += $this->relatorioFinanceiro($this->dados['mesInicio'], $this->dados['mesFim']);
                break;
            case 'membros':
                $this->dados['membros'] = $this->relatorioMembros();
                break;
            case 'agenda':
                $this->dados['agenda'] = $this->relatorioAgenda();
                break;
            default:
                $this->dados += $this->relatorioResumo();
        }

        return view('relatorios/index', $this->dados);
    }

    /**
     * Exporta os dados em CSV de acordo com o tipo selecionado.
     */
    public function exportar(): RedirectResponse|string
    {
        $this->exigirPermissao('relatorios', 'exportar');

        $tipo = (string) $this->request->getGet('tipo');
        $de   = (string) $this->request->getGet('de');
        $ate  = (string) $this->request->getGet('ate');

        switch ($tipo) {
            case 'financeiro':
                $cabecalho = ['Tipo', 'Data', 'Descrição', 'Valor'];
                $linhas    = $this->lancamentosFinanceiros($de, $ate);
                break;
            case 'membros':
                $cabecalho = ['ID', 'Nome', 'CPF', 'Telefone', 'Congregação', 'Status'];
                $linhas    = $this->membrosCSV();
                break;
            case 'agenda':
                $cabecalho = ['Título', 'Data', 'Horário', 'Local', 'Tipo', 'Status', 'Responsável'];
                $linhas    = $this->agendaCSV();
                break;
            default:
                return redirect()->to('/relatorios')->with('erro', 'Tipo de relatório inválido.');
        }

        $nome = 'relatorio-' . $tipo . '-' . date('Ymd-His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nome . '"');

        $saida = fopen('php://output', 'w');
        fwrite($saida, "\xEF\xBB\xBF"); // BOM UTF-8
        fputcsv($saida, $cabecalho);
        foreach ($linhas as $linha) {
            fputcsv($saida, $linha);
        }
        fclose($saida);

        return '';
    }

    private function relatorioResumo(): array
    {
        $db = db_connect();

        $resumo = [
            'membros'      => $this->contarSeguro($db, 'membros'),
            'visitantes'   => $this->contarSeguro($db, 'visitantes'),
            'congregacoes' => $this->contarSeguro($db, 'congregacoes'),
            'obreiros'     => $this->contarSeguro($db, 'obreiros'),
            'ministerios'  => $this->contarSeguro($db, 'ministerios'),
            'celulas'      => $this->contarSeguro($db, 'celulas'),
            'discipulados' => $this->contarSeguro($db, 'discipulados'),
            'cursos'       => $this->contarSeguro($db, 'cursos'),
            'departamentos'=> $this->contarSeguro($db, 'departamentos'),
            'eventos'      => $this->contarSeguro($db, 'agenda'),
        ];

        $financeiro = (new \App\Models\FinanceiroModel())->resumoMes(date('Y-m'));

        return ['contagens' => $resumo, 'financeiro' => $financeiro];
    }

    private function relatorioFinanceiro(string $de, string $ate): array
    {
        $de  = $this->validarMes($de);
        $ate = $this->validarMes($ate);

        $dizimos   = (new DizimoModel())->listarPeriodo($de, $ate);
        $ofertas   = (new OfertaModel())->listarPeriodo($de, $ate);
        $receitas  = (new ReceitaModel())->listarPeriodo($de, $ate);
        $despesas  = (new DespesaModel())->listarPeriodo($de, $ate);

        $totalDizimos  = array_sum(array_column($dizimos, 'valor'));
        $totalOfertas  = array_sum(array_column($ofertas, 'valor'));
        $totalReceitas = array_sum(array_column($receitas, 'valor'));
        $totalDespesas = array_sum(array_column($despesas, 'valor'));

        return [
            'dizimos'      => $dizimos,
            'ofertas'      => $ofertas,
            'receitas'     => $receitas,
            'despesas'     => $despesas,
            'totais' => [
                'entradas' => $totalDizimos + $totalOfertas + $totalReceitas,
                'despesas' => $totalDespesas,
                'saldo'    => $totalDizimos + $totalOfertas + $totalReceitas - $totalDespesas,
            ],
            'totalDizimos'  => $totalDizimos,
            'totalOfertas'  => $totalOfertas,
            'totalReceitas' => $totalReceitas,
            'totalDespesas' => $totalDespesas,
        ];
    }

    private function relatorioMembros(): array
    {
        $model = (new MembroModel())
            ->builder('membros m')
            ->select('m.id, m.nome, m.cpf, m.telefone, m.cidade, m.status, c.nome AS congregacao')
            ->join('congregacoes c', 'c.id = m.congregacao_id', 'left')
            ->where('m.deleted_at', null)
            ->orderBy('m.status')
            ->orderBy('m.nome')
            ->get()->getResultArray();

        return $model;
    }

    private function relatorioAgenda(): array
    {
        $eventos = (new AgendaModel())->builder('agenda a')
            ->select('a.*, c.nome AS congregacao')
            ->join('congregacoes c', 'c.id = a.congregacao_id', 'left')
            ->where('a.deleted_at', null)
            ->orderBy('a.data_inicio', 'DESC')
            ->get()->getResultArray();

        $presencas = new AgendaPresencaModel();
        foreach ($eventos as &$ev) {
            $ev['presencas'] = $presencas->contarPorEvento((int) $ev['id']);
        }
        unset($ev);

        return $eventos;
    }

    private function lancamentosFinanceiros(string $de, string $ate): array
    {
        $de  = $this->validarMes($de);
        $ate = $this->validarMes($ate);

        // Agrega todos os lançamentos em um único array para exportação
        $resultado = [];
        foreach ((new DizimoModel())->listarPeriodo($de, $ate) as $l) {
            $resultado[] = ['Dízimo', formatar_data($l['data']), $l['membro'], $l['valor']];
        }
        foreach ((new OfertaModel())->listarPeriodo($de, $ate) as $l) {
            $resultado[] = ['Oferta', formatar_data($l['data']), 'Oferta: ' . ($l['categoria'] ?? 'Geral'), $l['valor']];
        }
        foreach ((new ReceitaModel())->listarPeriodo($de, $ate) as $l) {
            $resultado[] = ['Receita', formatar_data($l['data']), 'Receita: ' . $l['descricao'], $l['valor']];
        }
        foreach ((new DespesaModel())->listarPeriodo($de, $ate) as $l) {
            $resultado[] = ['Despesa', formatar_data($l['data']), 'Despesa: ' . $l['descricao'], $l['valor']];
        }

        return $resultado;
    }

    private function membrosCSV(): array
    {
        $linhas = [];
        foreach ($this->relatorioMembros() as $m) {
            $linhas[] = [$m['id'], $m['nome'], $m['cpf'] ?: '', $m['telefone'] ?: '', $m['congregacao'] ?: '', $m['status']];
        }

        return $linhas;
    }

    private function agendaCSV(): array
    {
        $linhas = [];
        foreach ($this->relatorioAgenda() as $ev) {
            $linhas[] = [
                $ev['titulo'],
                formatar_data($ev['data_inicio']),
                $ev['hora_inicio'] ? substr($ev['hora_inicio'], 0, 5) : '',
                $ev['local'] ?: '',
                $ev['tipo'],
                $ev['status'],
                $ev['responsavel'] ?: '',
            ];
        }

        return $linhas;
    }

    private function validarMes(string $mes): string
    {
        return preg_match('/^\d{4}-\d{2}$/', $mes) ? $mes : date('Y-m');
    }

    private function contarSeguro($db, string $tabela): int
    {
        if (! $db->tableExists($tabela)) {
            return 0;
        }

        return $db->table($tabela)->where('deleted_at', null)->countAllResults();
    }
}
