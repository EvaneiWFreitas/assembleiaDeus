<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-chart-bar me-2 text-primary"></i>Relatórios</h4>
    <?php $exportar = in_array($tipo, ['financeiro', 'membros', 'agenda'], true); ?>
    <?php if ($exportar && tem_permissao('relatorios', 'exportar')): ?>
        <a class="btn btn-success" href="<?= site_url('relatorios/exportar') ?>?tipo=<?= $tipo ?>&de=<?= urlencode($mesInicio) ?>&ate=<?= urlencode($mesFim) ?>">
            <i class="fa-solid fa-file-csv me-1"></i>Exportar CSV
        </a>
    <?php endif; ?>
</div>

<div class="mb-4">
    <a href="<?= site_url('relatorios') ?>" class="btn btn-sm <?= $tipo === 'resumo' ? 'btn-primary' : 'btn-outline-primary' ?>">Visão Geral</a>
    <a href="<?= site_url('relatorios') ?>?tipo=financeiro" class="btn btn-sm <?= $tipo === 'financeiro' ? 'btn-primary' : 'btn-outline-primary' ?>">Financeiro</a>
    <a href="<?= site_url('relatorios') ?>?tipo=membros" class="btn btn-sm <?= $tipo === 'membros' ? 'btn-primary' : 'btn-outline-primary' ?>">Membros</a>
    <a href="<?= site_url('relatorios') ?>?tipo=agenda" class="btn btn-sm <?= $tipo === 'agenda' ? 'btn-primary' : 'btn-outline-primary' ?>">Agenda</a>
</div>

<?php if ($tipo === 'financeiro'): ?>
<form class="row g-2 mb-3" method="get">
    <input type="hidden" name="tipo" value="financeiro">
    <div class="col-auto">
        <label class="visually-hidden">De</label>
        <input type="month" name="de" class="form-control" value="<?= esc($mesInicio) ?>">
    </div>
    <div class="col-auto"><span class="form-control-plaintext">até</span></div>
    <div class="col-auto">
        <label class="visually-hidden">Até</label>
        <input type="month" name="ate" class="form-control" value="<?= esc($mesFim) ?>">
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary"><i class="fa-solid fa-filter"></i> Filtrar</button></div>
</form>
<?php endif; ?>

<?php if ($tipo === 'resumo'): ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-metrica"><div class="card-body">
                <span class="text-muted small">Entradas (mês)</span>
                <div class="valor fs-5"><?= formatar_moeda($financeiro['entradas']) ?></div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-metrica" style="border-left-color:#dc3545;"><div class="card-body">
                <span class="text-muted small">Despesas (mês)</span>
                <div class="valor fs-5"><?= formatar_moeda($financeiro['despesas']) ?></div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-metrica" style="border-left-color:#198754;"><div class="card-body">
                <span class="text-muted small">Saldo (mês)</span>
                <div class="valor fs-5"><?= formatar_moeda($financeiro['saldo']) ?></div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-metrica" style="border-left-color:#6f42c1;"><div class="card-body">
                <span class="text-muted small">Eventos</span>
                <div class="valor fs-5"><?= (int) $contagens['eventos'] ?></div>
            </div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold"><i class="fa-solid fa-database me-2 text-primary"></i>Cadastros do Sistema</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <tbody>
                    <?php
                        $itens = [
                            'Membros' => $contagens['membros'],
                            'Visitantes' => $contagens['visitantes'],
                            'Congregações' => $contagens['congregacoes'],
                            'Pastores & Obreiros' => $contagens['obreiros'],
                            'Ministérios' => $contagens['ministerios'],
                            'Departamentos' => $contagens['departamentos'],
                            'Células' => $contagens['celulas'],
                            'Discipulados' => $contagens['discipulados'],
                            'Cursos' => $contagens['cursos'],
                            'Eventos (Agenda)' => $contagens['eventos'],
                        ];
                    ?>
                    <?php foreach ($itens as $rotulo => $valor): ?>
                        <tr>
                            <td class="fw-semibold"><?= $rotulo ?></td>
                            <td class="text-end"><span class="badge bg-primary-subtle text-primary fs-6"><?= $valor ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if ($tipo === 'financeiro'): ?>
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <div class="card card-metrica"><div class="card-body"><span class="text-muted small">Dízimos</span><div class="valor"><?= formatar_moeda($totalDizimos) ?></div></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-metrica"><div class="card-body"><span class="text-muted small">Ofertas</span><div class="valor"><?= formatar_moeda($totalOfertas) ?></div></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-metrica"><div class="card-body"><span class="text-muted small">Receitas</span><div class="valor"><?= formatar_moeda($totalReceitas) ?></div></div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-metrica" style="border-left-color:#dc3545;"><div class="card-body"><span class="text-muted small">Despesas</span><div class="valor"><?= formatar_moeda($totalDespesas) ?></div></div></div>
        </div>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between">
                    <div><strong>Saldo do período</strong></div>
                    <strong class="<?= $totais['saldo'] >= 0 ? 'text-success' : 'text-danger' ?>"><?= formatar_moeda($totais['saldo']) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold"><i class="fa-solid fa-hand-holding-dollar me-2 text-primary"></i>Dízimos</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Data</th><th>Membro</th><th class="text-end">Valor</th></tr></thead>
                        <tbody>
                        <?php if (empty($dizimos)): ?><tr><td colspan="3" class="text-center text-muted py-3">Sem registros.</td></tr><?php endif; ?>
                        <?php foreach ($dizimos as $l): ?>
                            <tr><td><?= formatar_data($l['data']) ?></td><td><?= esc($l['membro'] ?? '-') ?></td><td class="text-end"><?= formatar_moeda($l['valor']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold"><i class="fa-solid fa-box-open me-2 text-primary"></i>Ofertas</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Data</th><th>Categoria</th><th class="text-end">Valor</th></tr></thead>
                        <tbody>
                        <?php if (empty($ofertas)): ?><tr><td colspan="3" class="text-center text-muted py-3">Sem registros.</td></tr><?php endif; ?>
                        <?php foreach ($ofertas as $l): ?>
                            <tr><td><?= formatar_data($l['data']) ?></td><td><?= esc($l['categoria'] ?? 'Geral') ?></td><td class="text-end"><?= formatar_moeda($l['valor']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold"><i class="fa-solid fa-money-check-dollar me-2 text-primary"></i>Receitas</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Data</th><th>Descrição</th><th class="text-end">Valor</th></tr></thead>
                        <tbody>
                        <?php if (empty($receitas)): ?><tr><td colspan="3" class="text-center text-muted py-3">Sem registros.</td></tr><?php endif; ?>
                        <?php foreach ($receitas as $l): ?>
                            <tr><td><?= formatar_data($l['data']) ?></td><td><?= esc($l['descricao']) ?></td><td class="text-end"><?= formatar_moeda($l['valor']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light fw-semibold"><i class="fa-solid fa-money-bill-wave me-2 text-danger"></i>Despesas</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Data</th><th>Descrição</th><th class="text-end">Valor</th></tr></thead>
                        <tbody>
                        <?php if (empty($despesas)): ?><tr><td colspan="3" class="text-center text-muted py-3">Sem registros.</td></tr><?php endif; ?>
                        <?php foreach ($despesas as $l): ?>
                            <tr><td><?= formatar_data($l['data']) ?></td><td><?= esc($l['descricao']) ?></td><td class="text-end"><?= formatar_moeda($l['valor']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($tipo === 'membros'): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold"><i class="fa-solid fa-people-group me-2 text-primary"></i>Membros (<?= count($membros) ?>)</div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light"><tr><th>#</th><th>Nome</th><th>CPF</th><th>Telefone</th><th>Congregação</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (empty($membros)): ?><tr><td colspan="6" class="text-center text-muted py-4">Nenhum membro encontrado.</td></tr><?php endif; ?>
                <?php foreach ($membros as $m): ?>
                    <tr>
                        <td><?= $m['id'] ?></td>
                        <td class="fw-semibold"><?= esc($m['nome']) ?></td>
                        <td><?= esc($m['cpf'] ?: '-') ?></td>
                        <td><?= esc($m['telefone'] ?: '-') ?></td>
                        <td><?= esc($m['congregacao'] ?? '-') ?></td>
                        <td>
                            <?php $cores = ['Ativo' => 'success', 'Inativo' => 'secondary', 'Transferido' => 'warning', 'Desligado' => 'danger', 'Falecido' => 'dark']; ?>
                            <span class="badge bg-<?= $cores[$m['status']] ?? 'secondary' ?>-subtle text-<?= $cores[$m['status']] ?? 'secondary' ?>"><?= esc($m['status']) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if ($tipo === 'agenda'): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold"><i class="fa-solid fa-calendar-days me-2 text-primary"></i>Agenda de Eventos (<?= count($agenda) ?>)</div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light"><tr><th>Título</th><th>Data</th><th>Horário</th><th>Local</th><th>Tipo</th><th>Status</th><th class="text-end">Presenças</th></tr></thead>
                <tbody>
                <?php if (empty($agenda)): ?><tr><td colspan="7" class="text-center text-muted py-4">Nenhum evento encontrado.</td></tr><?php endif; ?>
                <?php foreach ($agenda as $ev): ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($ev['titulo']) ?></td>
                        <td><?= formatar_data($ev['data_inicio']) ?></td>
                        <td><?= $ev['hora_inicio'] ? esc(substr($ev['hora_inicio'], 0, 5)) : '-' ?></td>
                        <td><?= esc($ev['local'] ?: '-') ?></td>
                        <td><span class="badge bg-info-subtle text-info"><?= esc($ev['tipo']) ?></span></td>
                        <td>
                            <?php $cores = ['Pendente' => 'warning', 'Confirmado' => 'success', 'Cancelado' => 'danger', 'Concluído' => 'secondary']; ?>
                            <span class="badge bg-<?= $cores[$ev['status']] ?? 'secondary' ?>-subtle text-<?= $cores[$ev['status']] ?? 'secondary' ?>"><?= esc($ev['status']) ?></span>
                        </td>
                        <td class="text-end"><span class="badge bg-success-subtle text-success"><?= (int) $ev['presencas'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
