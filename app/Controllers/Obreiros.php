<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CongregacaoModel;
use App\Models\MembroModel;
use App\Models\ObreiroModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * CRUD de pastores e obreiros (vinculado ao registro de membro).
 */
class Obreiros extends BaseController
{
    private const DIR_UPLOAD = 'public/uploads/obreiros/';

    private ObreiroModel $obreiros;
    private MembroModel $membros;
    private CongregacaoModel $congregacoes;

    public function __construct()
    {
        $this->obreiros     = new ObreiroModel();
        $this->membros      = new MembroModel();
        $this->congregacoes = new CongregacaoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('obreiros', 'visualizar');

        $busca  = $this->request->getGet('q');
        $cargo  = $this->request->getGet('cargo');
        $pagina = max(1, (int) ($this->request->getGet('page') ?? 1));
        $porPagina = 20;

        $this->dados['titulo']    = 'Pastores & Obreiros';
        $this->dados['obreiros']  = $this->obreiros->listar($busca, $cargo, $porPagina, ($pagina - 1) * $porPagina);
        $this->dados['total']     = $this->obreiros->contar($busca, $cargo);
        $this->dados['pagina']    = $pagina;
        $this->dados['porPagina'] = $porPagina;
        $this->dados['busca']     = $busca;
        $this->dados['cargoFiltro'] = $cargo;
        $this->dados['cargos']    = $this->obreiros->getCargosAtivos();

        return view('obreiros/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('obreiros', 'cadastrar');
        $this->carregarDropdowns();

        $this->dados['titulo']   = 'Novo Obreiro';
        $this->dados['registro'] = null;
        $this->dados['cargos']   = $this->obreiros->getCargosAtivos();

        return view('obreiros/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('obreiros', 'cadastrar');

        if (! $this->validate($this->obreiros->getValidationRules())) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();

        $foto = $this->receberFoto();
        if (! $foto['ok']) {
            return redirect()->back()->withInput()->with('erro', $foto['msg'])->with('erros', $foto['erros'] ?? []);
        }
        if ($foto['arquivo'] !== null) {
            $dados['foto'] = $foto['arquivo'];
        }

        if (! $this->obreiros->insert($dados)) {
            if (isset($dados['foto'])) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['foto']);
            }
            return redirect()->back()->withInput()->with('erros', $this->obreiros->errors());
        }
        $id = (int) $this->obreiros->getInsertID();

        (new Auditoria())->log('criar', 'obreiros', $id, null, ['membro_id' => $dados['membro_id'], 'cargo' => $dados['cargo']]);

        return redirect()->to('/obreiros')->with('sucesso', 'Obreiro cadastrado com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('obreiros', 'editar');
        $this->carregarDropdowns();

        $this->dados['titulo']   = 'Editar Obreiro';
        $this->dados['registro'] = $this->obreiros->find($id);
        $this->dados['cargos']   = $this->obreiros->getCargosAtivos();

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('obreiros/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('obreiros', 'editar');

        $antes = $this->obreiros->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->obreiros->getValidationRules())) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();

        $foto = $this->receberFoto();
        if (! $foto['ok']) {
            return redirect()->back()->withInput()->with('erro', $foto['msg'])->with('erros', $foto['erros'] ?? []);
        }
        if ($foto['arquivo'] !== null) {
            $dados['foto'] = $foto['arquivo'];
        }

        if (! $this->obreiros->update($id, $dados)) {
            if (isset($dados['foto']) && $dados['foto'] !== $antes['foto']) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['foto']);
            }
            return redirect()->back()->withInput()->with('erros', $this->obreiros->errors());
        }

        // Remove a foto antiga quando uma nova foi enviada
        if ($foto['arquivo'] !== null && ! empty($antes['foto']) && $antes['foto'] !== $foto['arquivo']) {
            @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['foto']);
        }

        (new Auditoria())->log('editar', 'obreiros', $id, ['cargo' => $antes['cargo']], ['cargo' => $dados['cargo']]);

        return redirect()->to('/obreiros')->with('sucesso', 'Obreiro atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('obreiros', 'excluir');

        $antes = $this->obreiros->find($id);
        if ($antes !== null) {
            $this->obreiros->delete($id);
            if (! empty($antes['foto'])) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['foto']);
            }
            (new Auditoria())->log('excluir', 'obreiros', $id, ['cargo' => $antes['cargo']]);
        }

        return redirect()->to('/obreiros')->with('sucesso', 'Obreiro excluído com sucesso!');
    }

    private function carregarDropdowns(): void
    {
        $this->dados['membros']      = $this->membros->getDropdown();
        $this->dados['congregacoes'] = $this->congregacoes->getDropdown();
    }

    private function dadosPost(): array
    {
        return [
            'membro_id'       => (int) $this->request->getPost('membro_id'),
            'cargo'           => $this->request->getPost('cargo'),
            'data_conexao'    => $this->request->getPost('data_conexao') ?: null,
            'congregacao_id'  => $this->request->getPost('congregacao_id') ?: null,
            'numero_registro' => trim((string) $this->request->getPost('numero_registro')) ?: null,
            'observacoes'     => trim((string) $this->request->getPost('observacoes')) ?: null,
            'ativo'           => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }

    /**
     * Recebe e valida a foto enviada (PNG/JPG/WebP, até 5MB).
     */
    private function receberFoto(): array
    {
        $arquivo = $this->request->getFile('foto');

        if ($arquivo === null || ! $arquivo->isValid()) {
            return ['ok' => true, 'arquivo' => null, 'msg' => '', 'erros' => []];
        }

        if (! in_array($arquivo->getMimeType(), ['image/png', 'image/jpeg', 'image/webp'], true)) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'Formato inválido: use PNG, JPG ou WebP.', 'erros' => ['foto' => 'Formato inválido: use PNG, JPG ou WebP.']];
        }

        if ($arquivo->getSizeByUnit('mb') > 5) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'A foto deve ter no máximo 5MB.', 'erros' => ['foto' => 'A foto deve ter no máximo 5MB.']];
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
