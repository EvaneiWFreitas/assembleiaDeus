<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\CongregacaoModel;
use App\Models\MembroModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * CRUD de membros + ficha completa.
 */
class Membros extends BaseController
{
    private const DIR_UPLOAD = 'public/uploads/membros/';

    private MembroModel $membros;
    private CongregacaoModel $congregacoes;

    public function __construct()
    {
        $this->membros       = new MembroModel();
        $this->congregacoes  = new CongregacaoModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('membros', 'visualizar');

        $busca        = $this->request->getGet('q');
        $status       = $this->request->getGet('status');
        $congregacao  = (int) ($this->request->getGet('congregacao') ?: 0);
        $pagina       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $porPagina    = 20;

        $this->dados['titulo']    = 'Membros';
        $this->dados['membros']   = $this->membros->listar($busca, $status, $congregacao ?: null, $porPagina, ($pagina - 1) * $porPagina);
        $this->dados['total']     = $this->membros->contar($busca, $status, $congregacao ?: null);
        $this->dados['pagina']    = $pagina;
        $this->dados['porPagina'] = $porPagina;
        $this->dados['busca']     = $busca;
        $this->dados['statusFiltro'] = $status;
        $this->dados['congregacaoFiltro'] = $congregacao;
        $this->dados['congregacoes'] = $this->congregacoes->getDropdown();

        return view('membros/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('membros', 'cadastrar');

        $this->dados['titulo']       = 'Novo Membro';
        $this->dados['registro']     = null;
        $this->dados['congregacoes'] = $this->congregacoes->getDropdown();

        return view('membros/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('membros', 'cadastrar');

        $regras = $this->membros->validationRules;

        $cpf = preg_replace('/\D/', '', (string) $this->request->getPost('cpf'));
        if ($cpf !== '' && (strlen($cpf) !== 11 || $this->membros->cpfEmUso($cpf))) {
            $this->validator->setError('cpf', $this->membros->cpfValido($cpf)
                ? 'O CPF já está cadastrado.'
                : 'O CPF deve conter exatamente 11 dígitos.');
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        if (! $this->validate($regras)) {
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

        if (! $this->membros->insert($dados)) {
            if (isset($dados['foto'])) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['foto']);
            }
            return redirect()->back()->withInput()->with('erros', $this->membros->errors());
        }
        $id = (int) $this->membros->getInsertID();

        (new Auditoria())->log('criar', 'membros', $id, null, ['nome' => $dados['nome'], 'status' => $dados['status']]);

        return redirect()->to('/membros')->with('sucesso', 'Membro cadastrado com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('membros', 'editar');

        $this->dados['titulo']       = 'Editar Membro';
        $this->dados['registro']     = $this->membros->find($id);
        $this->dados['congregacoes'] = $this->congregacoes->getDropdown();

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('membros/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('membros', 'editar');

        $antes = $this->membros->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $regras = $this->membros->validationRules;

        $cpf = preg_replace('/\D/', '', (string) $this->request->getPost('cpf'));
        if ($cpf !== '' && (strlen($cpf) !== 11 || $this->membros->cpfEmUso($cpf, $id))) {
            $this->validator->setError('cpf', $this->membros->cpfValido($cpf)
                ? 'O CPF já está cadastrado.'
                : 'O CPF deve conter exatamente 11 dígitos.');
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        if (! $this->validate($regras)) {
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

        if (! $this->membros->update($id, $dados)) {
            if (isset($dados['foto']) && $dados['foto'] !== $antes['foto']) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['foto']);
            }
            return redirect()->back()->withInput()->with('erros', $this->membros->errors());
        }

        // Remove a foto antiga quando uma nova foi enviada
        if ($foto['arquivo'] !== null && ! empty($antes['foto']) && $antes['foto'] !== $foto['arquivo']) {
            @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['foto']);
        }

        (new Auditoria())->log('editar', 'membros', $id, ['nome' => $antes['nome'], 'status' => $antes['status']], ['nome' => $dados['nome'], 'status' => $dados['status']]);

        return redirect()->to('/membros')->with('sucesso', 'Membro atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('membros', 'excluir');

        $antes = $this->membros->find($id);
        if ($antes !== null) {
            $this->membros->delete($id); // soft delete
            if (! empty($antes['foto'])) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['foto']);
            }
            (new Auditoria())->log('excluir', 'membros', $id, ['nome' => $antes['nome']]);
        }

        return redirect()->to('/membros')->with('sucesso', 'Membro excluído com sucesso!');
    }

    public function ficha(int $id): string
    {
        $this->exigirPermissao('membros', 'visualizar');

        $this->dados['titulo']   = 'Ficha do Membro';
        $this->dados['registro'] = $this->membros->find($id);

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->dados['congregacao'] = $this->dados['registro']['congregacao_id']
            ? $this->congregacoes->find($this->dados['registro']['congregacao_id'])['nome'] ?? '-'
            : '-';

        return view('membros/ficha', $this->dados);
    }

    private function dadosPost(): array
    {
        $camposTexto = ['nome', 'nome_social', 'rg', 'estado_civil', 'nacionalidade',
            'naturalidade', 'telefone', 'whatsapp', 'email', 'logradouro', 'complemento',
            'bairro', 'cidade', 'local_batismo', 'igreja_anterior', 'cargo', 'observacoes'];

        $dados = [];
        foreach ($camposTexto as $campo) {
            $dados[$campo] = trim((string) $this->request->getPost($campo)) ?: null;
        }

        return $dados + [
            'congregacao_id'   => $this->request->getPost('congregacao_id') ?: null,
            'cpf'              => preg_replace('/\D/', '', (string) $this->request->getPost('cpf')) ?: null,
            'data_nascimento'  => $this->request->getPost('data_nascimento') ?: null,
            'sexo'             => $this->request->getPost('sexo') ?: null,
            'cep'              => preg_replace('/\D/', '', (string) $this->request->getPost('cep')) ?: null,
            'numero'           => trim((string) $this->request->getPost('numero')) ?: null,
            'estado'           => strtoupper(trim((string) $this->request->getPost('estado'))) ?: null,
            'data_conversao'   => $this->request->getPost('data_conversao') ?: null,
            'data_batismo'     => $this->request->getPost('data_batismo') ?: null,
            'data_recebimento' => $this->request->getPost('data_recebimento') ?: null,
            'status'           => $this->request->getPost('status') ?: 'Ativo',
            'tipo_membro'      => trim((string) $this->request->getPost('tipo_membro')) ?: 'Comunhão',
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
