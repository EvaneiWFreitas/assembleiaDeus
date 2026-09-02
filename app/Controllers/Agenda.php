<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\AgendaModel;
use App\Models\AgendaPresencaModel;
use App\Models\CongregacaoModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD da agenda de compromissos e eventos.
 */
class Agenda extends BaseController
{
    private const DIR_UPLOAD = 'public/uploads/agenda/';

    private AgendaModel $agenda;
    private CongregacaoModel $congregacoes;

    public function __construct()
    {
        $this->agenda       = new AgendaModel();
        $this->congregacoes = new CongregacaoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('agenda', 'visualizar');

        $busca        = $this->request->getGet('q');
        $status       = $this->request->getGet('status');
        $tipo         = $this->request->getGet('tipo');
        $congregacao  = (int) ($this->request->getGet('congregacao') ?: 0);
        $pagina       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $porPagina    = 20;

        $this->dados['titulo']           = 'Agenda';
        $this->dados['eventos']          = $this->agenda->listar($busca, $status, $tipo, $congregacao ?: null, $porPagina, ($pagina - 1) * $porPagina);
        $this->dados['total']            = $this->agenda->contar($busca, $status, $tipo, $congregacao ?: null);
        $this->dados['pagina']           = $pagina;
        $this->dados['porPagina']        = $porPagina;
        $this->dados['busca']            = $busca;
        $this->dados['statusFiltro']     = $status;
        $this->dados['tipoFiltro']       = $tipo;
        $this->dados['congregacaoFiltro'] = $congregacao;
        $this->dados['congregacoes']     = $this->congregacoes->getDropdown();

        return view('agenda/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('agenda', 'cadastrar');

        $this->dados['titulo']       = 'Novo Evento';
        $this->dados['registro']     = null;
        $this->dados['congregacoes'] = $this->congregacoes->getDropdown();

        return view('agenda/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('agenda', 'cadastrar');

        if (! $this->validate($this->agenda->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();

        $foto = $this->receberFoto();
        if (! $foto['ok']) {
            return redirect()->back()->withInput()->with('erro', $foto['msg'])->with('erros', $foto['erros'] ?? []);
        }
        if ($foto['arquivo'] !== null) {
            $dados['foto_responsavel'] = $foto['arquivo'];
        }

        if (! $this->agenda->insert($dados)) {
            if (isset($dados['foto_responsavel'])) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['foto_responsavel']);
            }
            return redirect()->back()->withInput()->with('erros', $this->agenda->errors());
        }
        $id = (int) $this->agenda->getInsertID();

        (new Auditoria())->log('criar', 'agenda', $id, null, ['titulo' => $dados['titulo'], 'data_inicio' => $dados['data_inicio']]);

        return redirect()->to('/agenda')->with('sucesso', 'Evento cadastrado com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('agenda', 'editar');

        $this->dados['titulo']       = 'Editar Evento';
        $this->dados['registro']     = $this->agenda->find($id);
        $this->dados['congregacoes'] = $this->congregacoes->getDropdown();

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('agenda/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('agenda', 'editar');

        $antes = $this->agenda->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->agenda->validationRules)) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();

        $foto = $this->receberFoto();
        if (! $foto['ok']) {
            return redirect()->back()->withInput()->with('erro', $foto['msg'])->with('erros', $foto['erros'] ?? []);
        }
        if ($foto['arquivo'] !== null) {
            $dados['foto_responsavel'] = $foto['arquivo'];
        }

        if (! $this->agenda->update($id, $dados)) {
            if (isset($dados['foto_responsavel']) && $dados['foto_responsavel'] !== $antes['foto_responsavel'] ?? null) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['foto_responsavel']);
            }
            return redirect()->back()->withInput()->with('erros', $this->agenda->errors());
        }

        // Remove a foto antiga quando uma nova for enviada
        if ($foto['arquivo'] !== null && ! empty($antes['foto_responsavel'] ?? null) && ($antes['foto_responsavel'] ?? null) !== $foto['arquivo']) {
            @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['foto_responsavel']);
        }

        (new Auditoria())->log('editar', 'agenda', $id, ['titulo' => $antes['titulo']], ['titulo' => $dados['titulo']]);

        return redirect()->to('/agenda')->with('sucesso', 'Evento atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('agenda', 'excluir');

        $antes = $this->agenda->find($id);
        if ($antes !== null) {
            $this->agenda->delete($id); // soft delete
            (new Auditoria())->log('excluir', 'agenda', $id, ['titulo' => $antes['titulo']]);
        }

        return redirect()->to('/agenda')->with('sucesso', 'Evento excluído com sucesso!');
    }

    /**
     * API simples para calendário (FullCalendar-compatible).
     */
    public function calendario(): string
    {
        $this->exigirPermissao('agenda', 'visualizar');

        $dataInicio = $this->request->getGet('start');
        $dataFim    = $this->request->getGet('end');

        $eventos = $this->agenda->eventosCalendario($dataInicio, $dataFim);

        return $this->response->setJSON($eventos);
    }

    /**
     * Confirmações de presença de um evento.
     */
    public function presencas(int $id): string
    {
        $this->exigirPermissao('agenda', 'visualizar');

        $evento = $this->agenda->find($id);
        if ($evento === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $presencas = new AgendaPresencaModel();

        $this->dados['titulo']      = 'Presenças — ' . $evento['titulo'];
        $this->dados['evento']      = $evento;
        $this->dados['presencas']   = $presencas->listarPorEvento($id);
        $this->dados['total']       = count($this->dados['presencas']);
        $this->dados['membros']     = (new \App\Models\MembroModel())->getDropdown();

        return view('agenda/presencas', $this->dados);
    }

    /**
     * Adiciona uma presença manualmente (vínculo com membro cadastrado).
     */
    public function adicionarPresenca(int $id): RedirectResponse
    {
        $this->exigirPermissao('agenda', 'editar');

        $evento = $this->agenda->find($id);
        if ($evento === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $presencas = new AgendaPresencaModel();

        $membroId  = $this->request->getPost('membro_id') ?: null;
        $nome      = trim((string) $this->request->getPost('nome'));
        $telefone  = trim((string) $this->request->getPost('telefone'));
        $confirmadoEm = $this->request->getPost('confirmado_em') ?: date('Y-m-d H:i:s');

        if ($membroId) {
            $membro = (new \App\Models\MembroModel())->find((int) $membroId);
            if ($membro !== null) {
                $nome = $membro['nome'];
            }
        }

        $confirmadoEm = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $confirmadoEm)));

        $presencas->save([
            'agenda_id'     => $id,
            'membro_id'     => $membroId ? (int) $membroId : null,
            'nome'          => $nome,
            'telefone'      => $telefone,
            'confirmado_em' => $confirmadoEm,
        ]);

        if ($presencas->errors()) {
            return redirect()->back()->withInput()->with('erros', $presencas->errors());
        }

        return redirect()->to('/agenda/presencas/' . $id)->with('sucesso', 'Presença registrada com sucesso!');
    }

    /**
     * Remove uma confirmação de presença.
     */
    public function removerPresenca(int $id): RedirectResponse
    {
        $this->exigirPermissao('agenda', 'excluir');

        $presenca = (new AgendaPresencaModel())->find($id);
        if ($presenca !== null) {
            $agendaId = (int) $presenca['agenda_id'];
            (new AgendaPresencaModel())->delete($id); // soft delete
            return redirect()->to('/agenda/presencas/' . $agendaId)->with('sucesso', 'Presença removida com sucesso!');
        }

        return redirect()->to('/agenda')->with('erro', 'Presença não encontrada.');
    }

    private function dadosPost(): array
    {
        $camposTexto = ['titulo', 'descricao', 'local', 'tipo', 'responsavel', 'recorrencia', 'cor', 'observacoes', 'status'];

        $dados = [];
        foreach ($camposTexto as $campo) {
            $dados[$campo] = trim((string) $this->request->getPost($campo)) ?: null;
        }

        return $dados + [
            'data_inicio'    => $this->request->getPost('data_inicio') ?: null,
            'data_fim'       => $this->request->getPost('data_fim') ?: null,
            'hora_inicio'    => $this->request->getPost('hora_inicio') ?: null,
            'hora_fim'       => $this->request->getPost('hora_fim') ?: null,
            'congregacao_id' => $this->request->getPost('congregacao_id') ?: null,
            'status'         => $this->request->getPost('status') ?: 'Pendente',
            'tipo'           => $this->request->getPost('tipo') ?: 'Reunião',
            'cor'            => $this->request->getPost('cor') ?: '#0d6efd',
        ];
    }

    /**
     * Recebe e valida a foto do responsável (PNG/JPG/WebP, até 5MB).
     */
    private function receberFoto(): array
    {
        $arquivo = $this->request->getFile('foto_responsavel');

        if ($arquivo === null || ! $arquivo->isValid()) {
            return ['ok' => true, 'arquivo' => null, 'msg' => '', 'erros' => []];
        }

        if (! in_array($arquivo->getMimeType(), ['image/png', 'image/jpeg', 'image/webp'], true)) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'Formato inválido: use PNG, JPG ou WebP.', 'erros' => ['foto_responsavel' => 'Formato inválido: use PNG, JPG ou WebP.']];
        }

        if ($arquivo->getSizeByUnit('mb') > 5) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'A foto deve ter no máximo 5MB.', 'erros' => ['foto_responsavel' => 'A foto deve ter no máximo 5MB.']];
        }

        $diretorio = ROOTPATH . self::DIR_UPLOAD;
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $nome = $arquivo->getRandomName();
        $arquivo->move($diretorio, $nome);

        return ['ok' => true, 'arquivo' => $nome, 'msg' => '', 'erros' => []];
    }
}