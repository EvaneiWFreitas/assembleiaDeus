<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\DiscipuladoModel;
use App\Models\MembroModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Discipulado: pares discípulo/discipulador + registro de encontros.
 */
class Discipulados extends BaseController
{
    private DiscipuladoModel $discipulados;
    private MembroModel $membros;

    public function __construct()
    {
        $this->discipulados = new DiscipuladoModel();
        $this->membros      = new MembroModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('discipulados', 'visualizar');

        $this->dados['titulo']       = 'Discipulado';
        $this->dados['discipulados'] = $this->discipulados->listar();

        return view('discipulado/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('discipulados', 'cadastrar');

        $this->dados['titulo']   = 'Novo Discipulado';
        $this->dados['registro'] = null;
        $this->dados['membros']  = $this->membros->getDropdown();

        return view('discipulado/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('discipulados', 'editar');

        $this->dados['titulo']    = 'Editar Discipulado';
        $this->dados['registro']  = $this->discipulados->find($id);
        $this->dados['membros']   = $this->membros->getDropdown();
        $this->dados['encontros'] = $this->discipulados->getEncontros($id);

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('discipulado/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('discipulados', 'cadastrar');

        if (! $this->validate($this->discipulados->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->discipulados->insert($dados);
        $id = (int) $this->discipulados->getInsertID();

        (new Auditoria())->log('criar', 'discipulados', $id, null, ['discipulo_id' => $dados['discipulo_id']]);

        return redirect()->to('/discipulados')->with('sucesso', 'Discipulado iniciado com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('discipulados', 'editar');

        $antes = $this->discipulados->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->discipulados->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->discipulados->update($id, $dados);
        (new Auditoria())->log('editar', 'discipulados', $id, ['status' => $antes['status']], $dados);

        return redirect()->back()->with('sucesso', 'Discipulado atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('discipulados', 'excluir');

        $antes = $this->discipulados->find($id);
        if ($antes !== null) {
            $this->discipulados->delete($id);
            (new Auditoria())->log('excluir', 'discipulados', $id, ['id' => $id]);
        }

        return redirect()->to('/discipulados')->with('sucesso', 'Discipulado excluído com sucesso!');
    }

    /**
     * Registra encontro com presença/ausência e progresso.
     */
    public function registrarEncontro(int $id): RedirectResponse
    {
        $this->exigirPermissao('discipulados', 'editar');

        if (! $this->validate([
            'data'   => 'required|valid_date',
            'etapa'  => 'permit_empty|max_length[100]',
        ])) {
            return redirect()->back()->with('erros', $this->validator->getErrors());
        }

        $this->discipulados->registrarEncontro([
            'discipulado_id' => $id,
            'data'           => $this->request->getPost('data'),
            'etapa'          => trim((string) $this->request->getPost('etapa')) ?: null,
            'presente'       => $this->request->getPost('presente') ? 1 : 0,
            'observacoes'    => trim((string) $this->request->getPost('observacoes')) ?: null,
        ]);

        (new Auditoria())->log('criar', 'discipulado_encontros', $id);

        return redirect()->back()->with('sucesso', 'Encontro registrado com sucesso!');
    }

    private function dadosPost(): array
    {
        return [
            'discipulo_id'    => (int) $this->request->getPost('discipulo_id'),
            'discipulador_id' => (int) $this->request->getPost('discipulador_id'),
            'data_inicio'     => $this->request->getPost('data_inicio') ?: date('Y-m-d'),
            'data_conclusao'  => $this->request->getPost('data_conclusao') ?: null,
            'status'          => $this->request->getPost('status') ?: 'Em andamento',
            'observacoes'     => trim((string) $this->request->getPost('observacoes')) ?: null,
        ];
    }
}
