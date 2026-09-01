<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CargoModel;
use CodeIgniter\HTTP\RedirectResponse;

class Cargos extends BaseController
{
    private CargoModel $cargos;

    public function __construct()
    {
        $this->cargos = new CargoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('cargos', 'visualizar');

        $busca = $this->request->getGet('q');
        $pagina = (int) ($this->request->getGet('page') ?? 1);
        $porPagina = 20;
        $offset = ($pagina - 1) * $porPagina;

        $this->dados['titulo'] = 'Cargos de Obreiros';
        $this->dados['cargos'] = $this->cargos->listar($busca, $porPagina, $offset);
        $this->dados['total'] = $this->cargos->contar($busca);
        $this->dados['porPagina'] = $porPagina;
        $this->dados['pagina'] = $pagina;
        $this->dados['busca'] = $busca;

        return view('cargos/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('cargos', 'cadastrar');

        $this->dados['titulo'] = 'Novo Cargo';
        $this->dados['acao'] = site_url('cargos/salvar');
        $this->dados['cargo'] = [
            'nome' => '',
            'descricao' => '',
            'ordem' => 0,
            'ativo' => 1,
        ];

        return view('cargos/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('cargos', 'cadastrar');

        $dados = $this->request->getPost(['nome', 'descricao', 'ordem', 'ativo']);

        if (! $this->cargos->save($dados)) {
            return redirect()->back()
                ->with('erro', implode('<br>', $this->cargos->errors()))
                ->withInput();
        }

        return redirect()->to('/cargos')->with('sucesso', 'Cargo cadastrado com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $this->exigirPermissao('cargos', 'editar');

        $cargo = $this->cargos->find($id);

        if ($cargo === null) {
            return redirect()->to('/cargos')->with('erro', 'Cargo não encontrado.');
        }

        $this->dados['titulo'] = 'Editar Cargo';
        $this->dados['acao'] = site_url('cargos/atualizar/' . $id);
        $this->dados['cargo'] = $cargo;

        return view('cargos/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('cargos', 'editar');

        $cargo = $this->cargos->find($id);

        if ($cargo === null) {
            return redirect()->to('/cargos')->with('erro', 'Cargo não encontrado.');
        }

        $dados = $this->request->getPost(['nome', 'descricao', 'ordem', 'ativo']);

        if (! $this->cargos->update($id, $dados)) {
            return redirect()->back()
                ->with('erro', implode('<br>', $this->cargos->errors()))
                ->withInput();
        }

        return redirect()->to('/cargos')->with('sucesso', 'Cargo atualizado com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('cargos', 'excluir');

        $cargo = $this->cargos->find($id);

        if ($cargo === null) {
            return redirect()->to('/cargos')->with('erro', 'Cargo não encontrado.');
        }

        // Verifica se há obreiros vinculados
        $obreiros = model('App\Models\ObreiroModel')
            ->where('cargo', $cargo['nome'])
            ->where('deleted_at', null)
            ->countAllResults();

        if ($obreiros > 0) {
            return redirect()->to('/cargos')->with('erro', "Não é possível excluir: existem {$obreiros} obreiro(s) vinculado(s) a este cargo.");
        }

        if (! $this->cargos->delete($id)) {
            return redirect()->to('/cargos')->with('erro', 'Erro ao excluir o cargo.');
        }

        return redirect()->to('/cargos')->with('sucesso', 'Cargo excluído com sucesso.');
    }
}