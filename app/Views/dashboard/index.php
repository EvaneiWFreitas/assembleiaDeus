<?= $this->extend('layouts/main') ?>
<?= $this->section('conteudo') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fa-solid fa-house me-2 text-primary"></i>Dashboard</h4>
    <span class="text-muted small"><?= date('d/m/Y') ?></span>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-metrica">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Membros</span>
                        <div class="valor"><?= number_format($cards['membros'], 0, ',', '.') ?></div>
                    </div>
                    <i class="fa-solid fa-people-group fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-metrica" style="border-left-color:#198754;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Visitantes</span>
                        <div class="valor"><?= number_format($cards['visitantes'], 0, ',', '.') ?></div>
                    </div>
                    <i class="fa-solid fa-user-plus fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-metrica" style="border-left-color:#6f42c1;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Congregações</span>
                        <div class="valor"><?= number_format($cards['congregacoes'], 0, ',', '.') ?></div>
                    </div>
                    <i class="fa-solid fa-place-of-worship fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <a href="<?= site_url('usuarios') ?>" class="text-decoration-none" title="Gerenciar Usuários do Sistema">
        <div class="card card-metrica h-100" style="border-left-color:#fd7e14; cursor:pointer;" onmouseover="this.style.boxShadow='0 .5rem 1rem rgba(0,0,0,.12)'" onmouseout="this.style.boxShadow=''">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Usuários do Sistema</span>
                        <div class="valor"><?= number_format($cards['usuarios'], 0, ',', '.') ?></div>
                        <span class="small text-primary"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Gerenciar</span>
                    </div>
                    <i class="fa-solid fa-user-gear fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-metrica" style="border-left-color:#20c997;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Dízimos (mês)</span>
                        <div class="valor fs-5">R$ <?= number_format($cards['dizimos_mes'], 2, ',', '.') ?></div>
                    </div>
                    <i class="fa-solid fa-hand-holding-dollar fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card card-metrica" style="border-left-color:#0dcaf0;">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted small">Ofertas (mês)</span>
                        <div class="valor fs-5">R$ <?= number_format($cards['ofertas_mes'], 2, ',', '.') ?></div>
                    </div>
                    <i class="fa-solid fa-gift fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-light border">
    <i class="fa-solid fa-circle-info text-primary me-2"></i>
    <strong>Etapa 1 concluída:</strong> estrutura, autenticação, usuários, perfis e permissões.
    Os módulos de Membros, Visitantes, Agenda e Financeiro serão liberados nas próximas etapas.
</div>

<?= $this->endSection() ?>
