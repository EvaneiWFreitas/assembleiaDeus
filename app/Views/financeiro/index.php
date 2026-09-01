<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-coins me-2 text-success"></i>Painel Financeiro</h4>
</div>

<!-- Filtro de mês -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label mb-0 small">Mês/Ano</label>
                <input type="month" name="mes" class="form-control form-control-sm" value="<?= esc($mes) ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="fa-solid fa-filter me-1"></i>Filtrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Cards de resumo -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body">
                <div class="text-muted small">Dízimos</div>
                <div class="fs-4 fw-bold text-success"><?= formatar_moeda($resumo['dizimos']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body">
                <div class="text-muted small">Ofertas</div>
                <div class="fs-4 fw-bold text-primary"><?= formatar_moeda($resumo['ofertas']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm border-start border-info border-4">
            <div class="card-body">
                <div class="text-muted small">Receitas</div>
                <div class="fs-4 fw-bold text-info"><?= formatar_moeda($resumo['receitas']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm border-start border-danger border-4">
            <div class="card-body">
                <div class="text-muted small">Despesas</div>
                <div class="fs-4 fw-bold text-danger"><?= formatar_moeda($resumo['despesas']) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Saldo -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body text-center">
        <div class="text-muted small">Saldo do Mês</div>
        <div class="fs-3 fw-bold <?= ($resumo['saldo'] >= 0) ? 'text-success' : 'text-danger' ?>">
            <?= formatar_moeda($resumo['saldo']) ?>
        </div>
    </div>
</div>

<!-- Últimos lançamentos -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-light fw-semibold">
        <i class="fa-solid fa-clock-rotate-left me-1"></i>Últimos Lançamentos
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th class="text-end">Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lancamentos)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Nenhum lançamento registrado.</td></tr>
                <?php endif; ?>
                <?php foreach ($lancamentos as $l): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($l['data'])) ?></td>
                        <td>
                            <?php
                            $badge = match($l['tipo'] ?? '') {
                                'dizimo'  => 'bg-success-subtle text-success',
                                'oferta'  => 'bg-primary-subtle text-primary',
                                'receita' => 'bg-info-subtle text-info',
                                'despesa' => 'bg-danger-subtle text-danger',
                                default   => 'bg-secondary-subtle text-secondary',
                            };
                            $rotulo = match($l['tipo'] ?? '') {
                                'dizimo'  => 'Dízimo',
                                'oferta'  => 'Oferta',
                                'receita' => 'Receita',
                                'despesa' => 'Despesa',
                                default   => $l['tipo'] ?? '-',
                            };
                            ?>
                            <span class="badge <?= $badge ?>"><?= $rotulo ?></span>
                        </td>
                        <td><?= esc($l['descricao']) ?></td>
                        <td class="text-end fw-semibold">
                            <?php if (($l['tipo'] ?? '') === 'despesa'): ?>
                                <span class="text-danger">-<?= formatar_moeda($l['valor']) ?></span>
                            <?php else: ?>
                                <span class="text-success">+<?= formatar_moeda($l['valor']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Botões de acesso rápido -->
<div class="row g-2 mt-3">
    <div class="col-auto">
        <a href="<?= site_url('dizimos') ?>" class="btn btn-outline-success btn-sm">
            <i class="fa-solid fa-hand-holding-dollar me-1"></i>Dízimos
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= site_url('ofertas') ?>" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-box-open me-1"></i>Ofertas
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= site_url('despesas') ?>" class="btn btn-outline-danger btn-sm">
            <i class="fa-solid fa-money-bill-wave me-1"></i>Despesas
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= site_url('receitas') ?>" class="btn btn-outline-info btn-sm">
            <i class="fa-solid fa-money-check-dollar me-1"></i>Receitas
        </a>
    </div>
    <div class="col-auto">
        <a href="<?= site_url('categorias') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-tags me-1"></i>Categorias
        </a>
    </div>
</div>

<?= $this->endSection() ?>
