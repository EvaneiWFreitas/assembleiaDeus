<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CongregacaoModel;
use App\Models\IgrejaModel;
use App\Models\MembroModel;
use App\Models\ObreiroModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Carteirinhas de identificação de membros, obreiros e pastores.
 * Gera cartões 10x7cm em diversos modelos, para impressão individual ou em lote.
 */
class Carteirinhas extends BaseController
{
    private const MODELOS = [
        'classica' => ['nome' => 'Clássica', 'cor' => '#0d6efd'],
        'moderna'  => ['nome' => 'Moderna',  'cor' => '#198754'],
        'elegante' => ['nome' => 'Elegante', 'cor' => '#b8860b'],
        'digital'  => ['nome' => 'Digital',  'cor' => '#6f42c1'],
    ];

    private MembroModel $membros;
    private ObreiroModel $obreiros;
    private CongregacaoModel $congregacoes;

    public function __construct()
    {
        $this->membros      = new MembroModel();
        $this->obreiros     = new ObreiroModel();
        $this->congregacoes = new CongregacaoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('carteirinhas', 'visualizar');

        $this->dados['titulo']    = 'Carteirinhas';
        $this->dados['modelos']   = self::MODELOS;
        $this->dados['modelo']    = (string) ($this->request->getGet('modelo') ?: 'classica');
        $this->dados['tipo']      = (string) ($this->request->getGet('tipo') ?: 'membro');
        $this->dados['pessoa_id'] = (int) ($this->request->getGet('pessoa_id') ?: 0);
        $this->dados['membros']   = $this->membros->listar(null, null, null, 5000, 0);
        $this->dados['obreiros']  = $this->obreiros->listar(null, null, 5000, 0);

        return view('carteirinhas/index', $this->dados);
    }

    public function imprimir(): string | RedirectResponse
    {
        $this->exigirPermissao('carteirinhas', 'imprimir');

        $modelo  = (string) ($this->request->getGet('modelo') ?: 'classica');
        $tipo    = (string) ($this->request->getGet('tipo') ?: 'membro');
        $pessoaId = (int) $this->request->getGet('pessoa_id');

        $pessoa = $this->carregarPessoa($tipo, $pessoaId);
        if ($pessoa === null) {
            return redirect()->to('/carteirinhas')->with('erro', 'Pessoa não encontrada para gerar a carteirinha.');
        }

        return view('carteirinhas/imprimir', $this->baseDados([
            'multi'   => false,
            'cartoes' => [$pessoa],
        ]));
    }

    public function imprimirTodas(): string | RedirectResponse
    {
        $this->exigirPermissao('carteirinhas', 'imprimir');

        $tipo = (string) ($this->request->getGet('tipo') ?: 'membro');

        if ($tipo === 'obreiro') {
            $cartoes = array_values(array_filter(array_map(
                fn (array $row) => $this->montarPessoa($row, 'obreiro'),
                $this->obreiros->listar(null, null, 5000, 0)
            )));
        } else {
            $cartoes = array_values(array_filter(array_map(
                fn (array $row) => $this->montarPessoa($row, 'membro'),
                $this->membros->listar(null, null, null, 5000, 0)
            )));
        }

        if ($cartoes === []) {
            return redirect()->to('/carteirinhas')->with('erro', 'Não há registros para gerar as carteirinhas.');
        }

        return view('carteirinhas/imprimir', $this->baseDados([
            'multi'   => true,
            'cartoes' => $cartoes,
        ]));
    }

    private function baseDados(array $extra): array
    {
        $modelo = (string) ($this->request->getGet('modelo') ?: 'classica');
        if (! isset(self::MODELOS[$modelo])) {
            $modelo = 'classica';
        }

        return array_merge([
            'modelo'   => $modelo,
            'tipo'     => (string) ($this->request->getGet('tipo') ?: 'membro'),
            'igreja'   => (new IgrejaModel())->getIgreja(),
            'emitidoEm' => date('d/m/Y'),
        ], $extra);
    }

    private function carregarPessoa(string $tipo, int $id): ?array
    {
        if ($tipo === 'obreiro' || $tipo === 'pastor') {
            $obreiro = $this->obreiros->buscarCompleto($id);

            return $obreiro !== null ? $this->montarPessoa($obreiro, 'obreiro') : null;
        }

        $membro = $this->membros->find($id);
        if ($membro === null) {
            return null;
        }

        if (! empty($membro['congregacao_id'])) {
            $cong = $this->congregacoes->find((int) $membro['congregacao_id']);
            $membro['congregacao'] = $cong['nome'] ?? '';
        }

        return $this->montarPessoa($membro, 'membro');
    }

    private function montarPessoa(array $d, string $tipo): ?array
    {
        if (empty($d['nome'])) {
            return null;
        }

        if ($tipo === 'obreiro') {
            $fotoDir = ! empty($d['foto']) ? 'obreiros' : 'membros';
            $fotoArq = ! empty($d['foto']) ? $d['foto'] : ($d['foto_membro'] ?? null);
            $rotulo  = $d['cargo'] ?? 'Obreiro';
        } else {
            $fotoDir = 'membros';
            $fotoArq = $d['foto'] ?? null;
            $rotulo  = 'Membro';
        }

        $p = [
            'id'              => (int) ($d['id'] ?? 0),
            'nome'            => (string) ($d['nome'] ?? ''),
            'rotulo'          => (string) $rotulo,
            'foto'            => is_string($fotoArq) && $fotoArq !== '' ? $fotoArq : '',
            'foto_dir'        => $fotoDir,
            'foto_inicial'    => mb_strtoupper(mb_substr((string) ($d['nome'] ?? ''), 0, 1)),
            'cpf'             => formatar_cpf((string) ($d['cpf'] ?? '')),
            'rg'              => (string) ($d['rg'] ?? ''),
            'nascimento'      => formatar_data((string) ($d['data_nascimento'] ?? '')),
            'tipo_membro'     => (string) ($d['tipo_membro'] ?? ''),
            'numero_registro' => (string) ($d['numero_registro'] ?? ''),
            'congregacao'     => (string) ($d['congregacao'] ?? ''),
            'telefone'        => (string) ($d['telefone'] ?? ''),
            'validade'        => date('d/m/Y', strtotime('last day of december this year')),
        ];

        return $p;
    }
}