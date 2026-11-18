<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Audit;
use App\Core\Flash;
use App\Models\Banner;
use App\Models\Configuracao;

final class SiteController extends AdminController
{
    private const TAMANHO_MAXIMO = 2097152;

    private const PALETA_CORES = [
        '#17324d' => 'Azul profundo',
        '#2b608c' => 'Azul institucional',
        '#516b85' => 'Azul acinzentado',
        '#a97f22' => 'Dourado',
        '#374151' => 'Cinza escuro',
        '#ffffff' => 'Branco',
    ];

    private const TEXTOS_NOTICIAS = [
        'noticias_eyebrow'          => 'Acontecendo no ministério',
        'noticias_titulo'           => 'Notícias',
        'noticias_vazio'            => 'Nenhuma notícia publicada no momento.',
        'noticias_ler'              => 'Ler notícia',
        'noticias_home_eyebrow'     => 'Acontecendo',
        'noticias_home_titulo'      => 'Notícias',
        'noticias_home_botao'       => 'Todas as notícias',
        'noticias_home_vazio'       => 'Nenhuma notícia publicada no momento.',
        'noticias_home_ler'         => 'Ler notícia',
    ];

    public function bannerForm(): void
    {
        $this->guard('site.editar');

        $slides = [];
        foreach (Banner::caminhos() as $caminho) {
            $slides[] = ['caminho' => $caminho, 'url' => url($caminho)];
        }

        $this->renderAdmin('admin.site.banner', [
            'title'     => 'Slides da página inicial',
            'subtitulo' => 'Gerencie as imagens exibidas em carrossel abaixo do menu.',
            'slides'    => $slides,
            'limite'    => Banner::limite(),
            'padrao'    => asset('images/banner.svg'),
        ]);
    }

    public function salvarBanner(): void
    {
        $this->guard('site.editar');

        $arquivo = $this->request->file('banner');
        if ($arquivo === null) {
            $this->falhar('Selecione ao menos uma imagem.');
        }

        $nomes = $arquivo['name'] ?? [];
        if (is_string($nomes)) {
            $nomes = [$nomes];
        }

        $total = count($nomes);
        if ($total === 0) {
            $this->falhar('Selecione ao menos uma imagem.');
        }

        $anteriores = Banner::caminhos();
        $disponiveis = Banner::limite() - count($anteriores);
        if ($disponiveis <= 0) {
            $this->falhar('O limite de ' . Banner::limite() . ' slides foi atingido.');
        }

        $novos = [];
        $erros = [];

        for ($i = 0; $i < $total; $i++) {
            if (count($novos) >= $disponiveis) {
                $erros[] = 'Apenas ' . $disponiveis . ' imagem(ns) foram adicionadas por causa do limite de slides.';
                break;
            }

            $erro = (int) ($arquivo['error'][$i] ?? UPLOAD_ERR_NO_FILE);
            if ($erro === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($erro !== UPLOAD_ERR_OK) {
                $erros[] = 'Falha ao receber o arquivo ' . ($nomes[$i] ?? '') . '.';
                continue;
            }

            if ((int) ($arquivo['size'][$i] ?? 0) > self::TAMANHO_MAXIMO) {
                $erros[] = 'A imagem ' . ($nomes[$i] ?? '') . ' ultrapassa 2 MB.';
                continue;
            }

            $temporario = (string) ($arquivo['tmp_name'][$i] ?? '');
            if ($temporario === '' || !is_uploaded_file($temporario)) {
                $erros[] = 'Envio inválido para ' . ($nomes[$i] ?? '') . '.';
                continue;
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
                $erros[] = 'A imagem ' . ($nomes[$i] ?? '') . ' deve ser JPG, PNG ou WebP.';
                continue;
            }

            $dimensoes = @getimagesize($temporario);
            if ($dimensoes === false || (int) $dimensoes[0] < 1 || (int) $dimensoes[1] < 1) {
                $erros[] = 'A imagem ' . ($nomes[$i] ?? '') . ' não é válida.';
                continue;
            }

            $nome    = 'banner-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensoes[$mime];
            $destino = ROOT_PATH . '/public/uploads/' . $nome;

            if (!move_uploaded_file($temporario, $destino)) {
                $erros[] = 'Não foi possível salvar ' . ($nomes[$i] ?? '') . '.';
                continue;
            }

            $novos[] = 'uploads/' . $nome;
        }

        if ($novos === []) {
            $this->falhar($erros === [] ? 'Nenhuma imagem válida foi enviada.' : implode(' ', $erros));
        }

        Banner::adicionar($novos);

        if ($erros !== []) {
            Flash::warning(implode(' ', $erros));
        }

        Audit::record(
            'editar',
            'site',
            ['slides' => Banner::caminhos()],
            ['slides' => $anteriores],
            'configuracoes',
            'banner_principal'
        );

        Flash::success(count($novos) . ' slide(s) adicionado(s) ao carrossel.');
        $this->redirectAdmin('site');
    }

    public function removerSlide(): void
    {
        $this->guard('site.editar');

        $indice = (int) $this->request->post('indice', -1);
        $anteriores = Banner::caminhos();
        $removido = Banner::removerIndice($indice);

        if ($removido === null) {
            $this->falhar('Slide inválido.');
        }

        $this->removerArquivo($removido);

        Audit::record(
            'editar',
            'site',
            ['slides' => Banner::caminhos()],
            ['slides' => $anteriores],
            'configuracoes',
            'banner_principal'
        );

        Flash::success('Slide removido do carrossel.');
        $this->redirectAdmin('site');
    }

    public function removerTodos(): void
    {
        $this->guard('site.editar');

        $anteriores = Banner::caminhos();
        foreach ($anteriores as $caminho) {
            $this->removerArquivo($caminho);
        }

        Banner::limpar();

        Audit::record(
            'editar',
            'site',
            ['slides' => []],
            ['slides' => $anteriores],
            'configuracoes',
            'banner_principal'
        );

        Flash::success('Slides removidos. A imagem padrão foi restaurada.');
        $this->redirectAdmin('site');
    }

    public function textosForm(): void
    {
        $this->guard('site.editar');

        $this->renderAdmin('admin.site.carrossel-textos', [
            'title'     => 'Textos do carrossel',
            'subtitulo' => 'Edite as palavras exibidas sobre os slides e escolha a cor de cada texto.',
            'paleta'    => self::PALETA_CORES,
            'valores'   => [
                'hero_eyebrow'      => Configuracao::get('hero_eyebrow', 'Ministério Evangélico'),
                'hero_titulo'       => Configuracao::get('hero_titulo', Configuracao::get('ministerio_nome', 'Assembleia de Deus')),
                'hero_texto'        => Configuracao::get('hero_texto', 'Igreja Matriz, congregações e pontos de pregação unidos na fé, na comunhão e no anúncio do evangelho.'),
                'hero_cor_eyebrow'  => $this->corSegura((string) Configuracao::get('hero_cor_eyebrow', ''), '#a97f22'),
                'hero_cor_titulo'   => $this->corSegura((string) Configuracao::get('hero_cor_titulo', ''), '#2b608c'),
                'hero_cor_texto'    => $this->corSegura((string) Configuracao::get('hero_cor_texto', ''), '#516b85'),
            ],
        ]);
    }

    public function salvarTextos(): void
    {
        $this->guard('site.editar');

        $eyebrow = trim((string) $this->request->post('hero_eyebrow', ''));
        $titulo  = trim((string) $this->request->post('hero_titulo', ''));
        $texto   = trim((string) $this->request->post('hero_texto', ''));

        $anterior = [];
        $novo = [
            'hero_eyebrow'     => mb_substr($eyebrow, 0, 120),
            'hero_titulo'      => mb_substr($titulo, 0, 180),
            'hero_texto'       => mb_substr($texto, 0, 500),
            'hero_cor_eyebrow' => $this->corSegura((string) $this->request->post('hero_cor_eyebrow', ''), '#a97f22'),
            'hero_cor_titulo'  => $this->corSegura((string) $this->request->post('hero_cor_titulo', ''), '#2b608c'),
            'hero_cor_texto'   => $this->corSegura((string) $this->request->post('hero_cor_texto', ''), '#516b85'),
        ];

        foreach ($novo as $chave => $valor) {
            $anterior[$chave] = Configuracao::get($chave, '');
            Configuracao::set($chave, $valor);
        }

        Audit::record('editar', 'site', $novo, $anterior, 'configuracoes', 'carrossel_textos');

        Flash::success('Textos e cores do carrossel atualizados.');
        $this->redirectAdmin('site/carrossel-textos');
    }

    public function noticiasTextosForm(): void
    {
        $this->guard('site.editar');

        $this->renderAdmin('admin.site.noticias-textos', [
            'title'     => 'Textos das notícias',
            'subtitulo' => 'Gerencie os textos exibidos na página de notícias e na seção da página inicial.',
            'textos'    => $this->textosNoticias(),
        ]);
    }

    public function salvarNoticiasTextos(): void
    {
        $this->guard('site.editar');

        $novo = [];
        foreach (self::TEXTOS_NOTICIAS as $chave => $padrao) {
            $novo[$chave] = mb_substr(trim((string) $this->request->post($chave, $padrao)), 0, 180);
        }

        $anterior = [];
        foreach ($novo as $chave => $valor) {
            $anterior[$chave] = Configuracao::get($chave, '');
            Configuracao::set($chave, $valor);
        }

        Audit::record('editar', 'site', $novo, $anterior, 'configuracoes', 'noticias_textos');

        Flash::success('Textos das notícias atualizados.');
        $this->redirectAdmin('site/noticias-textos');
    }

    private function textosNoticias(): array
    {
        $textos = [];
        foreach (self::TEXTOS_NOTICIAS as $chave => $padrao) {
            $textos[$chave] = Configuracao::get($chave, $padrao);
        }

        return $textos;
    }

    private function corSegura(string $valor, string $padrao): string
    {
        $valor = strtolower(trim($valor));

        return isset(self::PALETA_CORES[$valor]) ? $valor : $padrao;
    }

    public function historiaForm(): void
    {
        $this->guard('site.editar');

        $imagem = Configuracao::get('historia_imagem', '');

        $this->renderAdmin('admin.site.historia', [
            'title'     => 'História da home',
            'subtitulo' => 'Edite os textos e a imagem da seção História do ministério.',
            'valores'   => [
                'eyebrow' => Configuracao::get('historia_eyebrow', 'Nossa história'),
                'titulo'  => Configuracao::get('historia_titulo', 'O que Deus tem feito entre nós'),
                'texto'   => Configuracao::get('historia_texto', ''),
            ],
            'imagem'    => $imagem,
            'preview'   => $imagem !== '' ? url($imagem) : asset('images/historia.svg'),
        ]);
    }

    public function salvarHistoria(): void
    {
        $this->guard('site.editar');

        $eyebrow = trim((string) $this->request->post('historia_eyebrow', ''));
        $titulo  = trim((string) $this->request->post('historia_titulo', ''));
        $texto   = trim((string) $this->request->post('historia_texto', ''));

        if ($eyebrow === '' || $titulo === '') {
            $this->falhar('Informe o título e o subtítulo da seção.');
        }

        $anterior = [
            'historia_eyebrow' => Configuracao::get('historia_eyebrow', 'Nossa história'),
            'historia_titulo'  => Configuracao::get('historia_titulo', 'O que Deus tem feito entre nós'),
            'historia_texto'   => Configuracao::get('historia_texto', ''),
            'historia_imagem'  => Configuracao::get('historia_imagem', ''),
        ];

        $novaImagem = null;
        $arquivo = $this->request->file('historia_imagem');
        if ($arquivo !== null && (int) ($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $novaImagem = $this->uploadUnico($arquivo, 'historia');
            if ($novaImagem === null) {
                $this->falhar('A imagem deve ser JPG, PNG ou WebP, com até 2 MB.');
            }
        }

        $novo = [
            'historia_eyebrow' => mb_substr($eyebrow, 0, 120),
            'historia_titulo'  => mb_substr($titulo, 0, 180),
            'historia_texto'   => $texto,
            'historia_imagem'  => $novaImagem ?? $anterior['historia_imagem'],
        ];

        foreach ($novo as $chave => $valor) {
            Configuracao::set($chave, $valor);
        }

        if ($novaImagem !== null) {
            $this->removerArquivo($anterior['historia_imagem']);
        }

        Audit::record('editar', 'site', $novo, $anterior, 'configuracoes', 'historia_home');

        Flash::success('Seção História da home atualizada.');
        $this->redirectAdmin('site/historia');
    }

    public function removerImagemHistoria(): void
    {
        $this->guard('site.editar');

        $anterior = Configuracao::get('historia_imagem', '');
        $this->removerArquivo($anterior);
        Configuracao::set('historia_imagem', '');

        Audit::record(
            'editar',
            'site',
            ['historia_imagem' => ''],
            ['historia_imagem' => $anterior],
            'configuracoes',
            'historia_imagem'
        );

        Flash::success('Imagem removida. A ilustração padrão foi restaurada.');
        $this->redirectAdmin('site/historia');
    }

    private function uploadUnico(array $arquivo, string $prefixo): ?string
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

        $arquivo = ROOT_PATH . '/public/' . $caminho;
        if (is_file($arquivo)) {
            @unlink($arquivo);
        }
    }

    private function falhar(string $mensagem): never
    {
        Flash::error($mensagem);
        $this->redirectAdmin('site');
    }
}
