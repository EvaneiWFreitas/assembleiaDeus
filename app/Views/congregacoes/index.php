<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-place-of-worship me-2 text-primary"></i>Congregações</h4>
    <?php if (tem_permissao('congregacoes', 'cadastrar')): ?>
        <a href="<?= site_url('congregacoes/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Nova Congregação</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por nome ou código..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Código</th><th>Nome</th><th>Cidade</th><th>Telefone</th><th>Membros Ativos</th><th>Abertura</th><th>Situação</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php if (empty($congregacoes)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhuma congregação cadastrada.</td></tr>
            <?php endif; ?>
            <?php foreach ($congregacoes as $c): ?>
                <tr>
                    <td><code><?= esc($c['codigo']) ?></code></td>
                    <td class="fw-semibold"><?= esc($c['nome']) ?></td>
                    <td><?= esc(($c['cidade'] ?: '-') . ($c['estado'] ? '/' . $c['estado'] : '')) ?></td>
                    <td><?= esc($c['telefone'] ?: '-') ?></td>
                    <td><span class="badge bg-primary-subtle text-primary"><?= (int) $c['total_membros'] ?></span></td>
                    <td><?= formatar_data($c['data_abertura']) ?></td>
                    <td><?= badge_status($c['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('congregacoes', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('congregacoes/editar/' . $c['id']) ?>"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('congregacoes', 'excluir')): ?>
                                <form method="post" action="<?= site_url('congregacoes/excluir/' . $c['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir a congregação <?= esc($c['nome']) ?>?"><i class="fa-solid fa-trash"></i></button>
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
