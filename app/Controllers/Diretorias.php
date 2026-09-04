<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\DiretoriaModel;
use CodeIgniter\HTTP\RedirectResponse;

class Diretorias extends BaseController
{
    private const DIR_UPLOAD = 'public/uploads/diretorias/';

    private DiretoriaModel $diretorias;

    public function __construct()
    {
        $this->diretorias = new DiretoriaModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('diretorias', 'visualizar');

        $busca  = $this->request->getGet('q');
        $pagina = max(1, (int) ($this->request->getGet('page') ?? 1));
        $porPagina = 20;

        $this->dados['titulo']    = 'Diretoria da Igreja';
        $this->dados['diretorias'] = $this->diretorias->listar($busca, $porPagina, ($pagina - 1) * $porPagina);
        $this->dados['total']     = $this->diretorias->contar($busca);
        $this->dados['pagina']    = $pagina;
        $this->dados['porPagina'] = $porPagina;
        $this->dados['busca']     = $busca;

        return view('diretorias/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('diretorias', 'cadastrar');

        $this->dados['titulo']    = 'Novo Membro da Diretoria';
        $this->dados['registro']  = null;

        return view('diretorias/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('diretorias', 'cadastrar');

        if (! $this->validate($this->diretorias->getValidationRules())) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();

        $foto = $this->receberFoto();
        if (! $foto['ok']) {
            return redirect()->back()->withInput()->with('erro', $foto['msg']);
        }
        if ($foto['arquivo'] !== null) {
            $dados['foto'] = $foto['arquivo'];
        }

        if (! $this->diretorias->insert($dados)) {
            if (isset($dados['foto'])) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['foto']);
            }
            return redirect()->back()->withInput()->with('erros', $this->diretorias->errors());
        }

        return redirect()->to('/diretorias')->with('sucesso', 'Membro da diretoria cadastrado com sucesso!');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $this->exigirPermissao('diretorias', 'editar');

        $registro = $this->diretorias->find($id);

        if ($registro === null) {
            return redirect()->to('/diretorias')->with('erro', 'Registro não encontrado.');
        }

        $this->dados['titulo']   = 'Editar Membro da Diretoria';
        $this->dados['registro'] = $registro;

        return view('diretorias/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('diretorias', 'editar');

        $antes = $this->diretorias->find($id);
        if ($antes === null) {
            return redirect()->to('/diretorias')->with('erro', 'Registro não encontrado.');
        }

        if (! $this->validate($this->diretorias->getValidationRules())) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados = $this->dadosPost();

        $foto = $this->receberFoto();
        if (! $foto['ok']) {
            return redirect()->back()->withInput()->with('erro', $foto['msg']);
        }
        if ($foto['arquivo'] !== null) {
            $dados['foto'] = $foto['arquivo'];
        }

        if (! $this->diretorias->update($id, $dados)) {
            if (isset($dados['foto']) && $dados['foto'] !== ($antes['foto'] ?? '')) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['foto']);
            }
            return redirect()->back()->withInput()->with('erros', $this->diretorias->errors());
        }

        if ($foto['arquivo'] !== null && ! empty($antes['foto']) && $antes['foto'] !== $foto['arquivo']) {
            @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['foto']);
        }

        return redirect()->to('/diretorias')->with('sucesso', 'Membro da diretoria atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('diretorias', 'excluir');

        $antes = $this->diretorias->find($id);

        if ($antes === null) {
            return redirect()->to('/diretorias')->with('erro', 'Registro não encontrado.');
        }

        $this->diretorias->delete($id);

        if (! empty($antes['foto'])) {
            @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['foto']);
        }

        return redirect()->to('/diretorias')->with('sucesso', 'Membro da diretoria excluído com sucesso!');
    }

    private function dadosPost(): array
    {
        return [
            'nome'     => trim((string) $this->request->getPost('nome')),
            'cargo'    => trim((string) $this->request->getPost('cargo')),
            'email'    => trim((string) $this->request->getPost('email')) ?: null,
            'telefone' => trim((string) $this->request->getPost('telefone')) ?: null,
            'whatsapp' => trim((string) $this->request->getPost('whatsapp')) ?: null,
            'ordem'    => (int) $this->request->getPost('ordem'),
            'ativo'    => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }

    private function receberFoto(): array
    {
        $arquivo = $this->request->getFile('foto');

        if ($arquivo === null || ! $arquivo->isValid()) {
            return ['ok' => true, 'arquivo' => null, 'msg' => ''];
        }

        if (! in_array($arquivo->getMimeType(), ['image/png', 'image/jpeg', 'image/webp'], true)) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'Formato inválido: use PNG, JPG ou WebP.'];
        }

        if ($arquivo->getSizeByUnit('mb') > 5) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'A foto deve ter no máximo 5MB.'];
        }

        $diretorio = ROOTPATH . self::DIR_UPLOAD;
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $nome = $arquivo->getRandomName();
        $arquivo->move($diretorio, $nome);

        return ['ok' => true, 'arquivo' => $nome, 'msg' => ''];
    }
}
