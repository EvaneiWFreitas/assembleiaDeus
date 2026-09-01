<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CelulaModel;
use App\Models\CongregacaoModel;
use App\Models\MembroModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de células / pequenos grupos + participantes.
 */
class Celulas extends BaseController
{
    private CelulaModel $celulas;
    private MembroModel $membros;
    private CongregacaoModel $congregacoes;

    public function __construct()
    {
        $this->celulas      = new CelulaModel();
        $this->membros      = new MembroModel();
        $this->congregacoes = new CongregacaoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('celulas', 'visualizar');

        $busca = $this->request->getGet('q');
        $this->dados['titulo']  = 'Células';
        $this->dados['celulas'] = $this->celulas->listarComLideres($busca);
        $this->dados['busca']   = $busca;

        return view('celulas/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('celulas', 'cadastrar');
        $this->carregarDropdowns();

        $this->dados['titulo']   = 'Nova Célula';
        $this->dados['registro'] = null;
        $this->dados['dias']     = CelulaModel::DIAS;

        return view('celulas/form', $this->dados);
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('celulas', 'editar');
        $this->carregarDropdowns();

        $this->dados['titulo']        = 'Editar Célula';
        $this->dados['registro']      = $this->celulas->find($id);
        $this->dados['dias']          = CelulaModel::DIAS;
        $this->dados['participantes'] = $this->celulas->getParticipantes($id);

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('celulas/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('celulas', 'cadastrar');

        if (! $this->validate($this->celulas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->celulas->insert($dados);
        $id = (int) $this->celulas->getInsertID();

        (new Auditoria())->log('criar', 'celulas', $id, null, ['nome' => $dados['nome']]);

        return redirect()->to('/celulas')->with('sucesso', 'Célula cadastrada com sucesso!');
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('celulas', 'editar');

        $antes = $this->celulas->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->celulas->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->celulas->update($id, $dados);
        $this->celulas->sincronizarParticipantes($id, array_map('intval', (array) ($this->request->getPost('participantes') ?? [])));

        (new Auditoria())->log('editar', 'celulas', $id, ['nome' => $antes['nome']], $dados);

        return redirect()->to('/celulas')->with('sucesso', 'Célula atualizada com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('celulas', 'excluir');

        $antes = $this->celulas->find($id);
        if ($antes !== null) {
            $this->celulas->delete($id);
            (new Auditoria())->log('excluir', 'celulas', $id, ['nome' => $antes['nome']]);
        }

        return redirect()->to('/celulas')->with('sucesso', 'Célula excluída com sucesso!');
    }

    public function membros(int $id): string
    {
        $this->exigirPermissao('celulas', 'visualizar');

        $celula = $this->celulas->find($id);
        if ($celula === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->dados['titulo']  = 'Célula: ' . $celula['nome'];
        $this->dados['celula']  = $celula;
        $this->dados['lider']   = $celula['lider_id'] ? $this->membros->find($celula['lider_id']) : null;
        $this->dados['membros'] = $this->celulas->getParticipantes($id);
        $this->dados['membrosDisponiveis'] = $this->membros->where('status', 'Ativo')->orderBy('nome')->findAll();

        return view('celulas/membros', $this->dados);
    }

    public function vincularMembro(int $id): RedirectResponse
    {
        $this->exigirPermissao('celulas', 'editar');

        $membroId = (int) $this->request->getPost('membro_id');
        if ($membroId <= 0) {
            return redirect()->back()->with('erro', 'Selecione um membro para vincular.');
        }

        $this->celulas->adicionarParticipante($id, $membroId);
        (new Auditoria())->log('criar', 'celula_membros', $id, null, ['membro_id' => $membroId]);

        return redirect()->back()->with('sucesso', 'Membro vinculado à célula com sucesso!');
    }

    public function removerMembro(int $id, int $membroId): RedirectResponse
    {
        $this->exigirPermissao('celulas', 'editar');

        $this->celulas->removerParticipante($id, $membroId);
        (new Auditoria())->log('excluir', 'celula_membros', $id, null, ['membro_id' => $membroId]);

        return redirect()->back()->with('sucesso', 'Membro removido da célula!');
    }

    private function carregarDropdowns(): void
    {
        $this->dados['membros']      = $this->membros->getDropdown();
        $this->dados['congregacoes'] = $this->congregacoes->getDropdown();
    }

    private function dadosPost(): array
    {
        return [
            'congregacao_id' => $this->request->getPost('congregacao_id') ?: null,
            'nome'           => trim((string) $this->request->getPost('nome')),
            'codigo'         => trim((string) $this->request->getPost('codigo')),
            'lider_id'       => $this->request->getPost('lider_id') ?: null,
            'vice_lider_id'  => $this->request->getPost('vice_lider_id') ?: null,
            'endereco'       => trim((string) $this->request->getPost('endereco')) ?: null,
            'dia_semana'     => $this->request->getPost('dia_semana') ?: null,
            'horario'        => $this->request->getPost('horario') ?: null,
            'ativo'          => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }
}
