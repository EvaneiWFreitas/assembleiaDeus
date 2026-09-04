<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-user-tie me-2 text-primary"></i>Diretoria da Igreja</h4>
    <?php if (tem_permissao('diretorias', 'cadastrar')): ?>
        <a href="<?= site_url('diretorias/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Membro</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-6">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por nome, cargo ou e-mail..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:50px"></th>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Contato</th>
                    <th class="text-center">Ordem</th>
                    <th class="text-center">Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($diretorias)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhum membro da diretoria cadastrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($diretorias as $d): ?>
                <tr>
                    <td>
                        <?php if (! empty($d['foto']) && is_file(ROOTPATH . 'public/uploads/diretorias/' . $d['foto'])): ?>
                            <img src="<?= base_url('uploads/diretorias/' . $d['foto']) ?>" alt="<?= esc($d['nome']) ?>" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;font-size:.9rem;"><?= mb_strimwidth(esc($d['nome']), 0, 2, '', 'UTF-8') ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="fw-semibold"><?= esc($d['nome']) ?></td>
                    <td><span class="badge bg-primary-subtle text-primary"><?= esc($d['cargo']) ?></span></td>
                    <td>
                        <?php if (! empty($d['whatsapp'])): ?>
                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $d['whatsapp']) ?>" target="_blank" class="text-decoration-none" title="WhatsApp">
                                <i class="fa-brands fa-whatsapp text-success me-1"></i><?= esc($d['whatsapp']) ?>
                            </a><br>
                        <?php endif; ?>
                        <?php if (! empty($d['email'])): ?>
                            <a href="mailto:<?= esc($d['email']) ?>" class="text-decoration-none text-muted small">
                                <i class="fa-solid fa-envelope me-1"></i><?= esc($d['email']) ?>
                            </a>
                        <?php endif; ?>
                        <?php if (empty($d['whatsapp']) && empty($d['email'])): ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= (int) $d['ordem'] ?></td>
                    <td class="text-center"><?= badge_status($d['ativo']) ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('diretorias', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('diretorias/editar/' . $d['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('diretorias', 'excluir')): ?>
                                <form method="post" action="<?= site_url('diretorias/excluir/' . $d['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir <?= esc($d['nome']) ?> da diretoria?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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

<?php if ($total > $porPagina): ?>
<nav class="mt-3">
    <ul class="pagination">
        <?php for ($i = 1; $i <= (int) ceil($total / $porPagina); $i++): ?>
            <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                <a class="page-link" href="<?= site_url('diretorias') ?>?page=<?= $i ?><?= ($busca ?? '') ? '&q=' . urlencode($busca) : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>
