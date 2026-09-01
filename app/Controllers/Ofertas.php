<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CategoriaModel;
use App\Models\OfertaModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de ofertas.
 */
class Ofertas extends BaseController
{
    private OfertaModel $ofertas;
    private CategoriaModel $categorias;

    public function __construct()
    {
        $this->ofertas    = new OfertaModel();
        $this->categorias = new CategoriaModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('financeiro', 'visualizar');

        $this->dados['titulo']  = 'Ofertas';
        $this->dados['ofertas'] = $this->ofertas->listar($this->request->getGet('mes'));
        $this->dados['formas']  = OfertaModel::FORMAS_PAGAMENTO;

        return view('ofertas/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $this->dados['titulo']     = 'Nova Oferta';
        $this->dados['registro']   = null;
        $this->dados['categorias'] = $this->categorias->getDropdown('entrada');
        $this->dados['formas']     = OfertaModel::FORMAS_PAGAMENTO;

        return view('ofertas/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('financeiro', 'editar');

        $this->dados['titulo']     = 'Editar Oferta';
        $this->dados['registro']   = $this->ofertas->find($id);
        $this->dados['categorias'] = $this->categorias->getDropdown('entrada');
        $this->dados['formas']     = OfertaModel::FORMAS_PAGAMENTO;

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('ofertas/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'cadastrar');

        $dados = $this->dadosPost();

        if (! $this->validate($this->ofertas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $this->ofertas->insert($dados);
        $id = (int) $this->ofertas->getInsertID();

        (new Auditoria())->log('criar', 'ofertas', $id, null, $dados);

        return redirect()->to('/ofertas')->with('sucesso', 'Oferta registrada com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'editar');

        $antes = $this->ofertas->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $dados = $this->dadosPost();

        if (! $this->validate($this->ofertas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $this->ofertas->update($id, $dados);

        (new Auditoria())->log('editar', 'ofertas', $id, $antes, $dados);

        return redirect()->to('/ofertas')->with('sucesso', 'Oferta atualizada com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('financeiro', 'excluir');

        $antes = $this->ofertas->find($id);
        if ($antes !== null) {
            $this->ofertas->delete($id);
            (new Auditoria())->log('excluir', 'ofertas', $id);
        }

        return redirect()->to('/ofertas')->with('sucesso', 'Oferta excluída com sucesso!');
    }

    private function dadosPost(): array
    {
        return [
            'categoria_id'    => $this->request->getPost('categoria_id') ?: null,
            'valor'           => number_format(limpar_decimal($this->request->getPost('valor')), 2, '.', ''),
            'data'            => $this->request->getPost('data'),
            'forma_pagamento' => $this->request->getPost('forma_pagamento') ?: null,
            'observacoes'     => trim((string) $this->request->getPost('observacoes')) ?: null,
        ];
    }
}