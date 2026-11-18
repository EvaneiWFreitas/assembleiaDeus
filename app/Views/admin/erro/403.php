<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">
        <div class="card admin-card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <div class="admin-erro-icone text-warning mb-3"><i class="fas fa-lock"></i></div>
                <h1 class="h4 mb-2">Acesso negado</h1>
                <p class="text-muted small mb-3">
                    Seu perfil não possui permissão para acessar este recurso.
                </p>
                <?php if (!empty($permissao)): ?>
                    <p class="small mb-4">
                        Permissão necessária:
                        <code><?= e($permissao) ?></code>
                    </p>
                <?php endif; ?>
                <a class="btn btn-ad-primary" href="<?= e(url('admin/dashboard')) ?>">
                    <i class="fas fa-gauge-high me-2"></i>Voltar ao dashboard
                </a>
            </div>
        </div>
    </div>
</div>
