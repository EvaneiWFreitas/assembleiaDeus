<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Libraries\Auditoria;
use App\Models\BannerModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * CRUD de banners/imagens de fundo da página principal do site.
 */
class Banners extends BaseController
{
    private const DIR_UPLOAD = 'public/uploads/banners/';

    private BannerModel $banners;

    public function __construct()
    {
        $this->banners = new BannerModel();
    }

    public function index(): string
    {
        $this->exigirPermissao('banners', 'visualizar');

        $busca = $this->request->getGet('q');
        $this->dados['titulo']  = 'Banners';
        $this->dados['banners'] = $this->banners->listarPainel($busca);
        $this->dados['busca']   = $busca;

        return view('banners/index', $this->dados);
    }

    public function novo(): string
    {
        $this->exigirPermissao('banners', 'cadastrar');
        $this->dados['titulo'] = 'Novo Banner';

        return view('banners/form', $this->dados);
    }

    public function salvar(): RedirectResponse
    {
        $this->exigirPermissao('banners', 'cadastrar');

        if (! $this->validar()) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $imagem = $this->receberImagem();
        if (! $imagem['ok']) {
            return redirect()->back()->withInput()->with('erro', $imagem['msg'])->with('erros', $imagem['erros'] ?? []);
        }

        $dados = $this->dadosPost();
        $dados['imagem'] = $imagem['arquivo'];

        if (! $this->banners->insert($dados)) {
            @unlink(ROOTPATH . self::DIR_UPLOAD . $imagem['arquivo']);
            return redirect()->back()->withInput()->with('erros', $this->banners->errors());
        }

        $id = (int) $this->banners->getInsertID();
        (new Auditoria())->log('criar', 'banners', $id, null, ['titulo' => $dados['titulo'], 'imagem' => $dados['imagem']]);

        return redirect()->to('/banners')->with('sucesso', 'Banner cadastrado com sucesso!');
    }

    public function editar(int $id): string
    {
        $this->exigirPermissao('banners', 'editar');

        $this->dados['titulo']   = 'Editar Banner';
        $this->dados['registro'] = $this->banners->find($id);

        if ($this->dados['registro'] === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('banners/form', $this->dados);
    }

    public function atualizar(int $id): RedirectResponse
    {
        $this->exigirPermissao('banners', 'editar');

        $antes = $this->banners->find($id);
        if ($antes === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validar()) {
            return redirect()->back()->withInput()->with('erros', $this->validator->getErrors());
        }

        $dados  = $this->dadosPost();
        $imagem = $this->receberImagem(obrigatoria: false);

        if (! $imagem['ok']) {
            return redirect()->back()->withInput()->with('erro', $imagem['msg'])->with('erros', $imagem['erros'] ?? []);
        }

        if ($imagem['arquivo'] !== null) {
            $dados['imagem'] = $imagem['arquivo'];
        }

        if (! $this->banners->update($id, $dados)) {
            if (isset($dados['imagem']) && $dados['imagem'] !== $antes['imagem']) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $dados['imagem']);
            }
            return redirect()->back()->withInput()->with('erros', $this->banners->errors());
        }

        // Remove a imagem antiga quando uma nova foi enviada
        if ($imagem['arquivo'] !== null && ! empty($antes['imagem']) && $antes['imagem'] !== $imagem['arquivo']) {
            @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['imagem']);
        }

        (new Auditoria())->log('editar', 'banners', $id, ['titulo' => $antes['titulo']], ['titulo' => $dados['titulo']]);

        return redirect()->to('/banners')->with('sucesso', 'Banner atualizado com sucesso!');
    }

    public function excluir(int $id): RedirectResponse
    {
        $this->exigirPermissao('banners', 'excluir');

        $antes = $this->banners->find($id);
        if ($antes !== null) {
            $this->banners->delete($id);
            if (! empty($antes['imagem'])) {
                @unlink(ROOTPATH . self::DIR_UPLOAD . $antes['imagem']);
            }
            (new Auditoria())->log('excluir', 'banners', $id, ['titulo' => $antes['titulo']]);
        }

        return redirect()->to('/banners')->with('sucesso', 'Banner excluído com sucesso!');
    }

    public function alternarAtivo(int $id): RedirectResponse
    {
        $this->exigirPermissao('banners', 'editar');

        $registro = $this->banners->find($id);
        if ($registro !== null) {
            $this->banners->update($id, ['ativo' => $registro['ativo'] ? 0 : 1]);
            (new Auditoria())->log('editar', 'banners', $id, ['ativo' => $registro['ativo']], ['ativo' => (int) ! (bool) $registro['ativo']]);
        }

        return redirect()->to('/banners')->with('sucesso', 'Situação do banner atualizada!');
    }

    private function validar(): bool
    {
        $regras = $this->banners->validationRules;
        $regras['imagem'] = 'permit_empty';

        return $this->validate($regras);
    }

    /**
     * Recebe e valida a imagem enviada (PNG/JPG/WebP, até 5MB).
     *
     * @return array{ok: bool, arquivo: string|null, msg: string, erros: array}
     */
    private function receberImagem(bool $obrigatoria = true): array
    {
        $arquivo = $this->request->getFile('imagem');

        if ($arquivo === null || ! $arquivo->isValid()) {
            if ($obrigatoria) {
                return ['ok' => false, 'arquivo' => null, 'msg' => 'A imagem de fundo é obrigatória.', 'erros' => ['imagem' => 'A imagem de fundo é obrigatória.']];
            }
            return ['ok' => true, 'arquivo' => null, 'msg' => '', 'erros' => []];
        }

        if (! in_array($arquivo->getMimeType(), ['image/png', 'image/jpeg', 'image/webp'], true)) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'Formato inválido: use PNG, JPG ou WebP.', 'erros' => ['imagem' => 'Formato inválido: use PNG, JPG ou WebP.']];
        }

        if ($arquivo->getSizeByUnit('mb') > 5) {
            return ['ok' => false, 'arquivo' => null, 'msg' => 'A imagem deve ter no máximo 5MB.', 'erros' => ['imagem' => 'A imagem deve ter no máximo 5MB.']];
        }

        $diretorio = ROOTPATH . self::DIR_UPLOAD;
        if (! is_dir($diretorio)) {
            mkdir($diretorio, 0775, true);
        }

        $nome = $arquivo->getRandomName();
        $arquivo->move($diretorio, $nome);

        return ['ok' => true, 'arquivo' => $nome, 'msg' => '', 'erros' => []];
    }

    private function dadosPost(): array
    {
        return [
            'titulo'    => trim((string) $this->request->getPost('titulo')),
            'subtitulo' => trim((string) $this->request->getPost('subtitulo')) ?: null,
            'link'      => trim((string) $this->request->getPost('link')) ?: null,
            'ordem'     => (int) ($this->request->getPost('ordem') ?: 0),
            'ativo'     => $this->request->getPost('ativo') ? 1 : 0,
        ];
    }
}