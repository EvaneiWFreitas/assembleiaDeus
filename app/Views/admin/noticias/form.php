<?php
$valor = static fn (string $campo, mixed $padrao = ''): string => (string) old($campo, $noticia[$campo] ?? $padrao);
$idNoticia = (int) ($noticia['id'] ?? 0);
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <a href="<?= e(url('admin/noticias')) ?>" class="btn btn-sm btn-ad-outline">
        <i class="fas fa-arrow-left me-1"></i>Voltar
    </a>
</div>

<form method="post" action="<?= e($acao) ?>" enctype="multipart/form-data" class="admin-form">
    <?= App\Core\Csrf::field() ?>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3">Conteúdo da notícia</h2>
            <div class="row g-3">
                <div class="col-lg-9">
                    <label for="campo_titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="campo_titulo" name="titulo"
                           value="<?= e($valor('titulo')) ?>" placeholder="Ex.: Nova programação de cultos" required>
                </div>
                <div class="col-lg-3">
                    <label for="campo_slug" class="form-label">Endereço (slug)</label>
                    <input type="text" class="form-control" id="campo_slug" name="slug"
                           value="<?= e($valor('slug')) ?>" placeholder="Gerado automaticamente">
                    <div class="form-text">Deixe vazio para gerar pelo título.</div>
                </div>
                <div class="col-12">
                    <label for="campo_resumo" class="form-label">Resumo</label>
                    <textarea class="form-control" id="campo_resumo" name="resumo" rows="2" maxlength="500"><?= e($valor('resumo')) ?></textarea>
                    <div class="form-text">Aparece nos cards da página inicial e da lista de notícias.</div>
                </div>
                <div class="col-12">
                    <label for="campo_conteudo" class="form-label">Conteúdo</label>
                    <textarea class="form-control" id="campo_conteudo" name="conteudo" rows="10"><?= e($valor('conteudo')) ?></textarea>
                    <div class="form-text">Html simples permitido: parágrafos, títulos, listas, links e imagens.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3"><i class="fas fa-photo-video me-2"></i>Imagem e vídeo</h2>
            <div class="row g-4">
                <div class="col-lg-6">
                    <label for="campo_imagem" class="form-label">Imagem</label>
                    <input type="file" class="form-control" id="campo_imagem" name="imagem" accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPG, PNG ou WebP, até 2 MB. Deixe vazio para manter a imagem atual.</div>
                    <?php if (!empty($noticia['imagem'])): ?>
                        <div class="mt-3">
                            <img src="<?= e(url('uploads/' . $noticia['imagem'])) ?>" alt="Imagem atual da notícia" class="admin-upload-preview">
                            <button type="submit" form="form-remover-imagem-<?= $idNoticia ?>"
                                    class="btn btn-sm btn-ad-outline text-danger mt-2 d-block"
                                    onclick="return confirm('Remover a imagem desta notícia?');">
                                <i class="fas fa-trash me-1"></i>Remover imagem
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <label for="campo_video" class="form-label">Vídeo (URL)</label>
                    <input type="url" class="form-control" id="campo_video" name="video"
                           value="<?= e($valor('video')) ?>" placeholder="https://www.youtube.com/watch?v=...">
                    <div class="form-text">Cole o link do YouTube, Vimeo ou de um arquivo de vídeo online.</div>

                    <label for="campo_video_arquivo" class="form-label mt-3">Ou envie um vídeo</label>
                    <input type="file" class="form-control" id="campo_video_arquivo" name="video_arquivo" accept="video/mp4,video/webm">
                    <div class="form-text">MP4 ou WebM, até 100 MB. O arquivo enviado tem prioridade sobre a URL.</div>

                    <?php if (!empty($noticia['video'])): ?>
                        <div class="mt-3">
                            <i class="fas fa-video me-1"></i>
                            <span class="small text-break"><?= e($noticia['video']) ?></span>
                            <button type="submit" form="form-remover-video-<?= $idNoticia ?>"
                                    class="btn btn-sm btn-ad-outline text-danger mt-2 d-block"
                                    onclick="return confirm('Remover o vídeo desta notícia?');">
                                <i class="fas fa-trash me-1"></i>Remover vídeo
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h2 class="h6 admin-card-titulo mb-3">Publicação</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="campo_publicada_em" class="form-label">Data da publicação</label>
                    <input type="datetime-local" class="form-control" id="campo_publicada_em" name="publicada_em"
                           value="<?= e($valor('publicada_em')) ?>">
                </div>
                <div class="col-md-8 d-flex align-items-end flex-wrap gap-4 pb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="campo_publicada"
                               name="publicada" value="1" <?= $valor('publicada', '1') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="campo_publicada">Publicar no site</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="campo_destaque"
                               name="destaque" value="1" <?= $valor('destaque', '0') === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="campo_destaque">Destacar na página inicial</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-end gap-2">
        <a href="<?= e(url('admin/noticias')) ?>" class="btn btn-ad-outline">Cancelar</a>
        <button type="submit" class="btn btn-ad-primary">
            <i class="fas fa-check me-1"></i>Salvar notícia
        </button>
    </div>
</form>

<?php if ($idNoticia > 0 && !empty($noticia['imagem'])): ?>
    <form id="form-remover-imagem-<?= $idNoticia ?>" method="post"
          action="<?= e(url('admin/noticias/' . $idNoticia . '/imagem/remover')) ?>" class="d-none">
        <?= App\Core\Csrf::field() ?>
    </form>
<?php endif; ?>
<?php if ($idNoticia > 0 && !empty($noticia['video'])): ?>
    <form id="form-remover-video-<?= $idNoticia ?>" method="post"
          action="<?= e(url('admin/noticias/' . $idNoticia . '/video/remover')) ?>" class="d-none">
        <?= App\Core\Csrf::field() ?>
    </form>
<?php endif; ?>
