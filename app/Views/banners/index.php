<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-images me-2 text-primary"></i>Banners da Página Principal</h4>
    <?php if (tem_permissao('banners', 'cadastrar')): ?>
        <a href="<?= site_url('banners/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Banner</a>
    <?php endif; ?>
</div>

<p class="text-muted small">
    <i class="fa-solid fa-circle-info me-1"></i>
    As imagens cadastradas aqui aparecem como fundo da área principal do site (hero), em forma de apresentação de slides.
    Recomenda-se imagens na orientação paisagem (16:9), largura mínima de 1600px.
</p>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por título ou subtítulo..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Prévia</th><th>Título</th><th>Ordem</th><th>Situação</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($banners)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhum banner cadastrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($banners as $b): ?>
                <tr>
                    <td>
                        <?php if (! empty($b['imagem']) && is_file(ROOTPATH . 'public/uploads/banners/' . $b['imagem'])): ?>
                            <img src="<?= base_url('uploads/banners/' . $b['imagem']) ?>" alt="<?= esc($b['titulo']) ?>"
                                 style="width:120px;height:60px;object-fit:cover;border-radius:8px;" class="border bg-light">
                        <?php else: ?>
                            <span class="text-muted small">Sem imagem</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="fw-semibold"><?= esc($b['titulo']) ?></div>
                        <?php if (! empty($b['subtitulo'])): ?>
                            <div class="small text-muted text-truncate" style="max-width:340px;"><?= esc($b['subtitulo']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary"><?= (int) $b['ordem'] ?></span></td>
                    <td><?= badge_status($b['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('banners', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('banners/editar/' . $b['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                <form method="post" action="<?= site_url('banners/alternarAtivo/' . $b['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-secondary" title="<?= $b['ativo'] ? 'Desativar' : 'Ativar' ?>">
                                        <i class="fa-solid <?= $b['ativo'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if (tem_permissao('banners', 'excluir')): ?>
                                <form method="post" action="<?= site_url('banners/excluir/' . $b['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o banner <?= esc($b['titulo']) ?>?"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>