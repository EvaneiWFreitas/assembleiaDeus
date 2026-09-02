<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-calendar-days me-2 text-primary"></i>Agenda</h4>
    <?php if (tem_permissao('agenda', 'cadastrar')): ?>
        <a href="<?= site_url('agenda/novo') ?>" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Novo Evento</a>
    <?php endif; ?>
</div>

<form class="row g-2 mb-3" method="get">
    <div class="col-md-3">
        <input type="text" name="q" class="form-control" placeholder="Pesquisar por título, responsável..." value="<?= esc($busca ?? '') ?>">
    </div>
    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Todos os status</option>
            <?php foreach (['Pendente', 'Confirmado', 'Cancelado', 'Concluído'] as $s): ?>
                <option value="<?= $s ?>" <?= ($statusFiltro ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="tipo" class="form-select">
            <option value="">Todos os tipos</option>
            <?php foreach (['Reunião', 'Culto', 'Evento', 'Retiro', 'Encontro', 'Outro'] as $t): ?>
                <option value="<?= $t ?>" <?= ($tipoFiltro ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="congregacao" class="form-select">
            <option value="">Todas as congregações</option>
            <?php foreach ($congregacoes as $id => $nome): ?>
                <option value="<?= $id ?>" <?= ($congregacaoFiltro ?? 0) == $id ? 'selected' : '' ?>><?= esc($nome) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-magnifying-glass"></i></button></div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Local</th>
                    <th>Tipo</th>
                    <th>Responsável</th>
                    <th>Congregação</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($eventos)): ?>
                <tr><td colspan="10" class="text-center text-muted py-4">Nenhum evento encontrado.</td></tr>
            <?php endif; ?>
            <?php foreach ($eventos as $e): ?>
                <tr>
                    <td><?= $e['id'] ?></td>
                    <td class="fw-semibold">
                        <span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:<?= esc($e['cor'] ?? '#0d6efd') ?>;"></span>
                        <?= esc($e['titulo']) ?>
                    </td>
                    <td><?= formatar_data($e['data_inicio']) ?><?= !empty($e['data_fim']) && $e['data_fim'] !== $e['data_inicio'] ? ' — ' . formatar_data($e['data_fim']) : '' ?></td>
                    <td><?= $e['hora_inicio'] ? esc(substr($e['hora_inicio'], 0, 5)) . ($e['hora_fim'] ? ' — ' . esc(substr($e['hora_fim'], 0, 5)) : '') : '-' ?></td>
                    <td><?= esc($e['local'] ?: '-') ?></td>
                    <td><span class="badge bg-info-subtle text-info"><?= esc($e['tipo']) ?></span></td>
                    <td><?= esc($e['responsavel'] ?: '-') ?></td>
                    <td><?= esc($e['congregacao'] ?? '-') ?></td>
                    <td>
                        <?php $cores = ['Pendente' => 'warning', 'Confirmado' => 'success', 'Cancelado' => 'danger', 'Concluído' => 'secondary']; ?>
                        <span class="badge bg-<?= $cores[$e['status']] ?? 'secondary' ?>-subtle text-<?= $cores[$e['status']] ?? 'secondary' ?>"><?= esc($e['status']) ?></span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (tem_permissao('agenda', 'visualizar')): ?>
                                <a class="btn btn-outline-success" href="<?= site_url('agenda/presencas/' . $e['id']) ?>" title="Presenças"><i class="fa-solid fa-user-check"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('agenda', 'editar')): ?>
                                <a class="btn btn-outline-primary" href="<?= site_url('agenda/editar/' . $e['id']) ?>" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <?php endif; ?>
                            <?php if (tem_permissao('agenda', 'excluir')): ?>
                                <form method="post" action="<?= site_url('agenda/excluir/' . $e['id']) ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger" data-confirmar="Excluir o evento <?= esc($e['titulo']) ?>?" title="Excluir"><i class="fa-solid fa-trash"></i></button>
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
                <a class="page-link" href="<?= site_url('agenda') ?>?page=<?= $i ?><?= $busca ? '&q=' . urlencode($busca) : '' ?><?= $statusFiltro ? '&status=' . urlencode($statusFiltro) : '' ?><?= $tipoFiltro ? '&tipo=' . urlencode($tipoFiltro) : '' ?><?= $congregacaoFiltro ? '&congregacao=' . $congregacaoFiltro : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?= $this->endSection() ?>