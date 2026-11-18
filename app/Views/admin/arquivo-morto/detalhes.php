<?php
$rotulosTipo = [
    'NOMEACAO'        => 'Nomeação',
    'MUDANCA_CARGO'   => 'Mudança de cargo',
    'TRANSFERENCIA'   => 'Transferência',
    'AFASTAMENTO'     => 'Afastamento',
    'RETORNO'         => 'Retorno',
    'CONSAGRACAO'     => 'Consagração',
    'DESLIGAMENTO'    => 'Desligamento',
    'OUTRO'           => 'Outro',
];
$campos = [
    'codigo' => 'Código', 'nome_completo' => 'Nome completo', 'cpf' => 'CPF', 'rg' => 'RG',
    'data_nascimento' => 'Nascimento', 'telefone' => 'Telefone', 'whatsapp' => 'WhatsApp',
    'email' => 'E-mail', 'endereco' => 'Endereço', 'numero' => 'Número',
    'complemento' => 'Complemento', 'bairro' => 'Bairro', 'cidade' => 'Cidade',
    'estado' => 'Estado', 'cep' => 'CEP', 'data_consagracao' => 'Consagração',
    'numero_registro_ministerial' => 'Registro ministerial', 'situacao' => 'Situação na época',
    'observacoes' => 'Observações', 'created_at' => 'Cadastrado em',
];
$exibir = static function (string $chave, mixed $valor): string {
    if ($valor === null || $valor === '') {
        return '—';
    }
    if (str_contains($chave, 'data') && preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $valor) === 1) {
        return date('d/m/Y', strtotime(substr((string) $valor, 0, 10)));
    }

    return (string) $valor;
};
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1"><?= e($title) ?></h1>
        <p class="text-muted small mb-0">Registro histórico de <?= e($arquivo['nome_completo']) ?></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php if ($podeRestaurar): ?>
            <a href="<?= e(url('admin/arquivo-morto/' . (int) $arquivo['id'] . '/restaurar')) ?>"
               class="btn btn-sm btn-outline-success">
                <i class="fas fa-rotate-left me-1"></i>Restaurar obreiro
            </a>
        <?php endif; ?>
        <a href="<?= e(url('admin/arquivo-morto')) ?>" class="btn btn-sm btn-ad-outline">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Desligamento</h2>
                <dl class="row mb-0 small">
                    <dt class="col-sm-5">Código</dt>
                    <dd class="col-sm-7"><?= e($arquivo['codigo']) ?></dd>
                    <dt class="col-sm-5">Cargo</dt>
                    <dd class="col-sm-7"><?= e($arquivo['cargo_nome'] ?: '—') ?></dd>
                    <dt class="col-sm-5">Congregação</dt>
                    <dd class="col-sm-7"><?= e($arquivo['congregacao_nome'] ?: '—') ?></dd>
                    <dt class="col-sm-5">Data</dt>
                    <dd class="col-sm-7"><?= e(date('d/m/Y', strtotime((string) $arquivo['data_desligamento']))) ?></dd>
                    <dt class="col-sm-5">Responsável</dt>
                    <dd class="col-sm-7"><?= e($arquivo['responsavel_nome'] ?: '—') ?></dd>
                    <dt class="col-sm-5">Situação</dt>
                    <dd class="col-sm-7">
                        <?php if (!empty($arquivo['restaurado_em'])): ?>
                            <span class="badge bg-success">Restaurado em <?= e(date('d/m/Y H:i', strtotime((string) $arquivo['restaurado_em']))) ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Arquivado</span>
                        <?php endif; ?>
                    </dd>
                    <?php if (!empty($arquivo['restaurado_por_nome'])): ?>
                        <dt class="col-sm-5">Restaurado por</dt>
                        <dd class="col-sm-7"><?= e($arquivo['restaurado_por_nome']) ?></dd>
                    <?php endif; ?>
                </dl>

                <hr>
                <h2 class="h6 text-uppercase text-muted mb-2">Motivo</h2>
                <p class="small mb-3"><?= e($arquivo['motivo']) ?></p>
                <?php if (!empty($arquivo['observacoes'])): ?>
                    <h2 class="h6 text-uppercase text-muted mb-2">Observações</h2>
                    <p class="small mb-0"><?= e($arquivo['observacoes']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Dados preservados</h2>
                <div class="row g-2 small">
                    <?php foreach ($campos as $chave => $rotulo): ?>
                        <div class="col-md-6">
                            <div class="border rounded p-2 h-100">
                                <div class="text-muted"><?= e($rotulo) ?></div>
                                <div><?= e($exibir($chave, $dados[$chave] ?? null)) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card admin-card border-0 shadow-sm mt-3">
    <div class="card-body p-4">
        <h2 class="h6 text-uppercase text-muted mb-3">Histórico ministerial preservado</h2>
        <?php if ($historico === []): ?>
            <p class="text-muted small mb-0">Nenhum evento ministerial registrado antes do arquivamento.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm admin-tabela mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Evento</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $evento): ?>
                            <tr>
                                <td class="text-nowrap small">
                                    <?= e(date('d/m/Y', strtotime((string) ($evento['data_evento'] ?? date('Y-m-d'))))) ?>
                                </td>
                                <td class="small">
                                    <?= e($rotulosTipo[(string) ($evento['tipo'] ?? '')] ?? ($evento['tipo'] ?? '—')) ?>
                                </td>
                                <td class="small"><?= e($evento['descricao'] ?: '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
