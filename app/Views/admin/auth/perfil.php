<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="admin-titulo-pagina mb-1">Meu perfil</h1>
        <p class="text-muted small mb-0">Dados da sua conta administrativa.</p>
    </div>
    <a class="btn btn-sm btn-ad-outline" href="<?= e(url('admin/trocar-senha')) ?>">
        <i class="fas fa-key me-1"></i>Alterar senha
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Dados pessoais</h2>
                <dl class="row admin-lista-dados mb-0">
                    <dt class="col-sm-4">Nome</dt>
                    <dd class="col-sm-8"><?= e($usuario['nome']) ?></dd>

                    <dt class="col-sm-4">E-mail</dt>
                    <dd class="col-sm-8"><?= e($usuario['email']) ?></dd>

                    <dt class="col-sm-4">Telefone</dt>
                    <dd class="col-sm-8"><?= e($usuario['telefone'] ?: 'Não informado') ?></dd>

                    <dt class="col-sm-4">Congregação</dt>
                    <dd class="col-sm-8"><?= e($usuario['congregacao_nome'] ?: 'Não vinculada') ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h2 class="h6 admin-card-titulo mb-3">Acesso</h2>
                <dl class="admin-lista-dados mb-0">
                    <dt>Perfil</dt>
                    <dd><?= e($perfil['nome'] ?? '—') ?></dd>

                    <dt>Nível</dt>
                    <dd><?= (int) ($perfil['nivel'] ?? 0) ?></dd>

                    <dt>Último acesso</dt>
                    <dd><?= e($usuario['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) : '—') ?></dd>

                    <dt>Status</dt>
                    <dd>
                        <span class="badge bg-<?= (int) $usuario['ativo'] === 1 ? 'success' : 'secondary' ?>">
                            <?= (int) $usuario['ativo'] === 1 ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
