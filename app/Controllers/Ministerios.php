<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\DepartamentoModel;
use App\Models\MembroModel;
use App\Models\MinisterioModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de ministérios + participantes.
 */
class Ministerios extends BaseController
{
    private MinisterioModel $ministerios;
    private MembroModel $membros;
    private DepartamentoModel $departamentos;

    public function __construct()
    {
        $this->ministerios   = new MinisterioModel();
        $this->membros       = new MembroModel();
        $this->departamentos = new DepartamentoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('ministerios', 'visualizar');

        $this->dados['titulo']      = 'Ministérios';
        $this->dados['ministerios'] = $this->ministerios->listarComLideres();

        return view('ministerios/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('ministerios', 'cadastrar');
        $this->carregarDropdowns();

        $this->dados['titulo']   = 'Novo Ministério';
        $this->dados['registro'] = null;

        return view('ministerios/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('ministerios', 'editar');
        $this->carregarDropdowns();

        $this->dados['titulo']       = 'Editar Ministério';
        $this->dados['registro']     = $this->ministerios->find($id);
        $this->dados['participantes'] = $this->ministerios->getParticipantes($id);

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('ministerios/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('ministerios', 'cadastrar');

        if (! $this->validate($this->ministerios->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->ministerios->insert($dados);
        $id = (int) $this->ministerios->getInsertID();

        (new Auditoria())->log('criar', 'ministerios', $id, null, ['nome' => $dados['nome']]);

        return redirect()->to('/ministerios')->with('sucesso', 'Ministério cadastrado com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('ministerios', 'editar');

        $antes = $this->ministerios->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->ministerios->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->ministerios->update($id, $dados);

        // Sincroniza participantes (regra: líder do ministério sempre fica no grupo)
        $participantes = array_map('intval', (array) ($this->request->getPost('participantes') ?? []));
        if (! in_array((int) $dados['lider_id'], $participantes, true) && $dados['lider_id']) {
            $participantes[] = (int) $dados['lider_id'];
        }
        $this->ministerios->sincronizarParticipantes($id, $participantes);

        (new Auditoria())->log('editar', 'ministerios', $id, ['nome' => $antes['nome']], $dados);

        return redirect()->to('/ministerios')->with('sucesso', 'Ministério atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('ministerios', 'excluir');

        $antes = $this->ministerios->find($id);
        if ($antes !== null) {
            $this->ministerios->delete($id);
            (new Auditoria())->log('excluir', 'ministerios', $id, ['nome' => $antes['nome']]);
        }

        return redirect()->to('/ministerios')->with('sucesso', 'Ministério excluído com sucesso!');
    }

    private function carregarDropdowns(): void
    {
        $this->dados['membros']      = $this->membros->getDropdown();
        $this->dados['departamentos'] = $this->departamentos->orderBy('nome')->findAll();
    }

    private function dadosPost(): array
    {
        return [
            'nome'            => trim((string) $this->request->getPost('nome')),
            'descricao'       => trim((string) $this->request->getPost('descricao')) ?: null,
            'lider_id'        => $this->request->getPost('lider_id') ?: null,
            'departamento_id' => $this->request->getPost('departamento_id') ?: null,
            'ativo'           => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }
}
