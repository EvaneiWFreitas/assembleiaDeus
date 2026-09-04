<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-file-lines me-2 text-primary"></i>Modelos de Documentos</h4>
    <?php if (tem_permissao('documentos', 'cadastrar')): ?>
        <a href="<?= site_url('documentos/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Modelo</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Variáveis</th>
                    <th>Padrão</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($modelos)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum modelo de documento cadastrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($modelos as $m): ?>
                <tr>
                    <td class="fw-semibold"><?= esc($m['titulo']) ?></td>
                    <td>
                        <span class="badge bg-info-subtle text-info"><?= esc($tipos[$m['tipo']] ?? $m['tipo']) ?></span>
                    </td>
                    <td>
                        <?php 
                        $vars = json_decode($m['variaveis'] ?? '[]', true);
                        if (!empty($vars)): ?>
                            <span class="badge bg-secondary-subtle text-secondary">
                                <i class="fa-solid fa-code me-1"></i><?= count($vars) ?> variável(is)
                            </span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ((int)$m['padrao'] === 1): ?>
                            <span class="badge bg-success-subtle text-success"><i class="fa-solid fa-star me-1"></i>Sim</span>
                        <?php else: ?>
                            <span class="text-muted">Não</span>
                        <?php endif; ?>
                    </td>
                    <td><?= badge_status($m['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-outline-primary" href="<?= site_url('documentos/visualizar/' . $m['id']) ?>" title="Visualizar"><i class="fa-solid fa-eye"></i></a>
                            <a class="btn btn-outline-success" href="<?= site_url('documentos/imprimir/' . $m['id']) ?>" title="Imprimir" target="_blank"><i class="fa-solid fa-print"></i></a>
                            <?php if (tem_permissao('documentos', 'editar')): ?>
                                <a class="btn btn-outline-warning" href="<?= site_url('documentos/editar/' . $m['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('documentos', 'excluir')): ?>
                                <form method="post" action="<?= site_url('documentos/excluir/' . $m['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o modelo <?= esc($m['titulo']) ?>?"><i class="fa-solid fa-trash"></i></button>
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