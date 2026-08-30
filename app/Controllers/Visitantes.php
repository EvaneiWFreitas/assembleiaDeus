<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CongregacaoModel;
use App\Models\MembroModel;
use App\Models\VisitanteModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD de visitantes + fluxo de acompanhamento.
 */
class Visitantes extends BaseController
{
    private VisitanteModel $visitantes;
    private CongregacaoModel $congregacoes;
    private MembroModel $membros;

    public function __construct()
    {
        $this->visitantes   = new VisitanteModel();
        $this->congregacoes = new CongregacaoModel();
        $this->membros      = new MembroModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('visitantes', 'visualizar');

        $busca   = $this->request->getGet('q');
        $status  = $this->request->getGet('status');
        $pagina  = max(1, (int) ($this->request->getGet('page') ?? 1));
        $porPagina = 20;

        $this->dados['titulo']    = 'Visitantes';
        $this->dados['visitantes'] = $this->visitantes->listar($busca, $status, $porPagina, ($pagina - 1) * $porPagina);
        $this->dados['total']     = $this->visitantes->contar($busca, $status);
        $this->dados['pagina']    = $pagina;
        $this->dados['porPagina'] = $porPagina;
        $this->dados['busca']     = $busca;
        $this->dados['statusFiltro'] = $status;
        $this->dados['statusFunil']  = VisitanteModel::STATUS_FUNIL;

        return view('visitantes/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('visitantes', 'cadastrar');
        $this->carregarDropdowns();

        $this->dados['titulo']   = 'Novo Visitante';
        $this->dados['registro'] = null;

        return view('visitantes/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('visitantes', 'cadastrar');

        if (! $this->validate($this->visitantes->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->visitantes->insert($dados);
        $id = (int) $this->visitantes->getInsertID();

        (new Auditoria())->log('criar', 'visitantes', $id, null, ['nome' => $dados['nome']]);
        $this->avancarParaMembroSeAplicavel($id, $dados);

        return redirect()->to('/visitantes')->with('sucesso', 'Visitante cadastrado com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('visitantes', 'editar');
        $this->carregarDropdowns();

        $this->dados['titulo']   = 'Editar Visitante';
        $this->dados['registro'] = $this->visitantes->find($id);

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('visitantes/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('visitantes', 'editar');

        $antes = $this->visitantes->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->visitantes->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();
        $this->visitantes->update($id, $dados);
        (new Auditoria())->log('editar', 'visitantes', $id, ['status' => $antes['status']], ['status' => $dados['status']]);
        $this->avancarParaMembroSeAplicavel($id, $dados, $antes);

        return redirect()->to('/visitantes')->with('sucesso', 'Visitante atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('visitantes', 'excluir');

        $antes = $this->visitantes->find($id);
        if ($antes !== null) {
            $this->visitantes->delete($id);
            (new Auditoria())->log('excluir', 'visitantes', $id, ['nome' => $antes['nome']]);
        }

        return redirect()->to('/visitantes')->with('sucesso', 'Visitante excluído com sucesso!');
    }

    /**
     * Regra de negócio: visitante que chega ao status "Membro" cria o cadastro de membro.
     */
    private function avancarParaMembroSeAplicavel(int $id, array $dados, ?array $antes = null): void
    {
        if ($dados['status'] !== 'Membro' || ($antes !== null && $antes['status'] === 'Membro')) {
            return;
        }

        $visitante = $this->visitantes->find($id);
        $jaExiste  = $this->membros->where('nome', $visitante['nome'])->where('deleted_at', null)->countAllResults();

        if ($jaExiste === 0) {
            $this->membros->insert([
                'congregacao_id'  => $visitante['congregacao_id'],
                'nome'            => $visitante['nome'],
                'telefone'        => $visitante['telefone'],
                'whatsapp'        => $visitante['whatsapp'],
                'email'           => $visitante['email'],
                'logradouro'      => $visitante['endereco'],
                'data_recebimento'=> date('Y-m-d'),
                'tipo_membro'     => 'Novo convertido',
                'status'          => 'Ativo',
            ]);
            (new Auditoria())->log('criar', 'membros', (int) $this->membros->getInsertID(), null, ['origem' => 'visitante #' . $id]);
        }
    }

    private function carregarDropdowns(): void
    {
        $this->dados['congregacoes'] = $this->congregacoes->getDropdown();
        $this->dados['membros']      = $this->membros->getDropdown();
        $this->dados['statusFunil']  = VisitanteModel::STATUS_FUNIL;
    }

    private function dadosPost(): array
    {
        return [
            'congregacao_id'       => $this->request->getPost('congregacao_id') ?: null,
            'nome'                 => trim((string) $this->request->getPost('nome')),
            'telefone'             => trim((string) $this->request->getPost('telefone')) ?: null,
            'whatsapp'             => trim((string) $this->request->getPost('whatsapp')) ?: null,
            'email'                => trim((string) $this->request->getPost('email')) ?: null,
            'endereco'             => trim((string) $this->request->getPost('endereco')) ?: null,
            'data_primeira_visita' => $this->request->getPost('data_primeira_visita') ?: null,
            'como_conheceu'        => trim((string) $this->request->getPost('como_conheceu')) ?: null,
            'culto_visitado'       => trim((string) $this->request->getPost('culto_visitado')) ?: null,
            'membro_id'            => $this->request->getPost('membro_id') ?: null,
            'status'               => $this->request->getPost('status'),
            'observacoes'          => trim((string) $this->request->getPost('observacoes')) ?: null,
        ];
    }
}
