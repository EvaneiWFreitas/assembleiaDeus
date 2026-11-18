<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0"><?= e($subtitulo) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= e(url('admin/site/noticias-textos')) ?>" class="btn btn-sm btn-ad-outline">
            <i class="fas fa-pen-nib me-1"></i>Textos das notícias
        </a>
        <a href="<?= e(url('noticias')) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-ad-outline">
            <i class="fas fa-arrow-up-right-from-square me-1"></i>Ver no site
        </a>
        <?php if ($podeCriar): ?>
            <a href="<?= e(url('admin/noticias/novo')) ?>" class="btn btn-sm btn-ad-primary">
                <i class="fas fa-plus me-1"></i>Nova notícia
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 admin-card-titulo mb-0">Notícias cadastradas</h2>
            <span class="badge ad-badge"><?= count($noticias) ?> notícia(s)</span>
        </div>

        <?php if ($noticias === []): ?>
            <div class="admin-vazio text-center py-5">
                <i class="fas fa-newspaper fa-2x mb-3 d-block"></i>
                Nenhuma notícia cadastrada.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-tabela align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Notícia</th>
                            <th>Publicação</th>
                            <th>Mídia</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($noticias as $noticia): ?>
                            <?php
                            $dataPublicacao = !empty($noticia['publicada_em'])
                                ? date('d/m/Y H:i', strtotime((string) $noticia['publicada_em']))
                                : '-';
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($noticia['imagem'])): ?>
                                            <img src="<?= e(url('uploads/' . $noticia['imagem'])) ?>" alt="" class="admin-evento-thumb">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= e($noticia['titulo']) ?></strong>
                                            <div class="small text-muted"><?= e($noticia['slug']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= (int) $noticia['publicada'] === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                        <?= (int) $noticia['publicada'] === 1 ? 'Publicada' : 'Rascunho' ?>
                                    </span>
                                    <?php if ((int) $noticia['destaque'] === 1): ?>
                                        <span class="badge text-bg-warning">Destaque</span>
                                    <?php endif; ?>
                                    <div class="small text-muted mt-1"><?= e($dataPublicacao) ?></div>
                                </td>
                                <td>
                                    <i class="fas fa-image <?= !empty($noticia['imagem']) ? 'text-success' : 'text-muted' ?>" title="Imagem"></i>
                                    <i class="fas fa-video <?= !empty($noticia['video']) ? 'text-success' : 'text-muted' ?>" title="Vídeo"></i>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php if ((int) $noticia['publicada'] === 1): ?>
                                            <a href="<?= e(url('noticias/' . $noticia['slug'])) ?>" target="_blank" rel="noopener noreferrer"
                                               class="btn btn-sm btn-ad-outline" title="Ver no site">
                                                <i class="fas fa-arrow-up-right-from-square"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($podeEditar): ?>
                                            <a href="<?= e(url('admin/noticias/' . (int) $noticia['id'] . '/editar')) ?>"
                                               class="btn btn-sm btn-ad-outline" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($podeExcluir): ?>
                                            <form method="post" action="<?= e(url('admin/noticias/' . (int) $noticia['id'] . '/excluir')) ?>"
                                                  onsubmit="return confirm('Excluir esta notícia?');">
                                                <?= App\Core\Csrf::field() ?>
                                                <button type="submit" class="btn btn-sm btn-ad-outline text-danger" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
