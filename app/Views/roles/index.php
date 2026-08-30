<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-shield-halved me-2 text-primary"></i>Perfis & Permissões</h4>
    <?php if (tem_permissao('roles', 'cadastrar')): ?>
        <a href="<?= site_url('roles/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Perfil</a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Nome</th><th>Identificador</th><th>Descrição</th><th>Situação</th><th class="text-end">Ações</th></tr>
            </thead>
            <tbody>
            <?php foreach ($roles as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td class="fw-semibold">
                        <?= esc($r['nome']) ?>
                        <?php if ($r['super_admin']): ?><span class="badge bg-danger ms-1">Super Admin</span><?php endif; ?>
                    </td>
                    <td><code><?= esc($r['slug']) ?></code></td>
                    <td><?= esc($r['descricao'] ?: '-') ?></td>
                    <td><?= badge_status(!$r['deleted_at']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('roles', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('roles/editar/' . $r['id']) ?>"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('roles', 'excluir') && ! $r['super_admin']): ?>
                                <form method="post" action="<?= site_url('roles/excluir/' . $r['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o perfil <?= esc($r['nome']) ?>?"><i class="fa-solid fa-trash"></i></button>
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
