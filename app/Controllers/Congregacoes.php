<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CongregacaoModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de congregações.
 */
class Congregacoes extends BaseController
{
    private CongregacaoModel $congregacoes;

    public function __construct()
    {
        $this->congregacoes = new CongregacaoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('congregacoes', 'visualizar');

        $busca = $this->request->getGet('q');
        $this->dados['titulo']      = 'Congregações';
        $this->dados['congregacoes'] = $this->congregacoes->listarComContagem($busca);
        $this->dados['busca']       = $busca;

        return view('congregacoes/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('congregacoes', 'cadastrar');
        $this->dados['titulo'] = 'Nova Congregação';

        return view('congregacoes/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('congregacoes', 'cadastrar');

        if (! $this->validate($this->congregacoes->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $dados['igreja_id'] = (int) (db_connect()->table('igrejas')->select('id')->get()->getFirstRow('array')['id'] ?? 1);

        $this->congregacoes->insert($dados);
        $id = (int) $this->congregacoes->getInsertID();
        (new Auditoria())->log('criar', 'congregacoes', $id, null, ['nome' => $dados['nome'], 'codigo' => $dados['codigo']]);

        return redirect()->to('/congregacoes')->with('sucesso', 'Congregação cadastrada com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('congregacoes', 'editar');

        $this->dados['titulo']   = 'Editar Congregação';
        $this->dados['registro'] = $this->congregacoes->find($id);

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('congregacoes/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('congregacoes', 'editar');

        $antes = $this->congregacoes->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->congregacoes->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->congregacoes->update($id, $dados);
        (new Auditoria())->log('editar', 'congregacoes', $id, ['nome' => $antes['nome']], $dados);

        return redirect()->to('/congregacoes')->with('sucesso', 'Congregação atualizada com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('congregacoes', 'excluir');

        $membrosVinculados = db_connect()->table('membros')
            ->where('congregacao_id', $id)->where('deleted_at', null)
            ->countAllResults();

        if ($membrosVinculados > 0) {
            return redirect()->back()->with('erro', "Existem {$membrosVinculados} membro(s) vinculado(s) a esta congregação. Transfira-os antes de excluir.");
        }

        $antes = $this->congregacoes->find($id);
        if ($antes !== null) {
            $this->congregacoes->delete($id);
            (new Auditoria())->log('excluir', 'congregacoes', $id, ['nome' => $antes['nome']]);
        }

        return redirect()->to('/congregacoes')->with('sucesso', 'Congregação excluída com sucesso!');
    }

    private function dadosPost(): array
    {
        return [
            'nome'            => trim((string) $this->request->getPost('nome')),
            'codigo'          => trim((string) $this->request->getPost('codigo')),
            'telefone'        => trim((string) $this->request->getPost('telefone')) ?: null,
            'email'           => trim((string) $this->request->getPost('email')) ?: null,
            'cep'             => trim((string) $this->request->getPost('cep')) ?: null,
            'logradouro'      => trim((string) $this->request->getPost('logradouro')) ?: null,
            'numero'          => trim((string) $this->request->getPost('numero')) ?: null,
            'bairro'          => trim((string) $this->request->getPost('bairro')) ?: null,
            'cidade'          => trim((string) $this->request->getPost('cidade')) ?: null,
            'estado'          => strtoupper(trim((string) $this->request->getPost('estado'))) ?: null,
            'pastor_responsavel_id' => $this->request->getPost('pastor_responsavel_id') ?: null,
            'lider_id'        => $this->request->getPost('lider_id') ?: null,
            'data_abertura'   => $this->request->getPost('data_abertura') ?: null,
            'ativo'           => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }
}
