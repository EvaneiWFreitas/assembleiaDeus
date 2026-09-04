<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\DocumentoModeloModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Modelos de Documentos (Certificados, Papel Timbrado, Atas, Convites, etc.)
 */
class Documentos extends BaseController
{
    private DocumentoModeloModel $modelos;

    public function __construct()
    {
        $this->modelos = new DocumentoModeloModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('documentos', 'visualizar');

        $this->dados['titulo']   = 'Modelos de Documentos';
        $this->dados['modelos']  = $this->modelos->listar();
        $this->dados['tipos']    = DocumentoModeloModel::TIPOS;

        return view('documentos/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('documentos', 'cadastrar');

        $this->dados['titulo']  = 'Novo Modelo de Documento';
        $this->dados['registro'] = null;
        $this->dados['tipos']   = DocumentoModeloModel::TIPOS;

        return view('documentos/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('documentos', 'editar');

        $registro = $this->modelos->find($id);
        if ($registro === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->dados['titulo']  = 'Editar Modelo de Documento';
        $this->dados['registro'] = $registro;
        $this->dados['tipos']   = DocumentoModeloModel::TIPOS;

        return view('documentos/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('documentos', 'cadastrar');

        if (! $this->validate($this->modelos->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();

        if ((int) $dados['padrao'] === 1) {
            $this->modelos->where('tipo', $dados['tipo'])->set('padrao', 0)->update();
        }

        $this->modelos->insert($dados);
        $id = (int) $this->modelos->getInsertID();

        (new Auditoria())->log('criar', 'documentos_modelos', $id, null, ['titulo' => $dados['titulo']]);

        return redirect()->to('/documentos')->with('sucesso', 'Modelo de documento cadastrado com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('documentos', 'editar');

        $antes = $this->modelos->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->modelos->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();

        if ((int) $dados['padrao'] === 1) {
            $this->modelos->where('tipo', $dados['tipo'])->where('id !=', $id)->set('padrao', 0)->update();
        }

        $this->modelos->update($id, $dados);
        (new Auditoria())->log('editar', 'documentos_modelos', $id, ['titulo' => $antes['titulo']], $dados);

        return redirect()->back()->with('sucesso', 'Modelo de documento atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('documentos', 'excluir');

        $antes = $this->modelos->find($id);
        if ($antes !== null) {
            $this->modelos->delete($id);
            (new Auditoria())->log('excluir', 'documentos_modelos', $id, ['titulo' => $antes['titulo']]);
        }

        return redirect()->to('/documentos')->with('sucesso', 'Modelo de documento excluído com sucesso!');
    }

    public function visualizar(int $id): string
    {
        $this->exigirPermissao('documentos', 'visualizar');

        $registro = $this->modelos->find($id);
        if ($registro === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $variaveis = json_decode($registro['variaveis'] ?? '[]', true);
        $this->dados['registro'] = $registro;
        $this->dados['variaveis'] = $variaveis;
        $this->dados['titulo'] = 'Visualizar: ' . $registro['titulo'];

        return view('documentos/visualizar', $this->dados);
    }

    public function imprimir(int $id): string
    {
        $this->exigirPermissao('documentos', 'visualizar');

        $registro = $this->modelos->find($id);
        if ($registro === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $variaveis = json_decode($registro['variaveis'] ?? '[]', true);
        $this->dados['registro'] = $registro;
        $this->dados['variaveis'] = $variaveis;
        $this->dados['titulo'] = $registro['titulo'];

        return view('documentos/imprimir', $this->dados);
    }

    private function dadosPost(): array
    {
        return [
            'titulo'    => trim((string) $this->request->getPost('titulo')),
            'tipo'      => trim((string) $this->request->getPost('tipo')),
            'conteudo'  => $this->request->getPost('conteudo'),
            'variaveis' => $this->request->getPost('variaveis') ?? '[]',
            'ativo'     => $this->request->getPost('ativo') ? 1 : 0,
            'padrao'    => $this->request->getPost('padrao') ? 1 : 0,
        ];
    }
}