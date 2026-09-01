<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<div class="row g-4">
    <?php if (empty($ministerios)): ?>
        <div class="col-12 text-center text-muted py-5">
            <i class="fa-solid fa-hands-praying fa-2x mb-3 text-azul"></i>
            <p class="mb-0">Em breve teremos a lista completa dos nossos ministérios. Fale conosco para saber como participar!</p>
        </div>
    <?php endif; ?>
    <?php foreach ($ministerios as $m): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card card-valor p-4">
                <div class="icone mb-3"><i class="fa-solid fa-<?= esc($m['icone'] ?? 'hands-praying') ?>"></i></div>
                <h5 class="fw-semibold"><?= esc($m['nome']) ?></h5>
                <?php if (!empty($m['departamento'])): ?>
                    <span class="badge badge-azul mb-2 align-self-start"><?= esc($m['departamento']) ?></span>
                <?php endif; ?>
                <p class="text-muted mb-0"><?= esc($m['descricao'] ?: 'Conheça este ministério e descubra como você pode servir.') ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="text-center mt-5">
    <a href="<?= site_url('login') ?>" class="btn btn-azul btn-lg"><i class="fa-solid fa-user-pen me-2"></i>Quero servir em um ministério</a>
</div>

<?= $this->endSection() ?>
