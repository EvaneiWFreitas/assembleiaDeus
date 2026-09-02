<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<?php if (session()->getFlashdata('info_aluno')): ?>
    <div class="alert alert-info"><?= esc(session()->getFlashdata('info_aluno')) ?></div>
<?php endif; ?>

<div class="row g-4 align-items-center mb-5">
    <div class="col-lg-6">
        <p class="text-muted mb-3">Estude de onde estiver: cursos bíblicos e de formação com <strong>videoaulas</strong> e <strong>orientação de texto</strong>, acompanhados pelo nosso corpo de professores.</p>
        <ul class="list-unstyled">
            <li class="mb-2"><i class="fa-solid fa-circle-check text-azul me-2"></i>Cadastre-se online e comece hoje</li>
            <li class="mb-2"><i class="fa-solid fa-circle-check text-azul me-2"></i>Videoaulas + material de apoio em texto</li>
            <li class="mb-2"><i class="fa-solid fa-circle-check text-azul me-2"></i>Acesso pela Área do Aluno com seu login e senha</li>
        </ul>
        <a href="#lista-cursos" class="btn btn-azul btn-lg mt-2"><i class="fa-solid fa-graduation-cap me-2"></i>Ver cursos disponíveis</a>
    </div>
    <div class="col-lg-6">
        <div class="row g-3 text-center">
            <div class="col-6"><div class="card card-valor p-4"><div class="icone mb-3 mx-auto"><i class="fa-solid fa-video"></i></div><h3 class="fw-bold text-azul">Videoaulas</h3><p class="text-muted small mb-0">Assista quando quiser</p></div></div>
            <div class="col-6"><div class="card card-valor p-4"><div class="icone mb-3 mx-auto"><i class="fa-solid fa-file-lines"></i></div><h3 class="fw-bold text-azul">Textos</h3><p class="text-muted small mb-0">Orientação e material</p></div></div>
        </div>
    </div>
</div>

<div id="lista-cursos">
    <?php if (empty($cursos)): ?>
        <div class="alert alert-light border text-center py-5">
            <i class="fa-solid fa-book-open fa-2x text-azul mb-3"></i>
            <p class="mb-0 text-muted">Nenhum curso disponível no momento. Volte em breve!</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($cursos as $c): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-valor p-4 d-flex flex-column">
                    <div class="icone mb-3"><i class="fa-solid fa-book-bible"></i></div>
                    <h5 class="fw-semibold mb-1"><?= esc($c['nome']) ?></h5>
                    <p class="text-muted small mb-3"><?= esc($c['descricao'] ?? '') ?></p>
                    <div class="d-flex gap-3 text-muted small mb-3">
                        <span><i class="fa-solid fa-video me-1 text-azul"></i><?= (int) $c['total_aulas'] ?> aulas</span>
                        <span><i class="fa-solid fa-users me-1 text-azul"></i><?= (int) $c['total_alunos'] ?> alunos</span>
                    </div>
                    <a href="<?= site_url('site/cursos/' . $c['id']) ?>" class="btn btn-azul mt-auto">Inscrever-se</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
