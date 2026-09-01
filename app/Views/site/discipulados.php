<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<div class="row g-4 align-items-center mb-5">
    <div class="col-lg-6">
        <p class="text-muted mb-3">O discipulado é o caminho que Jesus deixou para formar vidas: um discipulador caminha junto com você, ensinando a Palavra e acompanhando o seu crescimento na fé.</p>
        <ul class="list-unstyled">
            <li class="mb-2"><i class="fa-solid fa-circle-check text-azul me-2"></i>Acompanhamento pessoal e semanal</li>
            <li class="mb-2"><i class="fa-solid fa-circle-check text-azul me-2"></i>Ensino bíblico estruturado por etapas</li>
            <li class="mb-2"><i class="fa-solid fa-circle-check text-azul me-2"></i>Aberto para novos convertidos e membros</li>
        </ul>
        <a href="<?= site_url('login') ?>" class="btn btn-azul btn-lg mt-2"><i class="fa-solid fa-hands-holding-circle me-2"></i>Quero ser discipulado</a>
    </div>
    <div class="col-lg-6">
        <div class="row g-3">
            <?php if (empty($status)): ?>
                <div class="col-12 text-center text-muted py-4">Nenhuma turma em andamento publicada no momento.</div>
            <?php endif; ?>
            <?php foreach ($status as $st => $qtd): ?>
                <div class="col-md-6">
                    <div class="card card-valor p-4 text-center">
                        <div class="icone mb-3 mx-auto"><i class="fa-solid fa-user-graduate"></i></div>
                        <h3 class="fw-bold text-azul mb-1"><?= (int) $qtd ?></h3>
                        <p class="text-muted small mb-0">Discipulados em <?= esc($st) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if (!empty($emAndamento)): ?>
<h3 class="fw-bold mb-3">Discipuladores ativos</h3>
<div class="row g-4">
    <?php foreach ($emAndamento as $d): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card card-valor p-4">
                <div class="icone mb-3"><i class="fa-solid fa-hands-holding-circle"></i></div>
                <h5 class="fw-semibold">Discipulado #<?= (int) $d['id'] ?></h5>
                <p class="text-muted mb-1"><i class="fa-solid fa-user-tie me-2 text-azul"></i>Discipulador: <?= esc($d['discipulador'] ?? '-') ?></p>
                <p class="text-muted mb-0"><i class="fa-solid fa-calendar me-2 text-azul"></i>Início: <?= esc(date('d/m/Y', strtotime($d['data_inicio']))) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
