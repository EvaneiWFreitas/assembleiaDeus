<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Flash;
use App\Models\Configuracao;
use App\Models\Pagina;

final class MinisterioController extends AdminController
{
    private const SLUG_HISTORIA = 'historia';
    private const SLUG_PALAVRA = 'palavra-pastoral';
    private const TAMANHO_MAXIMO = 2097152;

    public function historiaForm(): void
    {
        $this->guard('site.editar');

        $pagina = Pagina::buscarPorSlug(self::SLUG_HISTORIA) ?? [];
        $imagem = trim((string) ($pagina['imagem'] ?? ''));

        $this->renderAdmin('admin.ministerio.historia', [
            'title'     => 'Nossa História',
            'subtitulo' => 'Gerencie o conteúdo da página de história do ministério.',
            'pagina'    => [
                'titulo'         => (string) ($pagina['titulo'] ?? 'Nossa História'),
                'conteudo'       => (string) ($pagina['conteudo'] ?? ''),
                'meta_descricao' => (string) ($pagina['meta_descricao'] ?? ''),
                'ativa'          => (int) ($pagina['ativa'] ?? 1) === 1,
                'imagem'         => $imagem,
            ],
            'preview'   => $imagem !== '' ? url($imagem) : asset('images/historia.svg'),
        ]);
    }

    public function salvarHistoria(): void
    {
        $this->guard('site.editar');

        $anterior = Pagina::buscarPorSlug(self::SLUG_HISTORIA) ?? [];
        $titulo = trim((string) $this->request->post('titulo', ''));
        $conteudo = $this->sanitizarHtml((string) $this->request->post('conteudo', ''));
        $metaDescricao = trim((string) $this->request->post('meta_descricao', ''));
        $ativa = $this->request->post('ativa', '0') === '1' ? 1 : 0;

        if ($titulo === '' || $conteudo === '') {
            $this->falhar('Preencha o título e o conteúdo da página.');
        }

        $novaImagem = null;
        $arquivo = $this->request->file('imagem');
        if ($arquivo !== null && (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $novaImagem = $this->uploadImagem($arquivo, 'pagina-historia');
            if ($novaImagem === null) {
                $this->falhar('A imagem deve ser JPG, PNG ou WebP, com até 2 MB.');
            }
        }

        $dados = [
            'titulo'         => mb_substr($titulo, 0, 180),
            'conteudo'       => $conteudo,
            'meta_descricao' => mb_substr($metaDescricao, 0, 255),
            'imagem'         => $novaImagem ?? trim((string) ($anterior['imagem'] ?? '')),
            'ativa'          => $ativa,
        ];

        Pagina::salvarPorSlug(self::SLUG_HISTORIA, $dados);

        if ($novaImagem !== null) {
            $this->removerArquivo((string) ($anterior['imagem'] ?? ''));
        }

        Audit::record('editar', 'site', $dados, $anterior, 'paginas', self::SLUG_HISTORIA);

        Flash::success('Página Nossa História atualizada.');
        $this->redirectAdmin('ministerio/historia');
    }

    public function removerImagem(): void
    {
        $this->guard('site.editar');

        $anterior = Pagina::buscarPorSlug(self::SLUG_HISTORIA) ?? [];
        $caminho = trim((string) ($anterior['imagem'] ?? ''));

        $this->removerArquivo($caminho);
        Pagina::salvarPorSlug(self::SLUG_HISTORIA, [
            'titulo'         => (string) ($anterior['titulo'] ?? 'Nossa História'),
            'conteudo'       => (string) ($anterior['conteudo'] ?? ''),
            'meta_descricao' => (string) ($anterior['meta_descricao'] ?? ''),
            'imagem'         => '',
            'ativa'          => (int) ($anterior['ativa'] ?? 1),
        ]);

        Audit::record(
            'editar',
            'site',
            ['imagem' => ''],
            ['imagem' => $caminho],
            'paginas',
            self::SLUG_HISTORIA
        );

        Flash::success('Imagem removida da página Nossa História.');
        $this->redirectAdmin('ministerio/historia');
    }

    public function palavraForm(): void
    {
        $this->guard('site.editar');

        $pagina = Pagina::buscarPorSlug(self::SLUG_PALAVRA) ?? [];
        $imagem = trim((string) ($pagina['imagem'] ?? ''));
        $fotoPastor = trim((string) Configuracao::get('pastor_foto', ''));

        $this->renderAdmin('admin.ministerio.palavra-pastoral', [
            'title'          => 'Palavra Pastoral',
            'subtitulo'      => 'Gerencie a mensagem pastoral e a foto do Pastor Presidente.',
            'pagina'         => [
                'titulo'         => (string) ($pagina['titulo'] ?? 'Palavra Pastoral'),
                'conteudo'       => (string) ($pagina['conteudo'] ?? ''),
                'meta_descricao' => (string) ($pagina['meta_descricao'] ?? ''),
                'ativa'          => (int) ($pagina['ativa'] ?? 1) === 1,
                'imagem'         => $imagem,
            ],
            'pastorNome'     => (string) Configuracao::get('pastor_presidente', ''),
            'fotoPastor'     => $fotoPastor,
            'previewPagina'  => $imagem !== '' ? url($imagem) : asset('images/placeholder.svg'),
            'previewPastor'  => $fotoPastor !== '' ? url($fotoPastor) : asset('images/placeholder.svg'),
        ]);
    }

    public function salvarPalavra(): void
    {
        $this->guard('site.editar');

        $anterior = Pagina::buscarPorSlug(self::SLUG_PALAVRA) ?? [];
        $titulo = trim((string) $this->request->post('titulo', ''));
        $conteudo = $this->sanitizarHtml((string) $this->request->post('conteudo', ''));
        $metaDescricao = trim((string) $this->request->post('meta_descricao', ''));
        $ativa = $this->request->post('ativa', '0') === '1' ? 1 : 0;
        $pastorNome = trim((string) $this->request->post('pastor_nome', ''));

        if ($titulo === '' || $conteudo === '') {
            $this->falhar('Preencha o título e o conteúdo da mensagem.');
        }

        $novaImagem = null;
        $arquivoImagem = $this->request->file('imagem');
        if ($arquivoImagem !== null && (int) ($arquivoImagem['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $novaImagem = $this->uploadImagem($arquivoImagem, 'pagina-palavra');
            if ($novaImagem === null) {
                $this->falhar('A imagem da página deve ser JPG, PNG ou WebP, com até 2 MB.');
            }
        }

        $novaFoto = null;
        $arquivoFoto = $this->request->file('foto_pastor');
        if ($arquivoFoto !== null && (int) ($arquivoFoto['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $novaFoto = $this->uploadImagem($arquivoFoto, 'pastor');
            if ($novaFoto === null) {
                $this->falhar('A foto do pastor deve ser JPG, PNG ou WebP, com até 2 MB.');
            }
        }

        $dados = [
            'titulo'         => mb_substr($titulo, 0, 180),
            'conteudo'       => $conteudo,
            'meta_descricao' => mb_substr($metaDescricao, 0, 255),
            'imagem'         => $novaImagem ?? trim((string) ($anterior['imagem'] ?? '')),
            'ativa'          => $ativa,
        ];

        Pagina::salvarPorSlug(self::SLUG_PALAVRA, $dados);

        $configAnterior = [
            'pastor_presidente' => Configuracao::get('pastor_presidente', ''),
            'pastor_foto'       => Configuracao::get('pastor_foto', ''),
        ];
        Configuracao::set('pastor_presidente', mb_substr($pastorNome, 0, 120));
        Configuracao::set('pastor_foto', $novaFoto ?? trim((string) $configAnterior['pastor_foto']));

        if ($novaImagem !== null) {
            $this->removerArquivo((string) ($anterior['imagem'] ?? ''));
        }
        if ($novaFoto !== null) {
            $this->removerArquivo((string) $configAnterior['pastor_foto']);
        }

        Audit::record('editar', 'site', $dados, $anterior, 'paginas', self::SLUG_PALAVRA);
        Audit::record('editar', 'site', [
            'pastor_presidente' => mb_substr($pastorNome, 0, 120),
            'pastor_foto'       => $novaFoto ?? trim((string) $configAnterior['pastor_foto']),
        ], $configAnterior, 'configuracoes', 'pastor_presidente');

        Flash::success('Palavra Pastoral atualizada.');
        $this->redirectAdmin('ministerio/palavra-pastoral');
    }

    public function removerImagemPalavra(): void
    {
        $this->guard('site.editar');

        $anterior = Pagina::buscarPorSlug(self::SLUG_PALAVRA) ?? [];
        $caminho = trim((string) ($anterior['imagem'] ?? ''));

        $this->removerArquivo($caminho);
        Pagina::salvarPorSlug(self::SLUG_PALAVRA, [
            'titulo'         => (string) ($anterior['titulo'] ?? 'Palavra Pastoral'),
            'conteudo'       => (string) ($anterior['conteudo'] ?? ''),
            'meta_descricao' => (string) ($anterior['meta_descricao'] ?? ''),
            'imagem'         => '',
            'ativa'          => (int) ($anterior['ativa'] ?? 1),
        ]);

        Audit::record('editar', 'site', ['imagem' => ''], ['imagem' => $caminho], 'paginas', self::SLUG_PALAVRA);

        Flash::success('Imagem da Palavra Pastoral removida.');
        $this->redirectAdmin('ministerio/palavra-pastoral');
    }

    public function removerFotoPastor(): void
    {
        $this->guard('site.editar');

        $anterior = trim((string) Configuracao::get('pastor_foto', ''));
        $this->removerArquivo($anterior);
        Configuracao::set('pastor_foto', '');

        Audit::record('editar', 'site', ['pastor_foto' => ''], ['pastor_foto' => $anterior], 'configuracoes', 'pastor_foto');

        Flash::success('Foto do Pastor Presidente removida.');
        $this->redirectAdmin('ministerio/palavra-pastoral');
    }

    private function sanitizarHtml(string $html): string
    {
        $permitidas = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4>'
            . '<blockquote><a><img><div><span><table><thead><tbody><tr><th><td><figure><figcaption>';
        $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $html = strip_tags($html, $permitidas);
        $html = preg_replace("/\son\w+\s*=\s*(?:\"[^\"]*\"|'[^']*'|[^\s>]+)/i", '', $html) ?? $html;
        $html = preg_replace("/\s(?:href|src)\s*=\s*([\"'])\s*javascript:[^\"']*\1/i", '', $html) ?? $html;

        return trim($html);
    }

    private function uploadImagem(array $arquivo, string $prefixo = 'pagina'): ?string
    {
        if ((int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        if ((int) ($arquivo['size'] ?? 0) > self::TAMANHO_MAXIMO) {
            return null;
        }

        $temporario = (string) ($arquivo['tmp_name'] ?? '');
        if ($temporario === '' || !is_uploaded_file($temporario)) {
            return null;
        }

        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $info === false ? '' : (string) finfo_file($info, $temporario);
        if ($info !== false) {
            finfo_close($info);
        }

        $extensoes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($extensoes[$mime])) {
            return null;
        }

        $dimensoes = @getimagesize($temporario);
        if ($dimensoes === false || (int) $dimensoes[0] < 1 || (int) $dimensoes[1] < 1) {
            return null;
        }

        $nome = $prefixo . '-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensoes[$mime];
        if (!move_uploaded_file($temporario, ROOT_PATH . '/public/uploads/' . $nome)) {
            return null;
        }

        return 'uploads/' . $nome;
    }

    private function removerArquivo(string $caminho): void
    {
        if ($caminho === '' || !str_starts_with($caminho, 'uploads/')) {
            return;
        }

        $caminhoCompleto = ROOT_PATH . '/public/' . $caminho;
        if (is_file($caminhoCompleto)) {
            @unlink($caminhoCompleto);
        }
    }
}
