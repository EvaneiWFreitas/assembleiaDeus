<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<?php if (session()->getFlashdata('erro_login')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('erro_login')) ?></div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="icone mx-auto mb-3" style="width:72px;height:72px;"><i class="fa-solid fa-graduation-cap"></i></div>
                <h5 class="fw-bold mb-1">Área do Aluno</h5>
                <p class="text-muted small mb-0">Acesse com seu e-mail e senha para assistir às suas videoaulas.</p>
            </div>
            <form method="post" action="<?= site_url('site/curso/entrar') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small">E-mail cadastrado *</label>
                    <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small">Senha *</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>
                <button class="btn btn-azul w-100"><i class="fa-solid fa-right-to-bracket me-1"></i>Entrar</button>
            </form>
            <p class="small text-muted mt-4 mb-0 text-center">
                Ainda não tem cadastro? <a href="<?= site_url('site/cursos') ?>" class="text-azul">Matricule-se em um curso</a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
