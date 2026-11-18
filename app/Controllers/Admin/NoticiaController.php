<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Acl;
use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Auth;
use App\Core\Flash;
use App\Models\Noticia;

final class NoticiaController extends AdminController
{
    private const TAMANHO_IMAGEM = 2097152;
    private const TAMANHO_VIDEO = 104857600;

    public function index(): void
    {
        $this->guard('site_noticias.ver');

        $this->renderAdmin('admin.noticias.index', [
            'title'       => 'Notícias',
            'subtitulo'   => 'Gerencie as notícias exibidas na página inicial e na página de notícias.',
            'noticias'    => Noticia::todas(),
            'podeCriar'   => Acl::can('site_noticias.criar'),
            'podeEditar'  => Acl::can('site_noticias.editar'),
            'podeExcluir' => Acl::can('site_noticias.excluir'),
        ]);
    }

    public function novoForm(): void
    {
        $this->guard('site_noticias.criar');

        $this->renderAdmin('admin.noticias.form', [
            'title'    => 'Nova notícia',
            'subtitulo'=> 'Cadastre uma notícia com imagem e/ou vídeo.',
            'noticia'  => $this->noticiaPadrao(),
            'acao'     => url('admin/noticias'),
        ]);
    }

    public function criar(): void
    {
        $this->guard('site_noticias.criar');

        $dados = $this->dadosValidados();
        $dados['imagem'] = $this->processarImagem();
        $dados['video'] = $this->processarVideo($dados['video']);

        $id = Noticia::criar($dados);

        Audit::record('criar', 'site_noticias', $dados, [], 'noticias', (string) $id);

        Flash::success('Notícia cadastrada com sucesso.');
        $this->redirectAdmin('noticias');
    }

    public function editarForm(string $id): void
    {
        $this->guard('site_noticias.editar');

        $noticia = Noticia::buscar((int) $id);
        if ($noticia === null) {
            Flash::error('Notícia não encontrada.');
            $this->redirectAdmin('noticias');
        }

        $noticia['publicada_em'] = !empty($noticia['publicada_em'])
            ? str_replace(' ', 'T', substr((string) $noticia['publicada_em'], 0, 16))
            : '';
        $noticia['video'] = str_starts_with((string) ($noticia['video'] ?? ''), 'noticias/')
            ? ''
            : (string) ($noticia['video'] ?? '');

        $this->renderAdmin('admin.noticias.form', [
            'title'     => 'Editar notícia',
            'subtitulo' => 'Atualize o conteúdo, a mídia e a publicação da notícia.',
            'noticia'   => $noticia,
            'acao'      => url('admin/noticias/' . (int) $id . '/editar'),
        ]);
    }

    public function editar(string $id): void
    {
        $this->guard('site_noticias.editar');

        $id = (int) $id;
        $anterior = Noticia::buscar($id);
        if ($anterior === null) {
            Flash::error('Notícia não encontrada.');
            $this->redirectAdmin('noticias');
        }

        $dados = $this->dadosValidados($anterior);
        $novaImagem = $this->processarImagem();
        $novoVideo = $this->processarVideo($dados['video']);

        $dados['imagem'] = $novaImagem ?? (string) ($anterior['imagem'] ?? '');
        $dados['video'] = $novoVideo ?? (string) ($anterior['video'] ?? '');

        Noticia::atualizar($id, $dados);

        if ($novaImagem !== null) {
            $this->removerArquivo((string) ($anterior['imagem'] ?? ''));
        }
        if ($novoVideo !== null) {
            $this->removerArquivo((string) ($anterior['video'] ?? ''));
        }

        Audit::record('editar', 'site_noticias', $dados, $anterior, 'noticias', (string) $id);

        Flash::success('Notícia atualizada com sucesso.');
        $this->redirectAdmin('noticias');
    }

    public function removerImagem(string $id): void
    {
        $this->guard('site_noticias.editar');

        $id = (int) $id;
        $anterior = Noticia::buscar($id);
        if ($anterior === null) {
            Flash::error('Notícia não encontrada.');
            $this->redirectAdmin('noticias');
        }

        $this->removerArquivo((string) ($anterior['imagem'] ?? ''));
        Noticia::atualizarCampo($id, 'imagem', null);
        Audit::record('editar', 'site_noticias', ['imagem' => null], ['imagem' => $anterior['imagem']], 'noticias', (string) $id);

        Flash::success('Imagem da notícia removida.');
        $this->redirectAdmin('noticias/' . $id . '/editar');
    }

    public function removerVideo(string $id): void
    {
        $this->guard('site_noticias.editar');

        $id = (int) $id;
        $anterior = Noticia::buscar($id);
        if ($anterior === null) {
            Flash::error('Notícia não encontrada.');
            $this->redirectAdmin('noticias');
        }

        $this->removerArquivo((string) ($anterior['video'] ?? ''));
        Noticia::atualizarCampo($id, 'video', null);
        Audit::record('editar', 'site_noticias', ['video' => null], ['video' => $anterior['video']], 'noticias', (string) $id);

        Flash::success('Vídeo da notícia removido.');
        $this->redirectAdmin('noticias/' . $id . '/editar');
    }

    public function excluir(string $id): void
    {
        $this->guard('site_noticias.excluir');

        $id = (int) $id;
        $anterior = Noticia::buscar($id);
        if ($anterior === null || Noticia::excluir($id) === 0) {
            Flash::error('Notícia não encontrada.');
            $this->redirectAdmin('noticias');
        }

        $this->removerArquivo((string) ($anterior['imagem'] ?? ''));
        $this->removerArquivo((string) ($anterior['video'] ?? ''));
        Audit::record('excluir', 'site_noticias', [], $anterior, 'noticias', (string) $id);

        Flash::success('Notícia excluída.');
        $this->redirectAdmin('noticias');
    }

    private function noticiaPadrao(): array
    {
        return [
            'id'            => null,
            'titulo'        => '',
            'slug'          => '',
            'resumo'        => '',
            'conteudo'      => '',
            'imagem'        => null,
            'video'         => '',
            'publicada_em'  => date('Y-m-d\TH:i'),
            'publicada'     => 1,
            'destaque'      => 0,
        ];
    }

    private function dadosValidados(?array $anterior = null): array
    {
        $titulo = mb_substr(trim((string) $this->request->post('titulo', '')), 0, 180);
        if ($titulo === '') {
            $this->falhar('Informe o título da notícia.', $anterior);
        }

        $slug = mb_substr(trim((string) $this->request->post('slug', '')), 0, 190);
        if ($slug === '') {
            $slug = Noticia::gerarSlug($titulo);
        }
        $slug = Noticia::gerarSlug($slug);
        $idAnterior = $anterior === null ? null : (int) $anterior['id'];
        if (Noticia::slugExiste($slug, $idAnterior)) {
            $slug .= '-' . substr(bin2hex(random_bytes(3)), 0, 6);
        }

        $dataHora = trim((string) $this->request->post('publicada_em', ''));
        $publicadaEm = null;
        if ($dataHora !== '') {
            $data = \DateTime::createFromFormat('!Y-m-d\TH:i', $dataHora);
            if ($data === false || $data->format('Y-m-d\TH:i') !== $dataHora) {
                $this->falhar('Informe uma data de publicação válida.', $anterior);
            }
            $publicadaEm = $data->format('Y-m-d H:i:s');
        }

        $publicada = $this->request->post('publicada', '0') === '1' ? 1 : 0;
        if ($publicada === 1 && $publicadaEm === null) {
            $publicadaEm = date('Y-m-d H:i:s');
        }
        if ($publicada === 0) {
            $publicadaEm = $anterior === null ? null : ($anterior['publicada_em'] ?: null);
        }

        return [
            'titulo'        => $titulo,
            'slug'          => $slug,
            'resumo'        => mb_substr(trim((string) $this->request->post('resumo', '')), 0, 500),
            'conteudo'      => $this->sanitizarHtml((string) $this->request->post('conteudo', '')),
            'video'         => mb_substr(trim((string) $this->request->post('video', '')), 0, 500),
            'autor_id'      => Auth::id(),
            'publicada_em'  => $publicadaEm,
            'publicada'     => $publicada,
            'destaque'      => $this->request->post('destaque', '0') === '1' ? 1 : 0,
        ];
    }

    private function processarImagem(): ?string
    {
        $arquivo = $this->request->file('imagem');
        if ($arquivo === null || (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ((int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || (int) ($arquivo['size'] ?? 0) > self::TAMANHO_IMAGEM) {
            $this->falhar('A imagem deve ser JPG, PNG ou WebP, com até 2 MB.', null);
        }

        $temporario = (string) ($arquivo['tmp_name'] ?? '');
        if ($temporario === '' || !is_uploaded_file($temporario)) {
            $this->falhar('Não foi possível receber a imagem enviada.', null);
        }

        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $info === false ? '' : (string) finfo_file($info, $temporario);
        if ($info !== false) {
            finfo_close($info);
        }

        $extensoes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extensoes[$mime]) || @getimagesize($temporario) === false) {
            $this->falhar('A imagem deve ser JPG, PNG ou WebP, com até 2 MB.', null);
        }

        $pasta = ROOT_PATH . '/public/uploads/noticias';
        $this->garantirPasta($pasta);

        $nome = 'noticia-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensoes[$mime];
        if (!move_uploaded_file($temporario, $pasta . '/' . $nome)) {
            $this->falhar('Não foi possível salvar a imagem da notícia.', null);
        }

        return 'noticias/' . $nome;
    }

    private function processarVideo(string $url): ?string
    {
        $arquivo = $this->request->file('video_arquivo');
        $temArquivo = $arquivo !== null && (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

        if ($temArquivo) {
            if ((int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || (int) ($arquivo['size'] ?? 0) > self::TAMANHO_VIDEO) {
                $this->falhar('O vídeo deve ser MP4 ou WebM, com até 100 MB.', null);
            }

            $temporario = (string) ($arquivo['tmp_name'] ?? '');
            if ($temporario === '' || !is_uploaded_file($temporario)) {
                $this->falhar('Não foi possível receber o vídeo enviado.', null);
            }

            $info = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $info === false ? '' : (string) finfo_file($info, $temporario);
            if ($info !== false) {
                finfo_close($info);
            }

            $extensoes = ['video/mp4' => 'mp4', 'video/webm' => 'webm'];
            if (!isset($extensoes[$mime])) {
                $this->falhar('O vídeo deve ser MP4 ou WebM, com até 100 MB.', null);
            }

            $pasta = ROOT_PATH . '/public/uploads/noticias';
            $this->garantirPasta($pasta);

            $nome = 'video-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensoes[$mime];
            if (!move_uploaded_file($temporario, $pasta . '/' . $nome)) {
                $this->falhar('Não foi possível salvar o vídeo da notícia.', null);
            }

            return 'noticias/' . $nome;
        }

        if ($url === '') {
            return null;
        }

        if (!preg_match('#^https?://[^\s]+$#i', $url)) {
            $this->falhar('Informe uma URL válida para o vídeo (http:// ou https://).', null);
        }

        return $url;
    }

    private function garantirPasta(string $pasta): void
    {
        if (!is_dir($pasta) && !mkdir($pasta, 0755, true) && !is_dir($pasta)) {
            $this->falhar('Não foi possível preparar a pasta de mídias das notícias.', null);
        }
    }

    private function removerArquivo(string $arquivo): void
    {
        if ($arquivo === '' || !str_starts_with($arquivo, 'noticias/') || basename($arquivo) !== substr($arquivo, 9)) {
            return;
        }

        $caminho = ROOT_PATH . '/public/uploads/' . $arquivo;
        if (is_file($caminho)) {
            @unlink($caminho);
        }
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

    private function falhar(string $mensagem, ?array $anterior): never
    {
        Flash::error($mensagem);
        $this->redirectAdmin($anterior === null ? 'noticias' : 'noticias/' . (int) $anterior['id'] . '/editar');
    }
}
