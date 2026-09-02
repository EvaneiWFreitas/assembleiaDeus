<?= $this->extend('site/layout') ?>
<?= $this->section('conteudo') ?>

<?php if (session()->getFlashdata('sucesso_aluno')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('sucesso_aluno')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('erro_cad')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('erro_cad')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('info_aluno')): ?>
    <div class="alert alert-info"><?= esc(session()->getFlashdata('info_aluno')) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <h3 class="fw-bold mb-3"><?= esc($curso['nome']) ?></h3>
        <?php if (! empty($curso['descricao'])): ?>
            <p class="text-muted"><?= esc($curso['descricao']) ?></p>
        <?php endif; ?>

        <div class="row g-3 my-3">
            <div class="col-md-6">
                <div class="card card-valor p-3">
                    <div class="small text-muted mb-1"><i class="fa-solid fa-user-tie me-1 text-azul"></i>Professor</div>
                    <span class="fw-semibold"><?= esc($curso['professor'] ?? 'Corpo docente') ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-valor p-3">
                    <div class="small text-muted mb-1"><i class="fa-solid fa-video me-1 text-azul"></i>Aulas online</div>
                    <span class="fw-semibold"><?= (int) count($aulas) ?> videoaula(s)</span>
                </div>
            </div>
        </div>

        <?php if (! empty($aulas)): ?>
        <h6 class="fw-semibold mt-4 mb-2">Conteúdo do curso</h6>
        <div class="list-group">
            <?php foreach ($aulas as $a): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <span><?= esc($a['tema']) ?></span>
                        <?php if (! empty($a['video_url'])): ?>
                            <i class="fa-solid fa-video text-azul" title="Videoaula"></i>
                        <?php elseif (! empty($a['conteudo'])): ?>
                            <i class="fa-solid fa-file-lines text-muted" title="Orientação de texto"></i>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-semibold mb-3"><i class="fa-solid fa-user-plus me-2 text-azul"></i>Cadastre-se neste curso</h5>
            <p class="small text-muted mb-3">Crie seu acesso e estude online pelas videoaulas. Cada aula possui orientação de texto.</p>
            <form method="post" action="<?= site_url('site/cursos/' . $curso['id'] . '/cadastrar') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label small">Nome completo *</label>
                    <input type="text" name="nome" class="form-control" value="<?= esc(old('nome')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small">E-mail *</label>
                    <input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Telefone</label>
                    <input type="text" name="telefone" class="form-control" value="<?= esc(old('telefone')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small">CPF</label>
                    <input type="text" name="cpf" class="form-control" value="<?= esc(old('cpf')) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label small">Senha de acesso (mín. 6 caracteres) *</label>
                    <input type="password" name="senha" class="form-control" minlength="6" required>
                </div>
                <button class="btn btn-azul w-100"><i class="fa-solid fa-graduation-cap me-1"></i>Matricular-me agora</button>
            </form>
            <p class="small text-muted mt-3 mb-0 text-center">
                Já é aluno? <a href="<?= site_url('site/curso/entrar') ?>" class="text-azul">Entrar na Área do Aluno</a>
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
